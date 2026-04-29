<?php

namespace NHT\QueueMonitor\Actions;

use Illuminate\Support\Facades\Artisan;

class RetryFailedJob
{
    /**
     * @param int|string $id
     * @return int
     */
    public function execute(int|string $id): int
    {
        return Artisan::call('queue:retry', [
            'id' => [$id],
        ]);
    }
}
