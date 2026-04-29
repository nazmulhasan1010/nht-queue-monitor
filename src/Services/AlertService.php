
<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Support\Facades\DB;
use NHT\QueueMonitor\Models\QueueMonitorAlert;

class AlertService
{
    public function check(): array
    {
        if (! config('queue-monitor.alerts.enabled', true)) {
            return [];
        }

        $created = [];

        $failed24h = DB::table('failed_jobs')->where('failed_at', '>=', now()->subDay())->count();
        $failed1h = DB::table('failed_jobs')->where('failed_at', '>=', now()->subHour())->count();

        if ($failed24h >= (int) config('queue-monitor.alerts.failed_jobs_24h', 20)) {
            $created[] = $this->createOrUpdate(
                'failed_jobs_24h',
                'critical',
                'High failed jobs in last 24 hours',
                "{$failed24h} failed jobs recorded in the last 24 hours.",
                ['count' => $failed24h]
            );
        }

        if ($failed1h >= (int) config('queue-monitor.alerts.failed_jobs_1h', 5)) {
            $created[] = $this->createOrUpdate(
                'failed_jobs_1h',
                'warning',
                'High failed jobs in last hour',
                "{$failed1h} failed jobs recorded in the last hour.",
                ['count' => $failed1h]
            );
        }

        return $created;
    }

    protected function createOrUpdate(string $key, string $level, string $title, string $message, array $meta): QueueMonitorAlert
    {
        return QueueMonitorAlert::query()->updateOrCreate(
            ['alert_key' => $key, 'resolved_at' => null],
            [
                'level' => $level,
                'title' => $title,
                'message' => $message,
                'meta' => $meta,
            ]
        );
    }
}
