
<?php

use Illuminate\Support\Facades\Route;
use NHT\QueueMonitor\Http\Controllers\Api\LiveController;

Route::prefix('queue-monitor')->middleware(['web','auth'])->group(function () {
    Route::get('/live', [LiveController::class, 'index']);
});
