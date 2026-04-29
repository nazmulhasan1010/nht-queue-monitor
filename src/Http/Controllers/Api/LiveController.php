<?php

namespace NHT\QueueMonitor\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LiveController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return response()->json([
            'failed_jobs' => DB::table('failed_jobs')
                ->latest('failed_at')
                ->limit(10)
                ->get(),

            'events' => DB::table('queue_monitor_events')
                ->latest('created_at')
                ->limit(10)
                ->get(),
        ]);
    }
}
