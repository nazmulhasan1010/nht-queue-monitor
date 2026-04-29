<?php

namespace NHT\QueueMonitor\Console\Commands;

use Illuminate\Console\Command;
use NHT\QueueMonitor\Services\QueueHealthService;

class QueueMonitorHealthCommand extends Command
{
    protected $signature = 'queue-monitor:health';
    protected $description = 'Show Queue Pulse health summary';

    public function handle(QueueHealthService $service): int
    {
        $summary = $service->summary();

        $this->info('Queue Pulse Health');
        $this->line('Status: ' . strtoupper($summary['status']));
        $this->line('Score: ' . $summary['score']);
        $this->line('Total Failed: ' . $summary['total_failed']);
        $this->line('Failed Last 24h: ' . $summary['failed_last_24h']);
        $this->line('Failed Last Hour: ' . $summary['failed_last_hour']);
        $this->line('Latest Failed At: ' . ($summary['latest_failed_at'] ?: 'N/A'));

        return self::SUCCESS;
    }
}
