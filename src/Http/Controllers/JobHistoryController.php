<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use NHT\QueueMonitor\Models\QueueMonitorJob;

class JobHistoryController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $query = QueueMonitorJob::query()->latest('finished_at');

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('queue')) {
            $query->where('queue', $request->get('queue'));
        }

        if ($request->filled('q')) {
            $term = '%' . $request->get('q') . '%';

            $query->where(function ($q) use ($term) {
                $q->where('job_name', 'like', $term)
                    ->orWhere('uuid', 'like', $term)
                    ->orWhere('exception', 'like', $term);
            });
        }

        return view('queue-monitor::jobs.index', [
            'jobs' => $query->paginate((int) config('queue-monitor.pagination', 20))->withQueryString(),
            'queues' => QueueMonitorJob::query()->distinct()->pluck('queue')->filter()->values(),
            'filters' => $request->only(['status', 'queue', 'q']),
        ]);
    }

    /**
     * @param int|string $id
     * @return View
     */
    public function show(int|string $id)
    {
        return view('queue-monitor::jobs.show', [
            'job' => QueueMonitorJob::query()->findOrFail($id),
        ]);
    }
}
