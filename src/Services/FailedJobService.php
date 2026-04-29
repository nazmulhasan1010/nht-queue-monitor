<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FailedJobService
{
    public function filteredPaginate(array $filters): LengthAwarePaginator
    {
        $q = DB::table('failed_jobs')->orderByDesc('failed_at');

        if (!empty($filters['q'])) {
            $term = '%'.$filters['q'].'%';
            $q->where(function($w) use ($term) {
                $w->where('payload', 'like', $term)
                  ->orWhere('exception', 'like', $term)
                  ->orWhere('uuid', 'like', $term);
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

    public function distinctQueues()
    {
        return DB::table('failed_jobs')->distinct()->pluck('queue')->filter()->values();
    }

    public function distinctConnections()
    {
        return DB::table('failed_jobs')->distinct()->pluck('connection')->filter()->values();
    }
}
