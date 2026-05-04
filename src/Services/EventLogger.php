<?php

namespace NHT\QueueMonitor\Services;

use NHT\QueueMonitor\Models\QueueMonitorEvent;

class EventLogger
{
    /**
     * @param string $eventType
     * @param array $data
     * @return void
     */
    public function log(string $eventType, array $data = []): void
    {
        if (!config('queue-monitor.audit.enabled', true)) {
            return;
        }

        QueueMonitorEvent::create([
            'event_type' => $eventType,
            'job_id' => $data['job_id'] ?? null,
            'queue' => $data['queue'] ?? null,
            'connection' => $data['connection'] ?? null,
            'job_name' => $data['job_name'] ?? null,
            'performed_by' => $data['performed_by'] ?? optional(auth()->user())->email ?? optional(auth()->user())->id,
            'meta' => $data['meta'] ?? null,
        ]);
    }
}
