<?php

namespace NHT\QueueMonitor\Listeners;

use Illuminate\Queue\Events\JobFailed;
use NHT\QueueMonitor\Events\QueueMonitorJobRecorded;
use NHT\QueueMonitor\Models\QueueMonitorJob;
use NHT\QueueMonitor\Services\FailureInsightService;
use NHT\QueueMonitor\Support\JobPayload;

use NHT\QueueMonitor\Support\JobTimer;

class RecordFailedJob
{
    /**
     * @param JobFailed $event
     * @return void
     */
    public function handle(JobFailed $event): void
    {
        if (! config('queue-monitor.tracking.track_failed_jobs_events', true)) {
            return;
        }

        $payload = JobPayload::fromRaw($event->job->getRawBody());
        $uuid = JobPayload::uuid($payload);
        $exception = (string) $event->exception;

        $job = QueueMonitorJob::query()->create([
            'uuid' => $uuid,
            'connection' => $event->connectionName,
            'queue' => $event->job->getQueue(),
            'node_name' => config('queue-monitor.node.name'),
            'tenant_id' => $this->tenantId(),
            'job_name' => JobPayload::displayName($payload),
            'status' => 'failed',
            'attempts' => method_exists($event->job, 'attempts') ? $event->job->attempts() : null,
            'duration_ms' => $uuid ? JobTimer::stop($uuid) : null,
            'exception' => config('queue-monitor.tracking.store_exception', true) ? $exception : null,
            'insight' => app(FailureInsightService::class)->insight($exception),
            'payload' => config('queue-monitor.tracking.store_payload', false) ? $payload : null,
            'tags' => $payload['tags'] ?? null,
            'finished_at' => now(),
        ]);

        if (config('queue-monitor.broadcasting.enabled', false)) {
            event(new QueueMonitorJobRecorded($job->toArray()));
        }
    }

    /**
     * @return string|null
     */
    protected function tenantId(): ?string
    {
        $resolver = config('queue-monitor.tenant.resolver');

        return is_callable($resolver) ? (string) $resolver() : null;
    }
}
