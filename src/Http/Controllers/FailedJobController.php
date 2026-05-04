<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use JsonException;
use NHT\QueueMonitor\Actions\RetryAllFailedJobs;
use NHT\QueueMonitor\Actions\RetryFailedJob;
use NHT\QueueMonitor\Services\AIService;
use NHT\QueueMonitor\Services\EventLogger;
use NHT\QueueMonitor\Services\FailedJobService;
use NHT\QueueMonitor\Services\QueueMonitorNotifier;

class FailedJobController extends Controller
{
    /**
     * @param Request $request
     * @param FailedJobService $svc
     * @return View
     */
    public function index(Request $request, FailedJobService $svc): View
    {
        $filters = [
            'q' => $request->get('q'),
            'queue' => $request->get('queue'),
            'connection' => $request->get('connection'),
            'from' => $request->get('from'),
            'to' => $request->get('to'),
        ];

        return view('queue-monitor::failed-jobs', [
            'jobs' => method_exists($svc, 'filteredPaginate') ? $svc->filteredPaginate($filters) : $svc->paginate(),
            'filters' => $filters,
            'queues' => method_exists($svc, 'distinctQueues') ? $svc->distinctQueues() : collect(),
            'connections' => method_exists($svc, 'distinctConnections') ? $svc->distinctConnections() : collect(),
        ]);
    }

    /**
     * @param int|string $id
     * @param FailedJobService $svc
     * @param AIService $ai
     * @return JsonResponse
     * @throws JsonException
     */
    public function analyze(int|string $id, FailedJobService $svc, AIService $ai): JsonResponse
    {
        abort_unless(config('queue-monitor.ai.enabled', false), 403);

        $job = $svc->findOrFail($id);
        $analysis = $ai->analyzeFailure(
            $svc->jobName($job),
            $job->exception,
            $svc->decodePayload($job)
        );

        return response()->json([
            'analysis' => $analysis,
        ]);
    }

    /**
     * @param int|string $id
     * @param FailedJobService $failedJobService
     * @return View
     * @throws JsonException
     */
    public function show(int|string $id, FailedJobService $failedJobService): View
    {
        $job = $failedJobService->findOrFail($id);
        $uuid = method_exists($failedJobService, 'uuid') ? $failedJobService->uuid($job) : ($job->uuid ?? null);
        
        $monitorJob = \NHT\QueueMonitor\Models\QueueMonitorJob::where('uuid', $uuid)->first();
        $batchJobs = collect();
        
        if ($monitorJob && $monitorJob->batch_id) {
            $batchJobs = \NHT\QueueMonitor\Models\QueueMonitorJob::where('batch_id', $monitorJob->batch_id)
                ->orderBy('created_at')
                ->get();
        }

        return view('queue-monitor::job-detail', [
            'job' => $job,
            'payload' => $failedJobService->decodePayload($job),
            'jobName' => $failedJobService->jobName($job),
            'uuid' => $uuid ?? 'N/A',
            'attempts' => method_exists($failedJobService, 'attempts') ? $failedJobService->attempts($job) : 'N/A',
            'exceptionPreview' => $failedJobService->exceptionPreview($job),
            'batchJobs' => $batchJobs,
        ]);
    }

    /**
     * @param int|string $id
     * @param RetryFailedJob $retryFailedJob
     * @param FailedJobService $svc
     * @param EventLogger $logger
     * @param QueueMonitorNotifier $notifier
     * @return RedirectResponse
     * @throws JsonException
     */
    public function retry(int|string $id, RetryFailedJob $retryFailedJob, FailedJobService $svc, EventLogger $logger, QueueMonitorNotifier $notifier): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_retry', true), 403);

        $job = $svc->find($id);
        $retryFailedJob->execute($id);

        $logger->log('job_retried', [
            'job_id' => (string)$id,
            'queue' => $job->queue ?? null,
            'connection' => $job->connection ?? null,
            'job_name' => $job ? $svc->jobName($job) : null,
        ]);

        $notifier->notify('Queue Pulse: Job Retried', "Failed job #$id retry command executed.", ['job_id' => $id]);

        return back()->with('queue_monitor_success', 'Failed job retry command executed.');
    }

    /**
     * @param int|string $id
     * @param Request $request
     * @param RetryFailedJob $retryFailedJob
     * @param FailedJobService $svc
     * @param EventLogger $logger
     * @return RedirectResponse
     */
    public function retryWithData(int|string $id, Request $request, RetryFailedJob $retryFailedJob, FailedJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_retry', true), 403);

        $newPayload = $request->input('payload');
        
        // Validate JSON
        json_decode($newPayload);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['payload' => 'Invalid JSON payload.']);
        }

        // Update the failed_jobs table directly
        \Illuminate\Support\Facades\DB::table(config('queue.failed.table', 'failed_jobs'))
            ->where('id', $id)
            ->update(['payload' => $newPayload]);

        $job = $svc->find($id);
        $retryFailedJob->execute($id);

        $logger->log('job_retried_with_edit', [
            'job_id' => (string)$id,
            'job_name' => $job ? $svc->jobName($job) : null,
        ]);

        return redirect()->route('queue-monitor.failed.index')->with('queue_monitor_success', 'Job payload updated and retry triggered.');
    }

    /**
     * @param RetryAllFailedJobs $retryAllFailedJobs
     * @param EventLogger $logger
     * @param QueueMonitorNotifier $notifier
     * @return RedirectResponse
     * @throws JsonException
     */
    public function retryAll(RetryAllFailedJobs $retryAllFailedJobs, EventLogger $logger, QueueMonitorNotifier $notifier): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_retry', true), 403);

        $retryAllFailedJobs->execute();

        $logger->log('all_jobs_retried');
        $notifier->notify('Queue Pulse: Retry All', 'Retry all failed jobs command executed.');

        return back()->with('queue_monitor_success', 'Retry all command executed.');
    }

    /**
     * @param int|string $id
     * @param FailedJobService $svc
     * @param EventLogger $logger
     * @return RedirectResponse
     * @throws JsonException
     */
    public function destroy(int|string $id, FailedJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_delete', true), 403);

        $job = $svc->find($id);

        $logger->log('job_deleted', [
            'job_id' => (string)$id,
            'queue' => $job->queue ?? null,
            'connection' => $job->connection ?? null,
            'job_name' => $job ? $svc->jobName($job) : null,
        ]);

        $svc->delete($id);

        return redirect()->route('queue-monitor.failed.index')->with('queue_monitor_success', 'Failed job deleted successfully.');
    }

    /**
     * @param Request $request
     * @param FailedJobService $svc
     * @param EventLogger $logger
     * @return RedirectResponse
     */
    public function bulkDestroy(Request $request, FailedJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_bulk_delete', true), 403);

        $ids = (array)$request->input('ids', []);
        $deleted = $svc->bulkDelete($ids);

        $logger->log('bulk_jobs_deleted', [
            'meta' => ['ids' => $ids, 'deleted' => $deleted],
        ]);

        return back()->with('queue_monitor_success', "$deleted failed job(s) deleted successfully.");
    }

    /**
     * @param FailedJobService $svc
     * @param EventLogger $logger
     * @return RedirectResponse
     */
    public function clear(FailedJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_clear', true), 403);

        $svc->clear();
        $logger->log('all_jobs_cleared');

        return redirect()->route('queue-monitor.failed.index')->with('queue_monitor_success', 'All failed jobs cleared successfully.');
    }
}
