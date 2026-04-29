<?php

namespace NHT\QueueMonitor\Models;

use Illuminate\Database\Eloquent\Model;

class QueueMonitorEvent extends Model
{
    protected $table = 'queue_pulse_events';

    protected $fillable = [
        'event_type',
        'job_id',
        'queue',
        'connection',
        'job_name',
        'performed_by',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
