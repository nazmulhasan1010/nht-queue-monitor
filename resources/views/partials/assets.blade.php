@if(config('queue-monitor.assets.use_built_assets', true))
    <link rel="stylesheet" href="{{ asset(config('queue-monitor.assets.css')) }}">
    <script defer src="{{ asset(config('queue-monitor.assets.js')) }}"></script>
@endif
