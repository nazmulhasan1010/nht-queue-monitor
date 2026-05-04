@extends('queue-monitor::layout')

@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Failed Job #{{ $job->id }}</h1>
            <p class="qp-subtitle">{{ $jobName }}</p>
        </div>

        <div class="qp-actions">
            <a href="{{ route('queue-monitor.failed.index') }}" class="qp-btn">Back</a>

            @if(config('queue-monitor.ai.enabled', false))
                <button class="qp-btn qp-btn-primary" id="qp-analyze-ai" data-id="{{ $job->id }}">
                    <span class="qp-btn-text">AI Analysis</span>
                    <span class="qp-spinner d-none"></span>
                </button>
            @endif

            @if(config('queue-monitor.allow_retry', true))
                <button class="qp-btn" onclick="document.getElementById('qp-edit-modal').classList.remove('d-none')">
                    Edit & Retry
                </button>
                <form action="{{ route('queue-monitor.failed.retry', $job->id) }}" method="POST">
                    @csrf
                    <button class="qp-btn" type="submit">Retry Job</button>
                </form>
            @endif

            @if(config('queue-monitor.allow_delete', true))
                <form action="{{ route('queue-monitor.failed.destroy', $job->id) }}" method="POST"
                      onsubmit="return qpConfirm('Delete this failed job? This action cannot be undone.', this, 'Delete Job', 'qp-btn-danger');">
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
            <div class="qp-meta-row"><span>Connection</span><span>{{ $job->connection }}</span></div>
            <div class="qp-meta-row"><span>Queue</span><span>{{ $job->queue }}</span></div>
            <div class="qp-meta-row"><span>Attempts</span><span>{{ $attempts }}</span></div>
            <div class="qp-meta-row"><span>Failed At</span><span>{{ $job->failed_at }}</span></div>
        </div>

        <div class="qp-card">
            <div class="qp-card-head">
                <div>
                    <h2>Exception Preview</h2>
                    <p>Quick summary of the failure.</p>
                </div>
                <div class="qp-card-actions">
                    <button class="qp-btn qp-btn-sm" data-qp-copy="#qp-exception-preview">Copy</button>
                </div>
            </div>
            <div class="qp-code" id="qp-exception-preview">{{ $exceptionPreview }}</div>
        </div>
    </div>

    @if(config('queue-monitor.ai.enabled', false))
        <div class="qp-card d-none" id="qp-ai-analysis-card">
            <div class="qp-card-head">
                <div>
                    <h2>AI Analysis</h2>
                    <p>Smart diagnostic of the failure.</p>
                </div>
                <button class="qp-btn qp-btn-sm"
                        onclick="document.getElementById('qp-ai-analysis-card').classList.add('d-none')">Close
                </button>
            </div>
            <div class="qp-ai-content" id="qp-ai-content"></div>
        </div>
    @endif

    <div class="qp-card qp-tabs-card">
        <div class="qp-tabs">
            <button type="button" class="active" data-qp-tab-button="payload">Payload</button>
            <button type="button" data-qp-tab-button="exception">Full Exception</button>
            <button type="button" data-qp-tab-button="flow">Relationship</button>
            <button type="button" data-qp-tab-button="metadata">Metadata</button>
        </div>

        <div class="qp-tab-panel active" data-qp-tab-panel="payload">
            <div class="qp-panel-head">
                <h2>Payload JSON</h2>
                <button class="qp-btn qp-btn-sm" data-qp-copy="#qp-payload-json">Copy Payload</button>
            </div>
            <pre class="qp-code"
                 id="qp-payload-json">{{ json_encode($payload, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>

        <div class="qp-tab-panel" data-qp-tab-panel="exception">
            <div class="qp-panel-head">
                <h2>Full Exception</h2>
                <button class="qp-btn qp-btn-sm" data-qp-copy="#qp-full-exception">Copy Exception</button>
            </div>
            <pre class="qp-code qp-code-large" id="qp-full-exception">{{ $job->exception }}</pre>
        </div>

        <div class="qp-tab-panel" data-qp-tab-panel="flow">
            <div class="qp-panel-head">
                <h2>Job Batch Flow</h2>
                <p>Showing other jobs in the same batch/chain.</p>
            </div>

            @if($batchJobs->isNotEmpty())
                <div class="qp-flow-container">
                    @foreach($batchJobs as $bJob)
                        <div class="qp-flow-item {{ $bJob->status === 'failed' ? 'is-failed' : '' }} {{ $bJob->uuid === $uuid ? 'is-current' : '' }}">
                            <div class="qp-flow-status"></div>
                            <div class="qp-flow-content">
                                <div class="qp-flow-name">{{ $bJob->job_name }}</div>
                                <div class="qp-flow-meta">{{ $bJob->status }}
                                    • {{ $bJob->finished_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="qp-empty">
                    <p>No batch or chain relationships found for this job.</p>
                </div>
            @endif
        </div>

        <div class="qp-tab-panel" data-qp-tab-panel="metadata">
            <div class="qp-panel-head">
                <h2>Raw Metadata</h2>
                <button class="qp-btn qp-btn-sm" data-qp-copy="#qp-raw-metadata">Copy Metadata</button>
            </div>
            <pre class="qp-code" id="qp-raw-metadata">{{ json_encode([
                'id' => $job->id,
                'uuid' => $uuid,
                'connection' => $job->connection,
                'queue' => $job->queue,
                'job_name' => $jobName,
                'attempts' => $attempts,
                'failed_at' => $job->failed_at,
            ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </div>

    <div class="qp-editor-modal d-none" id="qp-edit-modal">
        <div class="qp-editor-modal-content qp-card">
            <div class="qp-card-head">
                <div>
                    <h2>Edit Job Payload</h2>
                    <p>Modify the JSON data before retrying the job.</p>
                </div>
                <button class="qp-btn qp-btn-sm"
                        onclick="document.getElementById('qp-edit-modal').classList.add('d-none')">Cancel
                </button>
            </div>

            <form action="{{ route('queue-monitor.failed.retry-with-data', $job->id) }}" method="POST">
                @csrf
                <textarea name="payload" class="qp-code qp-code-large"
                          style="width: 100%; min-height: 400px; margin-bottom: 20px;">{{ json_encode($payload, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</textarea>

                <div class="qp-actions" style="justify-content: flex-end;">
                    <button type="submit" class="qp-btn qp-btn-primary">Update & Retry Now</button>
                </div>
            </form>
        </div>
    </div>
@endsection
