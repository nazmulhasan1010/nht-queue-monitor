<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Support\Facades\Redis;
use Throwable;

class RedisQueueMetricsService
{
    /**
     * @param array $queues
     * @return array
     */
    public function metrics(array $queues = ['default']): array
    {
        $rows = [];

        foreach ($queues as $queue) {
            try {
                $pending = Redis::llen("queues:{$queue}");
                $delayed = Redis::zcard("queues:{$queue}:delayed");
                $reserved = Redis::zcard("queues:{$queue}:reserved");

                $rows[] = [
                    'queue' => $queue,
                    'pending' => $pending,
                    'delayed' => $delayed,
                    'reserved' => $reserved,
                    'available' => true,
                ];
            } catch (Throwable $e) {
                $rows[] = [
                    'queue' => $queue,
                    'pending' => null,
                    'delayed' => null,
                    'reserved' => null,
                    'available' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $rows;
    }
}
