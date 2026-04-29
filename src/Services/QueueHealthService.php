<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Support\Facades\DB;

class QueueHealthService
{
    public function summary(): array
    {
        $total = DB::table('failed_jobs')->count();
        $last24h = DB::table('failed_jobs')->where('failed_at', '>=', now()->subDay())->count();
        $lastHour = DB::table('failed_jobs')->where('failed_at', '>=', now()->subHour())->count();

        $score = 100;

        if ($last24h >= config('queue-monitor.health.critical_failed_jobs_24h', 50)) {
            $score -= 50;
        } elseif ($last24h >= config('queue-monitor.health.warning_failed_jobs_24h', 10)) {
            $score -= 25;
        }

        if ($total >= config('queue-monitor.health.critical_failed_jobs_total', 500)) {
            $score -= 30;
        } elseif ($total >= config('queue-monitor.health.warning_failed_jobs_total', 100)) {
            $score -= 15;
        }

        if ($lastHour > 0) {
            $score -= min(20, $lastHour * 2);
        }

        $score = max(0, $score);

        return [
            'score' => $score,
            'status' => $this->statusFromScore($score),
            'total_failed' => $total,
            'failed_last_24h' => $last24h,
            'failed_last_hour' => $lastHour,
            'latest_failed_at' => DB::table('failed_jobs')->max('failed_at'),
        ];
    }

    public function queueDistribution()
    {
        return DB::table('failed_jobs')
            ->selectRaw('queue, COUNT(*) as total')
            ->groupBy('queue')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    public function connectionDistribution()
    {
        return DB::table('failed_jobs')
            ->selectRaw('connection, COUNT(*) as total')
            ->groupBy('connection')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    public function hourlyHeatmap()
    {
        return DB::table('failed_jobs')
            ->selectRaw('HOUR(failed_at) as hour, COUNT(*) as total')
            ->where('failed_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    protected function statusFromScore(int $score): string
    {
        if ($score >= 85) {
            return 'healthy';
        }

        if ($score >= 60) {
            return 'warning';
        }

        return 'critical';
    }
}
