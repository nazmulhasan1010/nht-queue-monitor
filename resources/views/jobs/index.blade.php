@extends('queue-monitor::layout')

@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Jobs History</h1>
            <p class="qp-subtitle">Optional processed and failed job lifecycle tracking.</p>
        </div>
    </div>

    <form method="GET" class="qp-card" style="margin-bottom:16px;">
        <div class="qp-actions">
            <input class="nht-queue form-input" name="q" placeholder="Search job / UUID / exception"
                   value="{{ $filters['q'] ?? '' }}"/>

            <select class="nht-queue form-input" name="status">
                <option value="">All Status</option>
                <option value="processed" @selected(($filters['status'] ?? '') === 'processed')>Processed</option>
                <option value="failed" @selected(($filters['status'] ?? '') === 'failed')>Failed</option>
            </select>

            <select class="nht-queue form-input" name="queue">
                <option value="">All Queues</option>
                @foreach($queues as $queue)
                    <option value="{{ $queue }}" @selected(($filters['queue'] ?? '') === $queue)>{{ $queue }}</option>
                @endforeach
            </select>

            <button class="qp-btn">Apply</button>
        </div>
    </form>

    <div class="qp-card">
        <div class="qp-table-wrap">
            <table class="qp-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Queue</th>
                    <th>Connection</th>
                    <th>Job</th>
                    <th>Attempts</th>
                    <th>Finished At</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>#{{ $job->id }}</td>
                        <td><span class="qp-badge">{{ strtoupper($job->status) }}</span></td>
                        <td>{{ $job->queue ?: '-' }}</td>
                        <td>{{ $job->connection ?: '-' }}</td>
                        <td>{{ $job->job_name ?: 'Unknown Job' }}</td>
                        <td>{{ $job->attempts ?? '-' }}</td>
                        <td>{{ $job->finished_at ?: $job->created_at }}</td>
                        <td><a class="qp-btn qp-btn-sm" href="{{ route('queue-monitor.jobs.show', $job->id) }}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="qp-empty">
                            No job history found. Enable QUEUE_MONITOR_TRACK_SUCCESSFUL_JOBS=true to track processed
                            jobs.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="qp-pagination">{{ $jobs->links() }}</div>
    </div>
@endsection
