<?php

namespace NHT\QueueMonitor\Models;

use Illuminate\Database\Eloquent\Model;

class QueueMonitorJob extends Model
{
    protected $table = 'queue_monitor_jobs';

    protected $fillable = [
        'uuid',
        'connection',
        'queue',
        'node_name',
        'tenant_id',
        'job_name',
        'status',
        'attempts',
        'duration_ms',
        'exception',
        'insight',
        'payload',
        'tags',
        'finished_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'tags' => 'array',
        'finished_at' => 'datetime',
    ];
}
