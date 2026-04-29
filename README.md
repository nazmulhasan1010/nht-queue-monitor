# Laravel Queue Pulse

Modern dark + pink Laravel queue monitor package.

## Features

- Failed jobs dashboard
- Failed job detail viewer
- Retry single failed job
- Retry all failed jobs
- Delete failed job
- Bulk delete
- Clear all failed jobs
- Payload viewer
- Exception viewer
- Copy buttons
- Failure charts
- Filters/search
- Audit logs
- Settings page
- Mail/Slack notification foundation
- Access control middleware
- CSV export

## Install from local package

In your Laravel app `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "packages/queue-monitor"
    }
  ],
  "require": {
    "your-vendor/queue-monitor": "*"
  }
}
```

Then:

```bash
composer update your-vendor/queue-monitor
php artisan vendor:publish --tag=queue-monitor-config
php artisan vendor:publish --tag=queue-monitor-assets
php artisan vendor:publish --tag=queue-monitor-migrations
php artisan migrate
```

Visit:

```txt
/queue-monitor
```

## Access Control

Use one or both:

```env
QUEUE_PULSE_ALLOWED_EMAILS=admin@example.com,dev@example.com
QUEUE_PULSE_ENABLE_GATE=true
```

In `AuthServiceProvider`:

```php
Gate::define('viewQueueMonitor', function ($user) {
    return $user->email === 'admin@example.com';
});
```
