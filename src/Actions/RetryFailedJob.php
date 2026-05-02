<?php

namespace NHT\QueueMonitor\Actions;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RetryFailedJob
{
    /**
     * @param int|string $id
     * @return int
     */
    public function execute(int|string $id): int
    {
        try {
            return Artisan::call('queue:retry', [
                'id' => [(string)$id],
            ]);
        } catch (\Exception $e) {
            Log::error("Queue Monitor: Failed to retry job {$id}. " . $e->getMessage());
            return 1;
        }
    }
}
