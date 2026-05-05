<?php

namespace NHT\QueueMonitor\Services;

use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JsonException;

class PendingJobService
{
    /**
     * @return string
     */
    private function table(): string
    {
        return config('queue-monitor.queue_jobs_table', 'jobs');
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function filteredPaginate(array $filters): LengthAwarePaginator
    {
        $q = DB::table($this->table())->orderBy('created_at');

        if (!empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $q->where(function ($w) use ($term) {
                $w->where('payload', 'like', $term)->orWhere('id', 'like', $term);
            });
        }

        if (!empty($filters['queue'])) {
            $queues = is_array($filters['queue']) ? $filters['queue'] : explode(',', $filters['queue']);
            $q->whereIn('queue', array_filter($queues));
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
            abort(404, 'Pending job not found');
        }

        return $job;
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


    /**
     * @param int|string $id
     * @return void
     * @throws JsonException
     */
    public function run(int|string $id): void
    {
        $job = $this->findOrFail($id);
        $payload = $this->decodePayload($job);
        
        $command = null;

        if (isset($payload['data']['command'])) {
            try {
                $command = unserialize($payload['data']['command'], ['allowed_classes' => true]);
            } catch (\Throwable $e) {
                throw new \Exception("Failed to unserialize job: " . $e->getMessage());
            }
        } elseif (isset($payload['job'])) {
            if (str_contains($payload['job'], '@')) {
                [$class, $method] = explode('@', $payload['job']);
                if (class_exists($class)) {
                    $instance = app($class);
                    $data = $payload['data'] ?? [];
                    
                    $this->delete($id);
                    
                    if (method_exists($instance, $method)) {
                        $instance->{$method}(...$data);
                        return;
                    }
                }
            }
            throw new Exception("Job type '" . $payload['job'] . "' is not directly runnable. Only standard queued jobs are supported.");
        }

        if ($command) {
            $this->delete($id);
            
            try {
                $dispatcher = app(Dispatcher::class);
                
                if (method_exists($dispatcher, 'dispatchSync')) {
                    $dispatcher->dispatchSync($command);
                } else {
                    $dispatcher->dispatch($command);
                }
            } catch (Exception $e) {
                Log::error("Queue Monitor: Manual run failed for job $id. " . $e->getMessage(), [
                    'id' => $id,
                    'job' => get_class($command),
                    'exception' => $e
                ]);
                throw $e;
            }
        } else {
            throw new Exception("Job command not found in payload. Payload keys: " . implode(', ', array_keys($payload)));
        }
    }
}
