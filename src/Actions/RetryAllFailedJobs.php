<?php

namespace NHT\QueueMonitor\Actions;

use Illuminate\Support\Facades\Artisan;

class RetryAllFailedJobs
{
    public function execute(): int
    {
        return Artisan::call('queue:retry', [
            'id' => ['all'],
        ]);
    }
}
