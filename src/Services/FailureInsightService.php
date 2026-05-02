<?php

namespace NHT\QueueMonitor\Services;

class FailureInsightService
{
    /**
     * @param string|null $exception
     * @return string
     */
    public function insight(?string $exception): string
    {
        $text = strtolower((string)$exception);

        return match (true) {
            str_contains($text, 'connection refused') => 'Possible database/redis/service connection issue. Check service availability and credentials.',
            str_contains($text, 'timeout') => 'This job may be timing out. Check job timeout, external API latency, and worker timeout settings.',
            str_contains($text, 'max attempts') => 'The job exceeded maximum attempts. Review retry/backoff strategy and root exception.',
            str_contains($text, 'memory') => 'Possible memory limit issue. Check payload size, chunk processing, and PHP memory_limit.',
            str_contains($text, 'deadlock') => 'Database deadlock detected. Consider transaction size, lock ordering, and retry logic.',
            str_contains($text, 'rate limit') || str_contains($text, 'too many requests') => 'External provider rate limit may be reached. Add backoff, throttling, or queue separation.',
            default => 'Review the exception stack trace, payload, queue name, and recent deployments for root cause.',
        };
    }
}
