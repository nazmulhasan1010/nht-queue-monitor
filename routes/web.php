
<?php

use Illuminate\Support\Facades\Route;
use NHT\QueueMonitor\Http\Controllers\AlertController;
use NHT\QueueMonitor\Http\Controllers\DashboardController;
use NHT\QueueMonitor\Http\Controllers\EventController;
use NHT\QueueMonitor\Http\Controllers\ExportController;
use NHT\QueueMonitor\Http\Controllers\FailedJobController;
use NHT\QueueMonitor\Http\Controllers\HealthController;
use NHT\QueueMonitor\Http\Controllers\JobHistoryController;
use NHT\QueueMonitor\Http\Controllers\SettingsController;
use NHT\QueueMonitor\Http\Controllers\SystemController;

Route::group([
    'prefix' => config('queue-monitor.route_prefix', 'queue-monitor'),
    'middleware' => config('queue-monitor.middleware', ['web', 'auth']),
], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('queue-monitor.dashboard');

    Route::get('/failed', [FailedJobController::class, 'index'])->name('queue-monitor.failed.index');
    Route::get('/failed/{id}', [FailedJobController::class, 'show'])->name('queue-monitor.failed.show');
    Route::post('/failed/{id}/retry', [FailedJobController::class, 'retry'])->name('queue-monitor.failed.retry');
    Route::post('/failed/retry-all', [FailedJobController::class, 'retryAll'])->name('queue-monitor.failed.retry-all');
    Route::delete('/failed/{id}', [FailedJobController::class, 'destroy'])->name('queue-monitor.failed.destroy');
    Route::delete('/failed-clear/all', [FailedJobController::class, 'clear'])->name('queue-monitor.failed.clear');
    Route::delete('/failed-bulk/delete', [FailedJobController::class, 'bulkDestroy'])->name('queue-monitor.failed.bulk-destroy');

    Route::get('/jobs', [JobHistoryController::class, 'index'])->name('queue-monitor.jobs.index');
    Route::get('/jobs/{id}', [JobHistoryController::class, 'show'])->name('queue-monitor.jobs.show');

    Route::get('/health', [HealthController::class, 'index'])->name('queue-monitor.health.index');
    Route::get('/health/json', [HealthController::class, 'json'])->name('queue-monitor.health.json');

    Route::get('/alerts', [AlertController::class, 'index'])->name('queue-monitor.alerts.index');
    Route::post('/alerts/check', [AlertController::class, 'check'])->name('queue-monitor.alerts.check');
    Route::post('/alerts/{id}/resolve', [AlertController::class, 'resolve'])->name('queue-monitor.alerts.resolve');

    Route::get('/events', [EventController::class, 'index'])->name('queue-monitor.events.index');
    Route::get('/system', [SystemController::class, 'index'])->name('queue-monitor.system.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('queue-monitor.settings.index');

    Route::get('/exports/failed-jobs', [ExportController::class, 'failedJobs'])->name('queue-monitor.exports.failed-jobs');
    Route::get('/exports/events', [ExportController::class, 'events'])->name('queue-monitor.exports.events');
});
