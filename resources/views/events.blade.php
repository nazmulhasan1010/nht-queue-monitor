@extends('queue-monitor::layout')

@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Audit Events</h1>
            <p class="qp-subtitle">Track retry, delete, clear, and bulk actions.</p>
        </div>

        @if(config('queue-monitor.allow_export', true))
            <a class="qp-btn" href="{{ route('queue-monitor.exports.events') }}">Export CSV</a>
        @endif
    </div>

    <div class="qp-card">
        <div class="qp-table-wrap">
            <table class="qp-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Event</th>
                    <th>Job ID</th>
                    <th>Queue</th>
                    <th>Performed By</th>
                    <th>Time</th>
                </tr>
                </thead>
                <tbody>
                @forelse($events as $event)
                    <tr>
                        <td>#{{ $event->id }}</td>
                        <td><span class="qp-badge">{{ $event->event_type }}</span></td>
                        <td>{{ $event->job_id ?? '-' }}</td>
                        <td>{{ $event->queue ?? '-' }}</td>
                        <td>{{ $event->performed_by ?? '-' }}</td>
                        <td>{{ $event->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="qp-empty">No audit events yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="qp-pagination">{{ $events->links() }}</div>
    </div>
@endsection
