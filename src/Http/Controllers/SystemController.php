<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use NHT\QueueMonitor\Services\RedisQueueMetricsService;

class SystemController extends Controller
{
    public function index(RedisQueueMetricsService $redisMetrics)
    {
        $queues = DB::table('failed_jobs')->distinct()->pluck('queue')->filter()->values()->all();

        if (empty($queues)) {
            $queues = ['default'];
        }

        return view('queue-monitor::system', [
            'node' => config('queue-monitor.node'),
            'broadcasting' => config('queue-monitor.broadcasting'),
            'tenant' => config('queue-monitor.tenant'),
            'redisMetrics' => $redisMetrics->metrics($queues),
        ]);
    }
}
