@extends('queue-monitor::layout')

@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Pending Jobs</h1>
            <p class="qp-subtitle">Monitor and manage jobs currently waiting in the queue</p>
        </div>

        <div class="qp-actions">
            <form method="POST" action="{{ route('queue-monitor.pending.clear') }}"
                  onsubmit="return qpConfirm('Clear all pending jobs? This will delete all jobs from the queue.', this, 'Clear All', 'qp-btn-danger');">
                @csrf
                @method('DELETE')
                <button type="submit" class="qp-btn qp-btn-danger">Clear All</button>
            </form>
        </div>
    </div>

    <form method="GET" class="qp-card" style="margin-bottom:16px;">
        <div class="qp-filter-grid">
            <div class="qp-filter-item">
                <label>Search Query</label>
                <input class="nht-queue form-input" name="q" placeholder="ID, Payload..." value="{{ $filters['q'] ?? '' }}"/>
            </div>

            <div class="qp-filter-item">
                <label>Queue</label>
                <select class="nht-queue form-input" name="queue">
                    <option value="">All Queues</option>
                    @foreach($queues as $q)
                        <option value="{{ $q }}" @selected(($filters['queue'] ?? '') === $q)>{{ $q }}</option>
                    @endforeach
                </select>
            </div>

            <div class="qp-filter-item qp-filter-actions">
                <label>&nbsp;</label>
                <div class="qp-actions">
                    <button type="submit" class="qp-btn">Apply Filters</button>
                    <a href="{{ route('queue-monitor.pending.index') }}" class="qp-btn qp-btn-danger">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="qp-card">
        <div class="qp-card-head" style="margin-bottom:16px;">
            <div>
                <h2 style="margin:0;">Pending Jobs Table</h2>
                <p class="qp-subtitle">Detailed list of all jobs waiting to be processed</p>
            </div>
        </div>

        <div class="qp-table-wrap">
            <table class="qp-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Queue</th>
                    <th>Job Name</th>
                    <th>Attempts</th>
                    <th>Available At</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @forelse($jobs as $job)
                    @php
                        $payload = json_decode($job->payload ?? '{}', true, 512, JSON_THROW_ON_ERROR);
                        $jobName = $payload['displayName']
                            ?? $payload['job']
                            ?? data_get($payload, 'data.commandName')
                            ?? 'Unknown Job';
                    @endphp

                    <tr>
                        <td>#{{ $job->id }}</td>
                        <td><span class="qp-badge">{{ $job->queue }}</span></td>
                        <td>{{ $jobName }}</td>
                        <td>{{ $job->attempts }}</td>
                        <td>
                            {{ \Carbon\Carbon::createFromTimestamp($job->available_at)->diffForHumans() }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::createFromTimestamp($job->created_at)->toDateTimeString() }}
                        </td>
                        <td>
                            <div class="qp-actions">
                                <a href="{{ route('queue-monitor.pending.show', $job->id) }}"
                                   class="qp-btn qp-btn-sm">
                                    View
                                </a>

                                <form method="POST" onsubmit="return qpConfirm('Run this job manually now? It will be removed from the queue and executed immediately.', this, 'Run Now', 'qp-btn-sm');"
                                      action="{{ route('queue-monitor.pending.run', $job->id) }}"
                                      style="display:inline;">
                                    @csrf
                                    <button type="submit" class="qp-btn qp-btn-sm qp-btn-primary">
                                        Run
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('queue-monitor.pending.destroy', $job->id) }}"
                                      onsubmit="return qpConfirm('Delete this pending job?', this, 'Delete', 'qp-btn-danger');"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="qp-btn qp-btn-sm qp-btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="qp-empty">No pending jobs found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="qp-pagination">
            {{ $jobs->links() }}
        </div>
    </div>
@endsection
