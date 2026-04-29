<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use NHT\QueueMonitor\Models\QueueMonitorEvent;

class EventController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        return view('queue-monitor::events', [
            'events' => QueueMonitorEvent::query()->latest()->paginate(30),
        ]);
    }
}
