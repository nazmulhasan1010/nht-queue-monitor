<?php

return [
    'enabled' => env('QUEUE_MONITOR_ENABLED', true),
    'route_prefix' => env('QUEUE_MONITOR_ROUTE_PREFIX', 'queue-monitor'),

    'middleware' => [
//        'web',
//        'auth',
//        \NHT\QueueMonitor\Http\Middleware\QueueMonitorAccess::class,
    ],

    'pagination' => 20,

    'allow_retry' => true,
    'allow_delete' => true,
    'allow_clear' => true,
    'allow_bulk_delete' => true,
    'allow_export' => true,

    'node' => [
        'name' => env('QUEUE_MONITOR_NODE_NAME', gethostname() ?: 'default-node'),
        'environment' => env('APP_ENV', 'production'),
    ],

    'tenant' => [
        'enabled' => env('QUEUE_MONITOR_TENANT_MODE', false),
        'resolver' => null,
    ],

    'broadcasting' => [
        'enabled' => env('QUEUE_MONITOR_BROADCAST_ENABLED', false),
        'channel' => env('QUEUE_MONITOR_BROADCAST_CHANNEL', 'queue-monitor'),
    ],

    'alerts' => [
        'enabled' => env('QUEUE_MONITOR_ALERTS_ENABLED', true),
        'failed_jobs_24h' => env('QUEUE_MONITOR_ALERT_FAILED_24H', 20),
        'failed_jobs_1h' => env('QUEUE_MONITOR_ALERT_FAILED_1H', 5),
    ],

    'tracking' => [
        'track_successful_jobs' => env('QUEUE_MONITOR_TRACK_SUCCESSFUL_JOBS', false),
        'track_failed_jobs_events' => env('QUEUE_MONITOR_TRACK_FAILED_JOB_EVENTS', true),
        'store_payload' => env('QUEUE_MONITOR_STORE_JOB_PAYLOAD', false),
        'store_exception' => env('QUEUE_MONITOR_STORE_JOB_EXCEPTION', true),
        'retention_days' => env('QUEUE_MONITOR_JOB_RETENTION_DAYS', 30),
    ],

    'access' => [
        'enable_gate' => env('QUEUE_MONITOR_ENABLE_GATE', false),
        'gate' => 'viewQueueMonitor',
        'allowed_emails' => array_filter(array_map('trim', explode(',', env('QUEUE_MONITOR_ALLOWED_EMAILS', '')))),
    ],

    'audit' => ['enabled' => true],

    'ai' => [
        'enabled' => env('QUEUE_MONITOR_AI_ENABLED', false),
        'provider' => env('QUEUE_MONITOR_AI_PROVIDER', 'openai'), // openai, anthropic
        'api_key' => env('QUEUE_MONITOR_AI_KEY'),
        'model' => env('QUEUE_MONITOR_AI_MODEL', 'gpt-4o-mini'),
    ],

    'notifications' => [
        'enabled' => env('QUEUE_MONITOR_NOTIFICATIONS_ENABLED', false),
        'channels' => [
            'mail' => env('QUEUE_MONITOR_NOTIFY_MAIL', false),
            'slack' => env('QUEUE_MONITOR_NOTIFY_SLACK', false),
        ],
        'mail_to' => env('QUEUE_MONITOR_NOTIFY_MAIL_TO'),
        'slack_webhook_url' => env('QUEUE_MONITOR_SLACK_WEBHOOK_URL'),
    ],

    'assets' => [
        'use_built_assets' => true,
        'css' => 'vendor/queue-monitor/scss/style.css',
        'js' => 'vendor/queue-monitor/js/app.js',
    ],
];
