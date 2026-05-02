<?php

namespace NHT\QueueMonitor\Listeners;

use Illuminate\Queue\Events\JobProcessed;
use NHT\QueueMonitor\Events\QueueMonitorJobRecorded;
use NHT\QueueMonitor\Models\QueueMonitorJob;
use NHT\QueueMonitor\Support\JobPayload;

use NHT\QueueMonitor\Support\JobTimer;

class RecordProcessedJob
{
    /**
     * @param JobProcessed $event
     * @return void
     */
    public function handle(JobProcessed $event): void
    {
        if (! config('queue-monitor.tracking.track_successful_jobs', false)) {
            return;
        }

        $payload = JobPayload::fromRaw($event->job->getRawBody());
        $uuid = JobPayload::uuid($payload);

        $job = QueueMonitorJob::query()->create([
            'uuid' => $uuid,
            'connection' => $event->connectionName,
            'queue' => $event->job->getQueue(),
            'node_name' => config('queue-monitor.node.name'),
            'tenant_id' => $this->tenantId(),
            'job_name' => JobPayload::displayName($payload),
            'status' => 'processed',
            'attempts' => method_exists($event->job, 'attempts') ? $event->job->attempts() : null,
            'duration_ms' => $uuid ? JobTimer::stop($uuid) : null,
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
