<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use JsonException;

class FailedJobService
{
    /**
     * @return string
     */
    private function table(): string
    {
        return config('queue.failed.table', 'failed_jobs');
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function filteredPaginate(array $filters): LengthAwarePaginator
    {
        $q = DB::table($this->table())->orderByDesc('failed_at');

        if (!empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $q->where(function ($w) use ($term) {
                $w->where('payload', 'like', $term)
                  ->orWhere('exception', 'like', $term)
                  ->orWhere('uuid', 'like', $term)
                  ->orWhere('id', 'like', $term);
            });
        }

        if (!empty($filters['queue'])) {
            $queues = is_array($filters['queue']) ? $filters['queue'] : explode(',', $filters['queue']);
            $q->whereIn('queue', array_filter($queues));
        }

        if (!empty($filters['connection'])) {
            $connections = is_array($filters['connection']) ? $filters['connection'] : explode(',', $filters['connection']);
            $q->whereIn('connection', array_filter($connections));
        }

        if (!empty($filters['exception'])) {
            $q->where('exception', 'like', '%' . $filters['exception'] . '%');
        }

        if (!empty($filters['from'])) {
            $q->whereDate('failed_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('failed_at', '<=', $filters['to']);
        }

        return $q->paginate((int)config('queue-monitor.pagination', 20))->withQueryString();
    }

    /**
     * @return mixed
     */
    public function distinctQueues(): mixed
    {
        return DB::table($this->table())->distinct()->pluck('queue')->filter()->values();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id): mixed
    {
        return DB::table($this->table())->where('id', $id)->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFail($id): mixed
    {
        $job = DB::table($this->table())->where('id', $id)->first();

        if (!$job) {
            abort(404, 'Failed job not found');
        }

        return $job;
    }

    /**
     * @return mixed
     */
    public function distinctConnections(): mixed
    {
        return DB::table($this->table())->distinct()->pluck('connection')->filter()->values();
    }

    /**
     * @param $job
     * @return array
     * @throws JsonException
     */
    public function decodePayload($job): array
    {
        if (!isset($job->payload)) {
            return [];
        }

        $payload = json_decode($job->payload, true, 512, JSON_THROW_ON_ERROR);

        return is_array($payload) ? $payload : [];
    }


    /**
     * @param $job
     * @return string
     * @throws JsonException
     */
    public function jobName($job): string
    {
        $payload = $this->decodePayload($job);

        return $payload['displayName'] ?? $payload['job'] ?? data_get($payload, 'data.commandName') ?? 'Unknown Job';
    }


    /**
     * @param $job
     * @return string|null
     * @throws JsonException
     */
    public function uuid($job): ?string
    {
        $payload = $this->decodePayload($job);

        return $job->uuid
            ?? ($payload['uuid'] ?? null);
    }


    /**
     * @param $job
     * @return int|null
     * @throws JsonException
     */
    public function attempts($job): ?int
    {
        $payload = $this->decodePayload($job);

        return $payload['attempts'] ?? null;
    }


    /**
     * @param $job
     * @param int $limit
     * @return string
     */
    public function exceptionPreview($job, int $limit = 300): string
    {
        if (!isset($job->exception)) {
            return '';
        }

        $text = trim($job->exception);

        if (preg_match('/^[^:]+:\s*(.*?)\s+in\s+/s', $text, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/^[^:]+:\s*(.*)$/s', $text, $matches)) {
            return trim($matches[1]);
        }

        return $text;
    }

    /**
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        return DB::table($this->table())->where('id', $id)->delete() > 0;
    }

    /**
     * @param array $ids
     * @return int
     */
    public function bulkDelete(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        return DB::table($this->table())->whereIn('id', $ids)->delete();
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        DB::table($this->table())->truncate();
    }
}
