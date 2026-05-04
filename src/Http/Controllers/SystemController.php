<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use NHT\QueueMonitor\Services\RedisQueueMetricsService;

class SystemController extends Controller
{
    /**
     * @param RedisQueueMetricsService $redisMetrics
     * @return View
     */
    public function index(RedisQueueMetricsService $redisMetrics): View
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
