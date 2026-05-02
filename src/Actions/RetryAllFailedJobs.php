<?php

namespace NHT\QueueMonitor\Actions;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RetryAllFailedJobs
{
    /**
     * @return int
     */
    public function execute(): int
    {
        try {
            return Artisan::call('queue:retry', [
                'id' => ['all'],
            ]);
        } catch (\Exception $e) {
            Log::error("Queue Monitor: Failed to retry all jobs. " . $e->getMessage());
            return 1;
        }
    }
}
