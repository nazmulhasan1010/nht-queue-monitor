<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use JsonException;
use NHT\QueueMonitor\Services\EventLogger;
use NHT\QueueMonitor\Services\PendingJobService;

class PendingJobController extends Controller
{
    /**
     * @param Request $request
     * @param PendingJobService $svc
     * @return View
     */
    public function index(Request $request, PendingJobService $svc): View
    {
        $filters = [
            'q' => $request->get('q'),
            'queue' => $request->get('queue'),
        ];

        return view('queue-monitor::pending-jobs', [
            'jobs' => $svc->filteredPaginate($filters),
            'filters' => $filters,
            'queues' => $svc->distinctQueues(),
        ]);
    }

    /**
     * @param int|string $id
     * @param PendingJobService $svc
     * @return View
     * @throws JsonException
     */
    public function show(int|string $id, PendingJobService $svc): View
    {
        $job = $svc->findOrFail($id);
        $payload = $svc->decodePayload($job);

        return view('queue-monitor::pending-job-detail', [
            'job' => $job,
            'payload' => $payload,
            'jobName' => $svc->jobName($job),
            'uuid' => $payload['uuid'] ?? 'N/A',
            'attempts' => $job->attempts,
        ]);
    }

    /**
     * @param int|string $id
     * @param PendingJobService $svc
     * @param EventLogger $logger
     * @return RedirectResponse
     * @throws JsonException
     */
    public function run(int|string $id, PendingJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_run', true), 403);

        $job = $svc->find($id);
        $jobName = $job ? $svc->jobName($job) : 'Unknown';
        
        try {
            $svc->run($id);
            
            $logger->log('pending_job_run_manually', [
                'job_id' => (string)$id,
                'job_name' => $jobName,
            ]);

            return redirect()->route('queue-monitor.pending.index')->with('queue_monitor_success', "Job '$jobName' executed successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => "Failed to run job: " . $e->getMessage()]);
        }
    }

    /**
     * @param int|string $id
     * @param PendingJobService $svc
     * @param EventLogger $logger
     * @return RedirectResponse
     * @throws JsonException
     */
    public function destroy(int|string $id, PendingJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_pending_delete', true), 403);

        $job = $svc->find($id);
        $jobName = $job ? $svc->jobName($job) : 'Unknown';

        $svc->delete($id);

        $logger->log('pending_job_deleted', [
            'job_id' => (string)$id,
            'job_name' => $jobName,
        ]);

        return redirect()->route('queue-monitor.pending.index')->with('queue_monitor_success', 'Pending job deleted successfully.');
    }

    /**
     * @param PendingJobService $svc
     * @param EventLogger $logger
     * @return RedirectResponse
     */
    public function clear(PendingJobService $svc, EventLogger $logger): RedirectResponse
    {
        abort_unless(config('queue-monitor.allow_clear', true), 403);

        $svc->clear();
        $logger->log('all_pending_jobs_cleared');

        return redirect()->route('queue-monitor.pending.index')->with('queue_monitor_success', 'All pending jobs cleared successfully.');
    }
}
