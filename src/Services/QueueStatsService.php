<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QueueStatsService
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
        $today = Carbon::today();

        return [
            'total_failed' => DB::table($this->table())->count(),
            'failed_today' => DB::table($this->table())->whereDate('failed_at', $today)->count(),
            'failed_this_week' => DB::table($this->table())->where('failed_at', '>=', now()->startOfWeek())->count(),
            'latest_failed_at' => DB::table($this->table())->max('failed_at'),
        ];
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function latestFailedJobs(int $limit = 5): mixed
    {
        return DB::table($this->table())->orderByDesc('failed_at')->limit($limit)->get();
    }
}
