@extends('queue-monitor::layout')

@section('content')
<div class="qp-header">
    <div>
        <h1 class="qp-title">Failed Jobs</h1>
        <p class="qp-subtitle">Filter, search, inspect, retry, delete, and export failed jobs.</p>
    </div>

    <div class="qp-actions">
        @if(config('queue-monitor.allow_export', true))
            <a class="qp-btn" href="{{ route('queue-monitor.exports.failed-jobs') }}">Export CSV</a>
        @endif

        @if(config('queue-monitor.allow_retry', true))
            <form method="POST" action="{{ route('queue-monitor.failed.retry-all') }}" data-qp-danger-form data-confirm-phrase="RETRY ALL">
                @csrf
                <button class="qp-btn" type="submit">Retry All</button>
            </form>
        @endif

        @if(config('queue-monitor.allow_clear', true))
            <form method="POST" action="{{ route('queue-monitor.failed.clear') }}" data-qp-danger-form data-confirm-phrase="CLEAR ALL">
                @csrf
                @method('DELETE')
                <button class="qp-btn qp-btn-danger" type="submit">Clear All</button>
            </form>
        @endif
    </div>
</div>

<div class="qp-card">
    <p class="qp-subtitle">Phase 6 override: keep your Phase 4 table/filter body below this header if needed.</p>
</div>
@endsection
