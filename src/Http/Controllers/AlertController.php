
<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use NHT\QueueMonitor\Models\QueueMonitorAlert;
use NHT\QueueMonitor\Services\AlertService;

class AlertController extends Controller
{
    public function index()
    {
        return view('queue-monitor::alerts', [
            'alerts' => QueueMonitorAlert::query()->latest()->paginate(30),
        ]);
    }

    public function check(AlertService $alertService): RedirectResponse
    {
        $alertService->check();

        return back()->with('queue_pulse_success', 'Alert check completed.');
    }

    public function resolve(int|string $id): RedirectResponse
    {
        QueueMonitorAlert::query()->where('id', $id)->update([
            'resolved_at' => now(),
        ]);

        return back()->with('queue_pulse_success', 'Alert resolved.');
    }
}
