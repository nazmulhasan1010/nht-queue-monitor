<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;
use NHT\QueueMonitor\Services\QueueHealthService;

class HealthController extends Controller
{
    public function index(QueueHealthService $service)
    {
        return view('queue-monitor::health', [
            'summary' => $service->summary(),
            'queues' => $service->queueDistribution(),
            'connections' => $service->connectionDistribution(),
            'heatmap' => $service->hourlyHeatmap(),
        ]);
    }

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
