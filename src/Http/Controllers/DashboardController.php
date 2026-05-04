<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use NHT\QueueMonitor\Services\QueueStatsService;

class DashboardController extends Controller
{
    /**
     * @param QueueStatsService $statsService
     * @return View
     */
    public function index(QueueStatsService $statsService): View
    {
        return view('queue-monitor::dashboard', [
            'stats' => $statsService->summary(),
            'latestFailedJobs' => $statsService->latestFailedJobs(),
        ]);
    }
}
