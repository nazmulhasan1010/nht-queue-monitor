@extends('queue-monitor::layout')

@section('content')
    <div class="qp-header">
        <div>
            <h1 class="qp-title">Live Queue Monitor</h1>
            <p class="qp-subtitle">Real-time overview of your application's queue health.</p>
        </div>

        <div class="qp-actions">
            <button id="qp-live-toggle" class="qp-btn qp-btn-secondary">Live Feed: OFF</button>
            <a href="{{ route('queue-monitor.failed.index') }}" class="qp-btn">Manage Failed Jobs</a>
        </div>
    </div>

    <div class="qp-grid">
        <div class="qp-card">
            <div class="qp-stat-label">Total Failed Jobs</div>
            <div class="qp-stat-value">{{ number_format($stats['total_failed'] ?? 0) }}</div>
        </div>
        <div class="qp-card">
            <div class="qp-stat-label">Failed Today</div>
            <div class="qp-stat-value">{{ number_format($stats['failed_today'] ?? 0) }}</div>
        </div>
        <div class="qp-card">
            <div class="qp-stat-label">Failed This Week</div>
            <div class="qp-stat-value">{{ number_format($stats['failed_this_week'] ?? 0) }}</div>
        </div>
        <div class="qp-card">
            <div class="qp-stat-label">Latest Failure</div>
            <div class="qp-stat-value qp-stat-date" style="font-size: 18px; margin-top: 15px;">
                {{ $stats['latest_failed_at'] ? \Illuminate\Support\Carbon::parse($stats['latest_failed_at'])->diffForHumans() : 'N/A' }}
            </div>
        </div>
    </div>

    <div class="qp-grid" style="grid-template-columns: 2fr 1fr;">
        <div class="qp-card">
            <div class="qp-card-head">
                <h2>Recent Failed Jobs</h2>
                <a href="{{ route('queue-monitor.failed.index') }}" style="font-size: 13px; color: #ff3ea5;">View All
                    Failed Jobs →</a>
            </div>

            <div class="qp-table-wrap">
                <table class="qp-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Job Name</th>
                        <th>Queue</th>
                        <th>Failed At</th>
                    </tr>
                    </thead>
                    <tbody id="qp-dashboard-recent">
                    @forelse($latestFailedJobs as $job)
                        @php
                            $payload = json_decode($job->payload ?? '{}', true, 512, JSON_THROW_ON_ERROR);
                            $jobName = $payload['displayName'] ?? $payload['job'] ?? 'Unknown';
                        @endphp
                        <tr>
                            <td class="qp-mono">#{{ $job->id }}</td>
                            <td>{{ $jobName }}</td>
                            <td><span class="qp-badge">{{ $job->queue }}</span></td>
                            <td>{{ $job->failed_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="qp-empty">No failed jobs recorded yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="qp-card">
            <div class="qp-card-head">
                <h2>Live Feed</h2>
            </div>
            <div id="qp-live-feed" class="qp-live-container">
                <div class="qp-empty">Live feed is currently disabled. Toggle the switch at the top to start
                    monitoring.
                </div>
            </div>
        </div>
    </div>

    <script>
        let liveEnabled = false;
        let liveInterval = null;

        document.getElementById('qp-live-toggle').addEventListener('click', function () {
            liveEnabled = !liveEnabled;
            this.innerText = liveEnabled ? 'Live Feed: ON' : 'Live Feed: OFF';
            this.classList.toggle('qp-btn-secondary', !liveEnabled);

            if (liveEnabled) {
                startLive();
            } else {
                stopLive();
            }
        });

        function startLive() {
            const container = document.getElementById('qp-live-feed');
            container.innerHTML = '<div class="qp-empty">Initializing live feed...</div>';
            liveInterval = setInterval(fetchLive, 4000);
            fetchLive();
        }

        function stopLive() {
            clearInterval(liveInterval);
            document.getElementById('qp-live-feed').innerHTML = '<div class="qp-empty">Live feed is currently disabled. Toggle the switch at the top to start monitoring.</div>';
        }

        async function fetchLive() {
            try {
                const res = await fetch('{{ route('queue-monitor.api.live') }}');
                const data = await res.json();

                const container = document.getElementById('qp-live-feed');

                if (data.failed_jobs.length === 0) {
                    container.innerHTML = '<div class="qp-empty">No recent failed jobs.</div>';
                    return;
                }

                const html = data.failed_jobs.map(job => {
                    const payload = JSON.parse(job.payload || '{}');
                    const name = payload.displayName || payload.job || 'Unknown';
                    const date = new Date(job.failed_at).toLocaleTimeString();

                    return ` <div class="qp-live-item">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <strong style="color:#f8f7ff;">${name}</strong>
                                <span class="qp-live-badge">#${job.id}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:11px; color:#a5a3b8;">
                                <span>${job.queue}</span>
                                <span>${date}</span>
                            </div>
                        </div>`;
                }).join('');

                if (container.innerHTML !== html) {
                    container.innerHTML = html;
                }
            } catch (e) {
                console.error('Live fetch failed', e);
            }
        }
    </script>
@endsection
