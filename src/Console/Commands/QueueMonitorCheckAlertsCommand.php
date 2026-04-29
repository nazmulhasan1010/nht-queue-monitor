<?php

namespace NHT\QueueMonitor\Console\Commands;

use Illuminate\Console\Command;
use NHT\QueueMonitor\Services\AlertService;

class QueueMonitorCheckAlertsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'queue-monitor:check-alerts';
    /**
     * @var string
     */
    protected $description = 'Check Queue Pulse alert thresholds';

    /**
     * @param AlertService $alertService
     * @return int
     */
    public function handle(AlertService $alertService): int
    {
        $alerts = $alertService->check();

        $this->info('Queue Pulse alert check complete.');
        $this->line('Active/updated alerts: ' . count($alerts));

        return self::SUCCESS;
    }
}
