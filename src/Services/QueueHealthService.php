<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Support\Facades\DB;

class QueueHealthService
{
    /**
     * @return string
     */
    private function table(): string
    {
        return config('queue.failed.table', 'failed_jobs');
    }

    /**
     * @return array
     */
    public function summary(): array
    {
        $total = DB::table($this->table())->count();
        $last24h = DB::table($this->table())->where('failed_at', '>=', now()->subDay())->count();
        $lastHour = DB::table($this->table())->where('failed_at', '>=', now()->subHour())->count();

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
            'latest_failed_at' => DB::table($this->table())->max('failed_at'),
        ];
    }

    /**
     * @return mixed
     */
    public function queueDistribution(): mixed
    {
        return DB::table($this->table())->selectRaw('queue, COUNT(*) as total')->groupBy('queue')->orderByDesc('total')->limit(10)->get();
    }

    /**
     * @return mixed
     */
    public function connectionDistribution(): mixed
    {
        return DB::table($this->table())->selectRaw('connection, COUNT(*) as total')->groupBy('connection')->orderByDesc('total')->limit(10)->get();
    }

    /**
     * @return mixed
     */
    public function hourlyHeatmap(): mixed
    {
        return DB::table($this->table())->selectRaw('HOUR(failed_at) as hour, COUNT(*) as total')->where('failed_at', '>=', now()->subDays(7))->groupBy('hour')->orderBy('hour')->get();
    }

    /**
     * @param int $score
     * @return string
     */
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
