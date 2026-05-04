<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Pulse</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('vendor/nht/queue-monitor/logos/nht.png') }}">
    @queuePulseAssets
    @stack('head')
</head>
<body>
<div class="qp-shell">
    @include(('queue-monitor::partials.aside'))
    <main class="qp-main">
        @if(session('queue_monitor_success'))
            <div class="qp-toast">{{ session('queue_monitor_success') }}</div>
        @endif

        @yield('content')
    </main>
</div>

@include('queue-monitor::partials.confirm-modal')

</body>
</html>
