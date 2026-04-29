
let qpLiveMode = false;

document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.getElementById('qp-live-toggle');

    if (toggle) {
        toggle.addEventListener('click', () => {
            qpLiveMode = !qpLiveMode;
            toggle.innerText = qpLiveMode ? 'Live: ON' : 'Live: OFF';
        });
    }

    setInterval(async () => {
        if (!qpLiveMode) return;

        try {
            const res = await fetch('/api/queue-monitor/live');
            const data = await res.json();

            updateLiveFeed(data);
        } catch (e) {}
    }, 5000);
});

function updateLiveFeed(data) {
    const container = document.getElementById('qp-live-feed');
    if (!container) return;

    container.innerHTML = '';

    data.failed_jobs.forEach(job => {
        const div = document.createElement('div');
        div.className = 'qp-live-item';
        div.innerText = `#${job.id} ${job.queue} (${job.failed_at})`;
        container.appendChild(div);
    });
}
