<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;
use NHT\QueueMonitor\Models\QueueMonitorEvent;

class EventController extends Controller
{
    public function index()
    {
        return view('queue-monitor::events', [
            'events' => QueueMonitorEvent::query()->latest()->paginate(30),
        ]);
    }
}
