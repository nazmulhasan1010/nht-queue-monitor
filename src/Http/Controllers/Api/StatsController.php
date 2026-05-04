<?php

namespace NHT\QueueMonitor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StatsController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function trend(Request $request)
    {
        $days = (int) $request->get('days', 7);
        $start = Carbon::today()->subDays($days - 1);

        $rows = DB::table('failed_jobs')->selectRaw('DATE(failed_at) as d, COUNT(*) as c')->whereDate('failed_at', '>=', $start)->groupBy('d')->orderBy('d')->get();

        $map = [];
        for ($i=0; $i<$days; $i++) {
            $d = $start->copy()->addDays($i)->toDateString();
            $map[$d] = 0;
        }
        foreach ($rows as $r) {
            $map[$r->d] = (int) $r->c;
        }

        return response()->json([
            'labels' => array_keys($map),
            'values' => array_values($map),
        ]);
    }
}
