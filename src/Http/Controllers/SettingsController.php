<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return view('queue-monitor::settings', [
            'config' => config('queue-monitor'),
        ]);
    }
}
