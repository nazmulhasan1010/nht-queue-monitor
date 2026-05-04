@extends('queue-monitor::layout')

@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Job History #{{ $job->id }}</h1>
            <p class="qp-subtitle">{{ $job->job_name }}</p>
        </div>

        <a class="qp-btn" href="{{ route('queue-monitor.jobs.index') }}">Back</a>
    </div>

    <div class="qp-detail-grid">
        <div class="qp-card">
            <h2>Job Information</h2>
            <div class="qp-meta-row"><span>Status</span><span>{{ strtoupper($job->status) }}</span></div>
            <div class="qp-meta-row"><span>UUID</span><span class="qp-mono">{{ $job->uuid ?: '-' }}</span></div>
            <div class="qp-meta-row"><span>Connection</span><span>{{ $job->connection ?: '-' }}</span></div>
            <div class="qp-meta-row"><span>Queue</span><span>{{ $job->queue ?: '-' }}</span></div>
            <div class="qp-meta-row"><span>Node</span><span>{{ $job->node_name ?: '-' }}</span></div>
            <div class="qp-meta-row"><span>Tenant</span><span>{{ $job->tenant_id ?: '-' }}</span></div>
            <div class="qp-meta-row"><span>Attempts</span><span>{{ $job->attempts ?? '-' }}</span></div>
            <div class="qp-meta-row">
                <span>Duration</span>
                <span>{{ $job->duration_ms ? $job->duration_ms . ' ms' : '-' }}</span>
            </div>
            <div class="qp-meta-row"><span>Finished At</span><span>{{ $job->finished_at ?: '-' }}</span></div>
        </div>

        <div class="qp-card">
            <h2>Insight</h2>
            <p class="qp-subtitle">{{ $job->insight ?: 'No insight generated for this job.' }}</p>
        </div>
    </div>

    <div class="qp-card" style="margin-top:16px;">
        <h2>Payload</h2>
        <pre class="qp-code">{{ json_encode($job->payload, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>

    @if($job->exception)
        <div class="qp-card" style="margin-top:16px;">
            <h2>Exception</h2>
            <pre class="qp-code qp-code-large">{{ $job->exception }}</pre>
        </div>
    @endif
@endsection
