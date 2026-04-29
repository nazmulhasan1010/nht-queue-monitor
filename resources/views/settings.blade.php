@extends('queue-monitor::layout')
@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Settings</h1>
            <p class="qp-subtitle">Current Queue Pulse configuration.</p>
        </div>
    </div>

    <div class="qp-detail-grid">
        <div class="qp-card">
            <h2>Access Control</h2>
            <div class="qp-meta-row">
                <span>Gate Enabled</span><span>{{ data_get($config, 'access.enable_gate') ? 'Yes' : 'No' }}</span></div>
            <div class="qp-meta-row"><span>Gate Name</span><span>{{ data_get($config, 'access.gate') }}</span></div>
            <div class="qp-meta-row">
                <span>Allowed Emails</span><span>{{ implode(', ', data_get($config, 'access.allowed_emails', [])) ?: '-' }}</span>
            </div>
        </div>

        <div class="qp-card">
            <h2>Exports</h2>
            <div class="qp-meta-row">
                <span>CSV Export</span><span>{{ data_get($config, 'allow_export') ? 'Enabled' : 'Disabled' }}</span>
            </div>
            <div class="qp-meta-row"><span>Failed Jobs</span><span><a
                            href="{{ route('queue-monitor.exports.failed-jobs') }}">Download</a></span></div>
            <div class="qp-meta-row"><span>Audit Events</span><span><a
                            href="{{ route('queue-monitor.exports.events') }}">Download</a></span></div>
        </div>
    </div>

    <div class="qp-card" style="margin-top:16px;">
        <h2>Access .env Example</h2>
        <pre class="qp-code">QUEUE_MONITOR_ALLOWED_EMAILS=admin@example.com,dev@example.com QUEUE_MONITOR_ENABLE_GATE=true</pre>
    </div>
@endsection
