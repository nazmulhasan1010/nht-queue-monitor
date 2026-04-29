<?php

namespace NHT\QueueMonitor\Support;

class JobPayload
{
    /**
     * @param string|null $rawPayload
     * @return array
     */
    public static function fromRaw(?string $rawPayload): array
    {
        $payload = json_decode($rawPayload ?: '{}', true);

        return is_array($payload) ? $payload : [];
    }

    /**
     * @param array $payload
     * @return string
     */
    public static function displayName(array $payload): string
    {
        return $payload['displayName']
            ?? $payload['job']
            ?? data_get($payload, 'data.commandName')
            ?? 'Unknown Job';
    }

    /**
     * @param array $payload
     * @return string|null
     */
    public static function uuid(array $payload): ?string
    {
        return $payload['uuid'] ?? null;
    }
}
