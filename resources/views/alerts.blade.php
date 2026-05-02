@extends('queue-monitor::layout')
@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Queue Alerts</h1>
            <p class="qp-subtitle">Threshold-based queue failure alerts.</p>
        </div>

        <form method="POST" action="{{ route('queue-monitor.alerts.check') }}">
            @csrf
            <button class="qp-btn">Run Alert Check</button>
        </form>
    </div>

    <div class="qp-card">
        <div class="qp-table-wrap">
            <table class="qp-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Level</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                @forelse($alerts as $alert)
                    <tr>
                        <td>#{{ $alert->id }}</td>
                        <td><span class="qp-badge">{{ strtoupper($alert->level) }}</span></td>
                        <td>{{ $alert->title }}</td>
                        <td>{{ $alert->message }}</td>
                        <td>{{ $alert->resolved_at ? 'Resolved' : 'Active' }}</td>
                        <td>{{ $alert->created_at }}</td>
                        <td>
                            @unless($alert->resolved_at)
                                <form method="POST" action="{{ route('queue-monitor.alerts.resolve', $alert->id) }}"
                                      onsubmit="return qpConfirm('Mark this alert as resolved?', this, 'Resolve', 'qp-btn-secondary');">
                                    @csrf
                                    <button class="qp-btn qp-btn-sm">Resolve</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="qp-empty">No alerts found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="qp-pagination">{{ $alerts->links() }}</div>
    </div>
@endsection
