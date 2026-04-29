<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FailedJobService
{
    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function filteredPaginate(array $filters): LengthAwarePaginator
    {
        $q = DB::table('failed_jobs')->orderByDesc('failed_at');

        if (!empty($filters['q'])) {
            $term = '%'.$filters['q'].'%';
            $q->where(function($w) use ($term) {
                $w->where('payload', 'like', $term)->orWhere('exception', 'like', $term)->orWhere('uuid', 'like', $term);
            });
        }

        if (!empty($filters['queue'])) {
            $q->where('queue', $filters['queue']);
        }

        if (!empty($filters['connection'])) {
            $q->where('connection', $filters['connection']);
        }

        if (!empty($filters['from'])) {
            $q->whereDate('failed_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $q->whereDate('failed_at', '<=', $filters['to']);
        }

        return $q->paginate((int) config('queue-monitor.pagination', 20))->withQueryString();
    }

    /**
     * @return mixed
     */
    public function distinctQueues()
    {
        return DB::table('failed_jobs')->distinct()->pluck('queue')->filter()->values();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFail($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();

        if (!$job) {
            abort(404, 'Failed job not found');
        }

        return $job;
    }

    /**
     * @return mixed
     */
    public function distinctConnections()
    {
        return DB::table('failed_jobs')->distinct()->pluck('connection')->filter()->values();
    }

    public function decodePayload($job): array
    {
        if (!isset($job->payload)) {
            return [];
        }

        $payload = json_decode($job->payload, true);

        return is_array($payload) ? $payload : [];
    }

    /**
     * Extract job name from payload
     */
    public function jobName($job): string
    {
        $payload = $this->decodePayload($job);

        return $payload['displayName']
            ?? $payload['job']
            ?? data_get($payload, 'data.commandName')
            ?? 'Unknown Job';
    }

    /**
     * Extract UUID safely
     */
    public function uuid($job): ?string
    {
        $payload = $this->decodePayload($job);

        return $job->uuid
            ?? ($payload['uuid'] ?? null);
    }

    /**
     * Extract attempts count
     */
    public function attempts($job): ?int
    {
        $payload = $this->decodePayload($job);

        return $payload['attempts'] ?? null;
    }

    /**
     * Short exception preview (for UI)
     */
    public function exceptionPreview($job, int $limit = 300): string
    {
        if (!isset($job->exception)) {
            return '';
        }

        $text = trim($job->exception);

        return mb_strlen($text) > $limit
            ? mb_substr($text, 0, $limit) . '...'
            : $text;
    }
}
