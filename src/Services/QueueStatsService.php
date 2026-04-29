<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QueueStatsService
{
    public function summary(): array
    {
        $today = Carbon::today();

        return [
            'total_failed' => DB::table('failed_jobs')->count(),
            'failed_today' => DB::table('failed_jobs')->whereDate('failed_at', $today)->count(),
            'failed_this_week' => DB::table('failed_jobs')->where('failed_at', '>=', now()->startOfWeek())->count(),
            'latest_failed_at' => DB::table('failed_jobs')->max('failed_at'),
        ];
    }

    public function latestFailedJobs(int $limit = 5)
    {
        return DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->limit($limit)
            ->get();
    }
}
