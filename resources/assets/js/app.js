
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

    // AI Analysis
    const aiBtn = document.getElementById('qp-analyze-ai');
    if (aiBtn) {
        aiBtn.addEventListener('click', async () => {
            const jobId = aiBtn.dataset.id;
            const card = document.getElementById('qp-ai-analysis-card');
            const content = document.getElementById('qp-ai-content');
            const spinner = aiBtn.querySelector('.qp-spinner');
            const btnText = aiBtn.querySelector('.qp-btn-text');

            card.classList.remove('d-none');
            content.innerHTML = '<p class="qp-loading-text">Analyzing failure with AI, please wait...</p>';
            aiBtn.disabled = true;
            spinner.classList.remove('d-none');
            btnText.classList.add('d-none');

            try {
                const response = await fetch(`/queue-monitor/failed/${jobId}/analyze`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                
                if (data.analysis) {
                    // Using a simple markdown formatter or just pre-wrap
                    content.innerHTML = `<div style="white-space: pre-wrap;">${data.analysis}</div>`;
                } else {
                    content.innerHTML = '<p class="qp-error-text">Failed to get AI analysis.</p>';
                }
            } catch (error) {
                console.error('AI Analysis Error:', error);
                content.innerHTML = '<p class="qp-error-text">An error occurred during analysis.</p>';
            } finally {
                aiBtn.disabled = false;
                spinner.classList.add('d-none');
                btnText.classList.remove('d-none');
            }
        });
    }
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
