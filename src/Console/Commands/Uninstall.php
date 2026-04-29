<?php

namespace NHT\QueueMonitor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use function NH\Notification\Console\app_path;
use function NH\Notification\Console\config_path;

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
        $this->info('Uninstalling nh|Notification...');

        $fs = new Filesystem();

        $paths = [
            config_path('notification.php'),
            app_path('Broadcasting/SmsChannel.php'),
            app_path('Broadcasting/SmsNiagaChannel.php'),
            app_path('Http/Controllers/NotificationController.php'),
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
        $this->info('✅ nht|notification successfully uninstalled!');
        $this->line('All related config, templates, and channels have been removed.');

        return self::SUCCESS;
    }
}
