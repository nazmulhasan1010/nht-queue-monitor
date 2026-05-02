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
        <div class="qp-actions">
            <input class="nht-queue form-input"
                   name="q"
                   placeholder="Search job, UUID, exception..."
                   value="{{ $filters['q'] ?? '' }}"/>

            <select class="nht-queue form-input" name="queue">
                <option value="">All Queues</option>
                @foreach($queues as $q)
                    <option value="{{ $q }}" @selected(($filters['queue'] ?? '') === $q)>
                        {{ $q }}
                    </option>
                @endforeach
            </select>

            <select class="nht-queue form-input" name="connection">
                <option value="">All Connections</option>
                @foreach($connections as $c)
                    <option value="{{ $c }}" @selected(($filters['connection'] ?? '') === $c)>
                        {{ $c }}
                    </option>
                @endforeach
            </select>

            <input class="nht-queue form-input"
                   type="date"
                   name="from"
                   value="{{ $filters['from'] ?? '' }}"/>

            <input class="nht-queue form-input"
                   type="date"
                   name="to"
                   value="{{ $filters['to'] ?? '' }}"/>

            <button type="submit" class="qp-btn">Apply</button>

            <a href="{{ route('queue-monitor.failed.index') }}" class="qp-btn qp-btn-secondary">
                Reset
            </a>
        </div>
    </form>

    <form method="POST"
          action="{{ route('queue-monitor.failed.bulk-destroy') }}"
          onsubmit="return qpConfirm('Delete selected failed jobs?', this, 'Delete Selected', 'qp-btn-danger');">
        @csrf
        @method('DELETE')

        <div class="qp-card">
            <div class="qp-card-head" style="margin-bottom:16px;">
                <div>
                    <h2 style="margin:0;">Failed Jobs Table</h2>
                    <p class="qp-subtitle">Select jobs for bulk action</p>
                </div>

                <div class="qp-actions">
                    <button type="submit" class="qp-btn qp-btn-danger">
                        Delete Selected
                    </button>
                </div>
            </div>

            <div class="qp-table-wrap">
                <table class="qp-table">
                    <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="checkAll">
                        </th>
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
                            $payload = json_decode($job->payload ?? '{}', true);
                            $jobName = $payload['displayName']
                                ?? $payload['job']
                                ?? data_get($payload, 'data.commandName')
                                ?? 'Unknown Job';

                            $uuid = $job->uuid ?? ($payload['uuid'] ?? 'N/A');
                        @endphp

                        <tr>
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $job->id }}" class="job-checkbox">
                            </td>

                            <td>#{{ $job->id }}</td>

                            <td class="qp-mono">
                                {{ $uuid }}
                            </td>

                            <td>
                                <span class="qp-badge">{{ $job->queue }}</span>
                            </td>

                            <td>{{ $job->connection }}</td>

                            <td>{{ $jobName }}</td>

                            <td>{{ $job->failed_at }}</td>

                            <td>
                                <div class="qp-actions">
                                    <a href="{{ route('queue-monitor.failed.show', $job->id) }}"
                                       class="qp-btn qp-btn-sm">
                                        View
                                    </a>

                                    <form method="POST"
                                          action="{{ route('queue-monitor.failed.retry', $job->id) }}">
                                        @csrf
                                        <button type="submit" class="qp-btn qp-btn-sm">
                                            Retry
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('queue-monitor.failed.destroy', $job->id) }}"
                                          onsubmit="return qpConfirm('Delete this failed job?', this, 'Delete', 'qp-btn-danger');">
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
                        <tr>
                            <td colspan="8" class="qp-empty">No results</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="qp-pagination">
                {{ $jobs->links() }}
            </div>
        </div>
    </form>

    <script>
        document.getElementById('checkAll')?.addEventListener('change', function () {
            document.querySelectorAll('.job-checkbox').forEach(function (checkbox) {
                checkbox.checked = event.target.checked;
            });
        });
    </script>
@endsection