<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Pulse</title>
    @queuePulseAssets
    @stack('head')
</head>
<body>
<div class="qp-shell">
    <aside class="qp-sidebar">
        <div class="qp-brand">
            <div class="qp-brand-icon">QP</div>
            <div>
                <div class="qp-brand-title">Queue Pulse</div>
                <div class="qp-brand-subtitle">Laravel queue monitor</div>
            </div>
        </div>

        <nav class="qp-nav">
            <a href="{{ route('queue-monitor.dashboard') }}"
               class="{{ request()->routeIs('queue-monitor.dashboard') ? 'active' : '' }}"><span>Dashboard</span><span>→</span></a>
            <a href="{{ route('queue-monitor.failed.index') }}"
               class="{{ request()->routeIs('queue-monitor.failed.*') ? 'active' : '' }}"><span>Failed Jobs</span><span>→</span></a>
            <a href="{{ route('queue-monitor.jobs.index') }}"
               class="{{ request()->routeIs('queue-monitor.jobs.*') ? 'active' : '' }}"><span>Jobs History</span><span>→</span></a>
            <a href="{{ route('queue-monitor.health.index') }}"
               class="{{ request()->routeIs('queue-monitor.health.*') ? 'active' : '' }}"><span>Health</span><span>→</span></a>
            <a href="{{ route('queue-monitor.alerts.index') }}"
               class="{{ request()->routeIs('queue-monitor.alerts.*') ? 'active' : '' }}"><span>Alerts</span><span>→</span></a>
            <a href="{{ route('queue-monitor.events.index') }}"
               class="{{ request()->routeIs('queue-monitor.events.*') ? 'active' : '' }}"><span>Audit Events</span><span>→</span></a>
            <a href="{{ route('queue-monitor.system.index') }}"
               class="{{ request()->routeIs('queue-monitor.system.*') ? 'active' : '' }}"><span>System</span><span>→</span></a>
            <a href="{{ route('queue-monitor.settings.index') }}"
               class="{{ request()->routeIs('queue-monitor.settings.*') ? 'active' : '' }}"><span>Settings</span><span>→</span></a>
        </nav>
    </aside>

    <main class="qp-main">
        @if(session('queue_monitor_success'))
            <div class="qp-toast">{{ session('queue_monitor_success') }}</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
