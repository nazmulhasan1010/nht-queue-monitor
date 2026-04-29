
@extends('queue-monitor::layout')

@section('content')
<div class="qp-header">
    <div>
        <h1 class="qp-title">System</h1>
        <p class="qp-subtitle">Node, tenant, broadcasting, and Redis queue metrics.</p>
    </div>
</div>

<div class="qp-detail-grid">
    <div class="qp-card">
        <h2>Node</h2>
        <div class="qp-meta-row"><span>Name</span><span>{{ $node['name'] ?? '-' }}</span></div>
        <div class="qp-meta-row"><span>Environment</span><span>{{ $node['environment'] ?? '-' }}</span></div>
    </div>

    <div class="qp-card">
        <h2>Broadcasting</h2>
        <div class="qp-meta-row"><span>Enabled</span><span>{{ data_get($broadcasting, 'enabled') ? 'Yes' : 'No' }}</span></div>
        <div class="qp-meta-row"><span>Channel</span><span>{{ data_get($broadcasting, 'channel') }}</span></div>
    </div>
</div>

<div class="qp-detail-grid" style="margin-top:16px;">
    <div class="qp-card">
        <h2>Tenant Mode</h2>
        <div class="qp-meta-row"><span>Enabled</span><span>{{ data_get($tenant, 'enabled') ? 'Yes' : 'No' }}</span></div>
        <div class="qp-meta-row"><span>Resolver</span><span>{{ data_get($tenant, 'resolver') ? 'Configured' : 'Not configured' }}</span></div>
    </div>

    <div class="qp-card">
        <h2>AI Failure Insight</h2>
        <p class="qp-subtitle">Rule-based insight helper is active for failed tracked jobs.</p>
    </div>
</div>

<div class="qp-card" style="margin-top:16px;">
    <h2>Redis Queue Metrics</h2>
    <div class="qp-table-wrap">
        <table class="qp-table">
            <thead>
                <tr>
                    <th>Queue</th>
                    <th>Pending</th>
                    <th>Delayed</th>
                    <th>Reserved</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($redisMetrics as $row)
                    <tr>
                        <td>{{ $row['queue'] }}</td>
                        <td>{{ $row['pending'] ?? '-' }}</td>
                        <td>{{ $row['delayed'] ?? '-' }}</td>
                        <td>{{ $row['reserved'] ?? '-' }}</td>
                        <td>{{ $row['available'] ? 'Available' : 'Unavailable' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
