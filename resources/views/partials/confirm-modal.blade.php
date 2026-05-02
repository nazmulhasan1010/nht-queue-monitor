<div id="qp-confirm-modal" class="qp-modal-overlay" style="display: none;">
    <div class="qp-modal">
        <div class="qp-modal-header">
            <h3 id="qp-confirm-title">Confirm Action</h3>
        </div>
        <div class="qp-modal-body">
            <p id="qp-confirm-message">Are you sure you want to proceed?</p>
        </div>
        <div class="qp-modal-footer">
            <button type="button" class="qp-btn qp-btn-secondary" onclick="qpCloseConfirm()">Cancel</button>
            <button type="button" class="qp-btn qp-btn-danger" id="qp-confirm-btn">Confirm Action</button>
        </div>
    </div>
</div>

<style>
.qp-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
}
.qp-modal {
    background: #1e1e2e;
    border: 1px solid #313244;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    padding: 24px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.4);
    animation: qp-modal-in 0.2s ease-out;
}
@keyframes qp-modal-in {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.qp-modal-header h3 {
    margin: 0 0 12px 0;
    color: #f5e0dc;
    font-size: 1.25rem;
}
.qp-modal-body p {
    margin: 0 0 24px 0;
    color: #a6adc8;
    line-height: 1.6;
    font-size: 0.95rem;
}
.qp-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}
</style>

<script>
let qpPendingForm = null;

function qpConfirm(message, form, btnText = 'Confirm Action', btnClass = 'qp-btn-danger') {
    const modal = document.getElementById('qp-confirm-modal');
    const msgEl = document.getElementById('qp-confirm-message');
    const btnEl = document.getElementById('qp-confirm-btn');
    
    msgEl.innerText = message;
    btnEl.innerText = btnText;
    
    // Reset button classes
    btnEl.className = 'qp-btn ' + btnClass;
    
    modal.style.display = 'flex';
    qpPendingForm = form;
    return false;
}

function qpCloseConfirm() {
    document.getElementById('qp-confirm-modal').style.display = 'none';
    qpPendingForm = null;
}

document.getElementById('qp-confirm-btn')?.addEventListener('click', function() {
    if (qpPendingForm) {
        qpPendingForm.submit();
    }
    qpCloseConfirm();
});

// Close on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') qpCloseConfirm();
});
</script>
