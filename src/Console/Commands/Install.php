<?php

namespace NHT\QueueMonitor\Console\Commands;

use Illuminate\Console\Command;
use NHT\QueueMonitor\QueueMonitorServiceProvider;

class Install extends Command
{
    /**
     * @var string
     */
    protected $signature = 'nht-queue-monitor:published {--force : Overwrite any existing files}';

    /**
     * @var string
     */
    protected $description = 'Install nh|queue-monitor (publish config, views, styles, controllers, routes and broadcast channel)';

    /**
     * @return int
     */
    public function handle(): int
    {
        $this->info('Installing nh|queue-monitor...');

        $this->callSilent('vendor:publish', [
            '--provider' => QueueMonitorServiceProvider::class,
            '--tag' => 'queue-monitor-config',
            '--force' => $this->option('force'),
        ]);

        $this->callSilent('vendor:publish', [
            '--provider' => QueueMonitorServiceProvider::class,
            '--tag' => 'queue-monitor-migrations',
            '--force' => $this->option('force'),
        ]);

        $this->callSilent('vendor:publish', [
            '--provider' => QueueMonitorServiceProvider::class,
            '--tag' => 'queue-monitor-assets',
            '--force' => $this->option('force'),
        ]);


        $this->info('nht-queue-monitor installed!');
        $this->info('Config: config/queue-monitor.php');
        $this->info('Assets: public/vendor/queue-monitor');
        $this->info('Migrations: database/migrations');
        return self::SUCCESS;
    }
}
