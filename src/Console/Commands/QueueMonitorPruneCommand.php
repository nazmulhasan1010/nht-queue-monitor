<?php

namespace NHT\QueueMonitor\Console\Commands;

use Illuminate\Console\Command;
use NHT\QueueMonitor\Models\QueueMonitorEvent;
use NHT\QueueMonitor\Models\QueueMonitorJob;

class QueueMonitorPruneCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'queue-monitor:prune';

    /**
     * @var string
     */
    protected $description = 'Prune old queue monitor records based on retention settings';

    /**
     * @return int
     */
    public function handle(): int
    {
        $days = config('queue-monitor.tracking.retention_days', 30);
        $date = now()->subDays($days);

        $this->info("Pruning records older than {$days} days ({$date->toDateTimeString()})...");

        $jobsDeleted = QueueMonitorJob::where('finished_at', '<', $date)->delete();
        $eventsDeleted = QueueMonitorEvent::where('created_at', '<', $date)->delete();

        $this->info("Deleted {$jobsDeleted} monitor job records.");
        $this->info("Deleted {$eventsDeleted} monitor event records.");

        return self::SUCCESS;
    }
}
