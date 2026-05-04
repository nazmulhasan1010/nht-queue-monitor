<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use NHT\QueueMonitor\Services\QueueHealthService;

class HealthController extends Controller
{
    /**
     * @param QueueHealthService $service
     * @return View
     */
    public function index(QueueHealthService $service): View
    {
        return view('queue-monitor::health', [
            'summary' => $service->summary(),
            'queues' => $service->queueDistribution(),
            'connections' => $service->connectionDistribution(),
            'heatmap' => $service->hourlyHeatmap(),
        ]);
    }

    /**
     * @param QueueHealthService $service
     * @return mixed
     */
    public function json(QueueHealthService $service)
    {
        return response()->json([
            'summary' => $service->summary(),
            'queues' => $service->queueDistribution(),
            'connections' => $service->connectionDistribution(),
            'heatmap' => $service->hourlyHeatmap(),
        ]);
    }
}
