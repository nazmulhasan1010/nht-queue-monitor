@extends('queue-monitor::layout')
@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Queue Health</h1>
            <p class="qp-subtitle">Queue failure score, distribution, and heatmap.</p>
        </div>
    </div>

    <div class="qp-grid">
        <div class="qp-card">
            <div class="qp-stat-label">Health Score</div>
            <div class="qp-stat-value">{{ $summary['score'] }}</div>
            <span class="qp-badge">{{ strtoupper($summary['status']) }}</span>
        </div>

        <div class="qp-card">
            <div class="qp-stat-label">Total Failed</div>
            <div class="qp-stat-value">{{ number_format($summary['total_failed']) }}</div>
        </div>

        <div class="qp-card">
            <div class="qp-stat-label">Failed Last 24h</div>
            <div class="qp-stat-value">{{ number_format($summary['failed_last_24h']) }}</div>
        </div>

        <div class="qp-card">
            <div class="qp-stat-label">Failed Last Hour</div>
            <div class="qp-stat-value">{{ number_format($summary['failed_last_hour']) }}</div>
        </div>
    </div>

    <div class="qp-detail-grid">
        <div class="qp-card">
            <h2>Queue Distribution</h2>
            <div class="qp-table-wrap">
                <table class="qp-table">
                    <thead>
                    <tr>
                        <th>Queue</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($queues as $row)
                        <tr>
                            <td><span class="qp-badge">{{ $row->queue ?: 'default' }}</span></td>
                            <td>{{ $row->total }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="qp-empty">No queue failures.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="qp-card">
            <h2>Connection Distribution</h2>
            <div class="qp-table-wrap">
                <table class="qp-table">
                    <thead>
                    <tr>
                        <th>Connection</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($connections as $row)
                        <tr>
                            <td>{{ $row->connection ?: 'unknown' }}</td>
                            <td>{{ $row->total }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="qp-empty">No connection failures.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="qp-card" style="margin-top:16px;">
        <h2>Hourly Failure Heatmap</h2>
        <p class="qp-subtitle">Failures grouped by hour from the last 7 days.</p>

        <div style="display:grid;grid-template-columns:repeat(24,minmax(32px,1fr));gap:8px;margin-top:16px;">
            @php
                $map = collect($heatmap)->pluck('total', 'hour');
                $max = max(1, (int) collect($heatmap)->max('total'));
            @endphp

            @for($i = 0; $i < 24; $i++)
                @php
                    $total = (int) ($map[$i] ?? 0);
                    $opacity = 0.15 + (($total / $max) * 0.85);
                @endphp
                <div title="{{ $i }}:00 - {{ $total }} failures"
                     style="height:54px;border-radius:14px;background:rgba(255,62,165,{{ $opacity }});display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;">
                    {{ $i }}
                </div>
            @endfor
        </div>
    </div>
@endsection
