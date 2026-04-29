
<?php

namespace NHT\QueueMonitor\Models;

use Illuminate\Database\Eloquent\Model;

class QueueMonitorAlert extends Model
{
    protected $table = 'queue_pulse_alerts';

    protected $fillable = [
        'alert_key',
        'level',
        'title',
        'message',
        'meta',
        'resolved_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'resolved_at' => 'datetime',
    ];
}
