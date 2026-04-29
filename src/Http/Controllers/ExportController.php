<?php

namespace NHT\QueueMonitor\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use NHT\QueueMonitor\Models\QueueMonitorEvent;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function failedJobs(): StreamedResponse
    {
        abort_unless(config('queue-monitor.allow_export', true), 403);

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['id', 'uuid', 'connection', 'queue', 'failed_at', 'job_name']);

            DB::table('failed_jobs')->orderByDesc('failed_at')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    $payload = json_decode($row->payload ?? '{}', true);
                    fputcsv($out, [
                        $row->id,
                        $row->uuid ?? ($payload['uuid'] ?? ''),
                        $row->connection,
                        $row->queue,
                        $row->failed_at,
                        $payload['displayName'] ?? $payload['job'] ?? 'Unknown Job',
                    ]);
                }
            });

            fclose($out);
        }, 'queue-monitor-failed-jobs.csv');
    }

    public function events(): StreamedResponse
    {
        abort_unless(config('queue-monitor.allow_export', true), 403);

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['id', 'event_type', 'job_id', 'queue', 'connection', 'job_name', 'performed_by', 'created_at']);

            QueueMonitorEvent::query()->latest()->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        $row->event_type,
                        $row->job_id,
                        $row->queue,
                        $row->connection,
                        $row->job_name,
                        $row->performed_by,
                        $row->created_at,
                    ]);
                }
            });

            fclose($out);
        }, 'queue-monitor-events.csv');
    }
}
