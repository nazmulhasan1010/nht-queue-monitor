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

    /**
     * @param array $payload
     */
    public function __construct(public array $payload)
    {
    }

    /**
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel(config('queue-monitor.broadcasting.channel', 'queue-monitor'));
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'job.recorded';
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
