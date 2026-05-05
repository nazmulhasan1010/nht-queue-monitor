<aside class="qp-sidebar">
    <div class="qp-brand">
        <a href="{{ route('queue-monitor.dashboard') }}">
            <img src="{{ asset('vendor/nht/queue-monitor/logos/nht-qm.png') }}" alt="Queue Pulse"
                 class="qp-brand-logo">
        </a>
    </div>

    <nav class="qp-nav">
        <a href="{{ route('queue-monitor.dashboard') }}"
           class="{{ request()->routeIs('queue-monitor.dashboard') ? 'active' : '' }}"><span>Dashboard</span><span>→</span></a>
        <a href="{{ route('queue-monitor.failed.index') }}"
           class="{{ request()->routeIs('queue-monitor.failed.*') ? 'active' : '' }}"><span>Failed Jobs</span><span>→</span></a>
        <a href="{{ route('queue-monitor.pending.index') }}"
           class="{{ request()->routeIs('queue-monitor.pending.*') ? 'active' : '' }}"><span>Pending Jobs</span><span>→</span></a>
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