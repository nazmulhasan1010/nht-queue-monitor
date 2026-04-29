<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;
use NHT\QueueMonitor\Services\QueueStatsService;

class DashboardController extends Controller
{
    public function index(QueueStatsService $statsService)
    {
        return view('queue-monitor::dashboard', [
            'stats' => $statsService->summary(),
            'latestFailedJobs' => $statsService->latestFailedJobs(),
        ]);
    }
}
