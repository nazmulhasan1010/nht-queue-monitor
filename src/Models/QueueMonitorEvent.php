<?php

namespace NHT\QueueMonitor\Models;

use Illuminate\Database\Eloquent\Model;

class QueueMonitorEvent extends Model
{
    /**
     * @var string
     */
    protected $table = 'queue_monitor_events';

    /**
     * @var string[]
     */
    protected $fillable = [
        'event_type',
        'job_id',
        'queue',
        'connection',
        'job_name',
        'performed_by',
        'meta',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'meta' => 'array',
    ];
}
