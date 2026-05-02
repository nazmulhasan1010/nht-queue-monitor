<?php

namespace NHT\QueueMonitor\Models;

use Illuminate\Database\Eloquent\Model;

class QueueMonitorAlert extends Model
{
    /**
     * @var string
     */
    protected $table = 'queue_monitor_alerts';

    /**
     * @var string[]
     */
    protected $fillable = [
        'alert_key',
        'level',
        'title',
        'message',
        'meta',
        'resolved_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'meta' => 'array',
        'resolved_at' => 'datetime',
    ];
}
