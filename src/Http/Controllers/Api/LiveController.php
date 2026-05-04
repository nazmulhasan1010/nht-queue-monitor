<?php

namespace NHT\QueueMonitor\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LiveController extends Controller
{
    /**
     * @return string
     */
    private function table(): string
    {
        return config('queue.failed.table', 'failed_jobs');
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return response()->json([
            'failed_jobs' => DB::table($this->table())->latest('failed_at')->limit(10)->get(),
            'events' => DB::table('queue_monitor_events')->latest('created_at')->limit(10)->get(),
        ]);
    }
}
