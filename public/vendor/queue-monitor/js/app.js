let i = !1;
document.addEventListener("DOMContentLoaded", () => {
    const a = document.getElementById("qp-live-toggle");
    a && a.addEventListener("click", () => {
        i = !i, a.innerText = i ? "Live: ON" : "Live: OFF"
    }), setInterval(async () => {
        if (i) try {
            const n = await (await fetch("/api/queue-monitor/live")).json();
            l(n)
        } catch {
        }
    }, 5e3);
    const e = document.getElementById("qp-analyze-ai");
    e && e.addEventListener("click", async () => {
        const t = e.dataset.id, n = document.getElementById("qp-ai-analysis-card"),
            s = document.getElementById("qp-ai-content"), r = e.querySelector(".qp-spinner"),
            o = e.querySelector(".qp-btn-text");
        n.classList.remove("d-none"), s.innerHTML = '<p class="qp-loading-text">Analyzing failure with AI, please wait...</p>', e.disabled = !0, r.classList.remove("d-none"), o.classList.add("d-none");
        try {
            const d = await (await fetch(`/queue-monitor/failed/${t}/analyze`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    Accept: "application/json"
                }
            })).json();
            d.analysis ? s.innerHTML = `<div style="white-space: pre-wrap;">${d.analysis}</div>` : s.innerHTML = '<p class="qp-error-text">Failed to get AI analysis.</p>'
        } catch (c) {
            console.error("AI Analysis Error:", c), s.innerHTML = '<p class="qp-error-text">An error occurred during analysis.</p>'
        } finally {
            e.disabled = !1, r.classList.add("d-none"), o.classList.remove("d-none")
        }
    })
});

function l(a) {
    const e = document.getElementById("qp-live-feed");
    e && (e.innerHTML = "", a.failed_jobs.forEach(t => {
        const n = document.createElement("div");
        n.className = "qp-live-item", n.innerText = `#${t.id} ${t.queue} (${t.failed_at})`, e.appendChild(n)
    }))
}
