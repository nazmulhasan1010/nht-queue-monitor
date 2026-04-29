<?php

namespace NHT\QueueMonitor\Actions;

use Illuminate\Support\Facades\Artisan;

class RetryAllFailedJobs
{
    /**
     * @return int
     */
    public function execute(): int
    {
        return Artisan::call('queue:retry', [
            'id' => ['all'],
        ]);
    }
}
