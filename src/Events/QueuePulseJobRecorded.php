<?php

namespace NHT\QueueMonitor\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueMonitorJobRecorded implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public array $payload)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel(config('queue-monitor.broadcasting.channel', 'queue-monitor'));
    }

    public function broadcastAs(): string
    {
        return 'job.recorded';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
