@extends('queue-monitor::layout')

@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Pending Job #{{ $job->id }}</h1>
            <p class="qp-subtitle">{{ $jobName }}</p>
        </div>

        <div class="qp-actions">
            <a href="{{ route('queue-monitor.pending.index') }}" class="qp-btn">Back</a>

            @if(config('queue-monitor.allow_run', true))
                <form action="{{ route('queue-monitor.pending.run', $job->id) }}" method="POST"
                      onsubmit="return qpConfirm('Run this job manually now?', this, 'Run Now', 'qp-btn-primary');">
                    @csrf
                    <button class="qp-btn qp-btn-primary" type="submit">Run Now</button>
                </form>
            @endif

            @if(config('queue-monitor.allow_pending_delete', true))
                <form action="{{ route('queue-monitor.pending.destroy', $job->id) }}" method="POST"
                      onsubmit="return qpConfirm('Delete this pending job?', this, 'Delete Job', 'qp-btn-danger');">
                    @csrf
                    @method('DELETE')
                    <button class="qp-btn qp-btn-danger" type="submit">Delete</button>
                </form>
            @endif
        </div>
    </div>

    <div class="qp-detail-grid">
        <div class="qp-card">
            <h2>Job Information</h2>
            <div class="qp-meta-row"><span>ID</span><span>#{{ $job->id }}</span></div>
            <div class="qp-meta-row"><span>UUID</span><span class="qp-mono">{{ $uuid }}</span></div>
            <div class="qp-meta-row"><span>Queue</span><span>{{ $job->queue }}</span></div>
            <div class="qp-meta-row"><span>Attempts</span><span>{{ $job->attempts }}</span></div>
            <div class="qp-meta-row"><span>Available At</span><span>{{ \Carbon\Carbon::createFromTimestamp($job->available_at)->toDateTimeString() }}</span></div>
            <div class="qp-meta-row"><span>Created At</span><span>{{ \Carbon\Carbon::createFromTimestamp($job->created_at)->toDateTimeString() }}</span></div>
        </div>

        <div class="qp-card">
            <div class="qp-card-head">
                <div>
                    <h2>Payload Preview</h2>
                    <p>Brief look at the job data.</p>
                </div>
            </div>
            <div class="qp-code" id="qp-payload-preview">{{ Str::limit(json_encode($payload), 500) }}</div>
        </div>
    </div>

    <div class="qp-card qp-tabs-card">
        <div class="qp-tabs">
            <button type="button" class="active" data-qp-tab-button="payload">Full Payload</button>
            <button type="button" data-qp-tab-button="metadata">Metadata</button>
        </div>

        <div class="qp-tab-panel active" data-qp-tab-panel="payload">
            <div class="qp-panel-head">
                <h2>Payload JSON</h2>
                <button class="qp-btn qp-btn-sm" data-qp-copy="#qp-payload-json">Copy Payload</button>
            </div>
            <pre class="qp-code" id="qp-payload-json">{{ json_encode($payload, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>

        <div class="qp-tab-panel" data-qp-tab-panel="metadata">
            <div class="qp-panel-head">
                <h2>Raw Metadata</h2>
                <button class="qp-btn qp-btn-sm" data-qp-copy="#qp-raw-metadata">Copy Metadata</button>
            </div>
            <pre class="qp-code" id="qp-raw-metadata">{{ json_encode([
                'id' => $job->id,
                'uuid' => $uuid,
                'queue' => $job->queue,
                'job_name' => $jobName,
                'attempts' => $job->attempts,
                'available_at' => $job->available_at,
                'created_at' => $job->created_at,
            ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </div>
@endsection
