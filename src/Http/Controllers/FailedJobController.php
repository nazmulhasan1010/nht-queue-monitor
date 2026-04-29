<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NHT\QueueMonitor\Actions\RetryAllFailedJobs;
use NHT\QueueMonitor\Actions\RetryFailedJob;
use NHT\QueueMonitor\Services\EventLogger;
use NHT\QueueMonitor\Services\FailedJobService;
use NHT\QueueMonitor\Services\QueueMonitorNotifier;

class FailedJobController extends Controller
{
    public function index(Request $request, FailedJobService $svc)
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

    public function show(int|string $id, FailedJobService $failedJobService)
    {
        $job = $failedJobService->findOrFail($id);

        return view('queue-monitor::job-detail', [
            'job' => $job,
            'payload' => $failedJobService->decodePayload($job),
            'jobName' => $failedJobService->jobName($job),
            'uuid' => method_exists($failedJobService, 'uuid') ? $failedJobService->uuid($job) : ($job->uuid ?? 'N/A'),
            'attempts' => method_exists($failedJobService, 'attempts') ? $failedJobService->attempts($job) : 'N/A',
            'exceptionPreview' => $failedJobService->exceptionPreview($job),
        ]);
    }

    public function retry(int|string $id, RetryFailedJob $retryFailedJob, FailedJobService $svc, EventLogger $logger, QueueMonitorNotifier $notifier): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_retry', true), 403);

        $job = $svc->find($id);
        $retryFailedJob->execute($id);

        $logger->log('job_retried', [
            'job_id' => (string) $id,
            'queue' => $job->queue ?? null,
            'connection' => $job->connection ?? null,
            'job_name' => $job ? $svc->jobName($job) : null,
        ]);

        $notifier->notify('Queue Pulse: Job Retried', "Failed job #{$id} retry command executed.", ['job_id' => $id]);

        return back()->with('queue_pulse_success', 'Failed job retry command executed.');
    }

    public function retryAll(RetryAllFailedJobs $retryAllFailedJobs, EventLogger $logger, QueueMonitorNotifier $notifier): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_retry', true), 403);

        $retryAllFailedJobs->execute();

        $logger->log('all_jobs_retried');
        $notifier->notify('Queue Pulse: Retry All', 'Retry all failed jobs command executed.');

        return back()->with('queue_pulse_success', 'Retry all command executed.');
    }

    public function destroy(int|string $id, FailedJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_delete', true), 403);

        $job = $svc->find($id);

        $logger->log('job_deleted', [
            'job_id' => (string) $id,
            'queue' => $job->queue ?? null,
            'connection' => $job->connection ?? null,
            'job_name' => $job ? $svc->jobName($job) : null,
        ]);

        $svc->delete($id);

        return redirect()->route('queue-monitor.failed.index')->with('queue_pulse_success', 'Failed job deleted successfully.');
    }

    public function bulkDestroy(Request $request, FailedJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_bulk_delete', true), 403);

        $ids = (array) $request->input('ids', []);
        $deleted = $svc->bulkDelete($ids);

        $logger->log('bulk_jobs_deleted', [
            'meta' => ['ids' => $ids, 'deleted' => $deleted],
        ]);

        return back()->with('queue_pulse_success', "{$deleted} failed job(s) deleted successfully.");
    }

    public function clear(FailedJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_clear', true), 403);

        $svc->clear();
        $logger->log('all_jobs_cleared');

        return redirect()->route('queue-monitor.failed.index')->with('queue_pulse_success', 'All failed jobs cleared successfully.');
    }
}
