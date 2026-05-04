@extends('queue-monitor::layout')

@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Failed Jobs</h1>
            <p class="qp-subtitle">Filter, search, inspect, retry and delete failed jobs</p>
        </div>

        <div class="qp-actions">
            <form method="POST" action="{{ route('queue-monitor.failed.retry-all') }}"
                  onsubmit="return qpConfirm('Retry all failed jobs?', this, 'Retry All', 'qp-btn-secondary');">
                @csrf
                <button type="submit" class="qp-btn">Retry All</button>
            </form>

            <form method="POST" action="{{ route('queue-monitor.failed.clear') }}"
                  onsubmit="return qpConfirm('Clear all failed jobs? This cannot be undone.', this, 'Clear All', 'qp-btn-danger');">
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
                <input class="nht-queue form-input" name="q" placeholder="ID, UUID, Job Name..." value="{{ $filters['q'] ?? '' }}"/>
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

            <div class="qp-filter-item">
                <label>Connection</label>
                <select class="nht-queue form-input" name="connection">
                    <option value="">All Connections</option>
                    @foreach($connections as $c)
                        <option value="{{ $c }}" @selected(($filters['connection'] ?? '') === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="qp-filter-item">
                <label>Date From</label>
                <input class="nht-queue form-input" type="date" name="from" value="{{ $filters['from'] ?? '' }}"/>
            </div>

            <div class="qp-filter-item">
                <label>Date To</label>
                <input class="nht-queue form-input" type="date" name="to" value="{{ $filters['to'] ?? '' }}"/>
            </div>

            <div class="qp-filter-item qp-filter-actions">
                <label>&nbsp;</label>
                <div class="qp-actions">
                    <button type="submit" class="qp-btn">Apply Filters</button>
                    <a href="{{ route('queue-monitor.failed.index') }}" class="qp-btn qp-btn-danger">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="qp-card">
        <div class="qp-card-head" style="margin-bottom:16px;">
            <div>
                <h2 style="margin:0;">Failed Jobs Table</h2>
                <p class="qp-subtitle">Detailed list of all failed queue jobs</p>
            </div>
        </div>

        <div class="qp-table-wrap">
            <table class="qp-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>UUID</th>
                    <th>Queue</th>
                    <th>Connection</th>
                    <th>Job</th>
                    <th>Failed At</th>
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

                        $uuid = $job->uuid ?? ($payload['uuid'] ?? 'N/A');
                    @endphp

                    <tr>
                        <td>#{{ $job->id }}</td>
                        <td class="qp-mono">{{ $uuid }}</td>
                        <td><span class="qp-badge">{{ $job->queue }}</span></td>
                        <td>{{ $job->connection }}</td>
                        <td>{{ $jobName }}</td>
                        <td>
                            <p>{{ $job->failed_at }}</p>
                            <p><b>{{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}</b></p>
                        </td>
                        <td>
                            <div class="qp-actions">
                                <a href="{{ route('queue-monitor.failed.show', $job->id) }}"
                                   class="qp-btn qp-btn-sm">
                                    View
                                </a>

                                <form method="POST" onsubmit="return qpConfirm('You want to retry this failed job?', this, 'Yes! Retry', 'qp-btn-sm');"
                                      action="{{ route('queue-monitor.failed.retry', $job->id) }}"
                                      style="display:inline;">
                                    @csrf
                                    <button type="submit" class="qp-btn qp-btn-sm">
                                        Retry
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('queue-monitor.failed.destroy', $job->id) }}"
                                      onsubmit="return qpConfirm('Delete this failed job?', this, 'Delete', 'qp-btn-danger');"
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
                    <tr><td colspan="7" class="qp-empty">No results</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="qp-pagination">
            {{ $jobs->links() }}
        </div>
    </div>
@endsection