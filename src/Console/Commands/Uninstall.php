<?php

namespace NHT\QueueMonitor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Uninstall extends Command
{
    /**
     * @var string
     */
    protected $signature = 'nht-queue-monitor:remove {--force : Force delete without confirmation}';

    /**
     * @var string
     */
    protected $description = 'Uninstall nht|queue-monitor (remove config, views, styles, controllers, routes and broadcast channel)';

    /**
     * @return int
     */
    public function handle(): int
    {
        $this->info('Uninstalling nh|queue-monitor...');

        $fs = new Filesystem();

        $paths = [
            config_path('queue-monitor.php'),
            database_path('migrations/2026_04_28_000001_create_queue_monitor_events_table.php'),
            database_path('migrations/2026_04_28_000002_create_queue_monitor_jobs_table.php'),
            database_path('migrations/2026_04_28_000003_add_enterprise_fields_to_queue_monitor_jobs.php'),
            database_path('migrations/2026_04_28_000004_create_queue_monitor_alerts_table.php'),
            public_path('vendor/queue-monitor')
        ];

        foreach ($paths as $path) {
            if ($fs->exists($path)) {
                if ($this->option('force') || $this->confirm("Delete {$path}?", true)) {
                    $fs->delete($path);
                    $fs->deleteDirectory($path);
                    $this->line("Removed: {$path}");
                }
            }
        }

        $this->callSilent('config:clear');
        $this->callSilent('view:clear');
        $this->callSilent('cache:clear');

        $this->newLine();
        $this->info('✅ nht|queue-monitor successfully uninstalled!');
        $this->line('All related config, templates, and channels have been removed.');

        return self::SUCCESS;
    }
}
