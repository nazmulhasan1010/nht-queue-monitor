<?php

namespace NHT\QueueMonitor\Support;

class JobTimer
{
    /**
     * @var array
     */
    protected static array $starts = [];

    /**
     * @param string $uuid
     * @return void
     */
    public static function start(string $uuid): void
    {
        static::$starts[$uuid] = microtime(true);
    }

    /**
     * @param string $uuid
     * @return int|null
     */
    public static function stop(string $uuid): ?int
    {
        if (!isset(static::$starts[$uuid])) {
            return null;
        }

        $duration = (int) ((microtime(true) - static::$starts[$uuid]) * 1000);
        unset(static::$starts[$uuid]);

        return $duration;
    }
}
