<?php

namespace NHT\QueueMonitor;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use NHT\QueueMonitor\Console\Commands\Install;
use NHT\QueueMonitor\Console\Commands\QueueMonitorCheckAlertsCommand;
use NHT\QueueMonitor\Console\Commands\QueueMonitorHealthCommand;
use NHT\QueueMonitor\Console\Commands\Uninstall;
use NHT\QueueMonitor\Listeners\RecordFailedJob;
use NHT\QueueMonitor\Listeners\RecordProcessedJob;

class QueueMonitorServiceProvider extends ServiceProvider
{
    /**
     *
     */
    const QUEUE_MONITOR_MIGRATIONS = 'queue-monitor-migrations';

    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/queue-monitor.php', 'queue-monitor');
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        if (! config('queue-monitor.enabled', true)) {
            return;
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'queue-monitor');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Event::listen(JobProcessed::class, RecordProcessedJob::class);
        Event::listen(JobFailed::class, RecordFailedJob::class);

        if ($this->app->runningInConsole()) {
            $commands = [QueueMonitorCheckAlertsCommand::class, Install::class, Uninstall::class,];

            if (class_exists(QueueMonitorHealthCommand::class)) {
                $commands[] = QueueMonitorHealthCommand::class;
            }

            $this->commands($commands);
        }

        Blade::directive('queuePulseAssets', function () {
            return "<?php echo view('queue-monitor::partials.assets')->render(); ?>";
        });

        $this->publishes([__DIR__ . '/../config/queue-monitor.php' => config_path('queue-monitor.php')], 'queue-monitor-config');
        $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], '' . self::QUEUE_MONITOR_MIGRATIONS . '');
        $this->publishes([__DIR__ . '/../public/vendor/queue-monitor' => public_path('vendor/queue-monitor')], 'queue-monitor-assets');
    }
}
