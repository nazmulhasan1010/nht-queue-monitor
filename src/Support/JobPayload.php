
<?php

namespace NHT\QueueMonitor\Support;

class JobPayload
{
    public static function fromRaw(?string $rawPayload): array
    {
        $payload = json_decode($rawPayload ?: '{}', true);

        return is_array($payload) ? $payload : [];
    }

    public static function displayName(array $payload): string
    {
        return $payload['displayName']
            ?? $payload['job']
            ?? data_get($payload, 'data.commandName')
            ?? 'Unknown Job';
    }

    public static function uuid(array $payload): ?string
    {
        return $payload['uuid'] ?? null;
    }
}
