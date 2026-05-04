<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use NHT\QueueMonitor\Models\QueueMonitorAlert;
use NHT\QueueMonitor\Services\AlertService;

class AlertController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        return view('queue-monitor::alerts', [
            'alerts' => QueueMonitorAlert::latest()->paginate(30),
        ]);
    }

    /**
     * @param AlertService $alertService
     * @return RedirectResponse
     */
    public function check(AlertService $alertService): RedirectResponse
    {
        $alertService->check();

        return back()->with('queue_monitor_success', 'Alert check completed.');
    }

    /**
     * @param int|string $id
     * @return RedirectResponse
     */
    public function resolve(int|string $id): RedirectResponse
    {
        QueueMonitorAlert::where('id', $id)->update([
            'resolved_at' => now(),
        ]);

        return back()->with('queue_monitor_success', 'Alert resolved.');
    }
}
