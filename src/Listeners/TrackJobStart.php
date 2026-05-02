<?php

namespace NHT\QueueMonitor\Listeners;

use Illuminate\Queue\Events\JobProcessing;
use NHT\QueueMonitor\Support\JobPayload;
use NHT\QueueMonitor\Support\JobTimer;

class TrackJobStart
{
    /**
     * @param JobProcessing $event
     * @return void
     */
    public function handle(JobProcessing $event): void
    {
        $payload = JobPayload::fromRaw($event->job->getRawBody());
        $uuid = JobPayload::uuid($payload);

        if ($uuid) {
            JobTimer::start($uuid);
        }
    }
}
