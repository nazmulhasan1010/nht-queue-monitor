@extends('queue-monitor::layout')
@section('content')
    <div class="qp-header">
        <h1 class="qp-title">Live Queue Monitor</h1>

        <button id="qp-live-toggle" class="qp-btn">Live: OFF</button>
    </div>

    <div class="qp-card">
        <h2>Live Failed Jobs Feed</h2>

        <div id="qp-live-feed" style="margin-top:10px;"></div>
    </div>
@endsection
