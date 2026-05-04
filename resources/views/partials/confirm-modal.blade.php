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
    background: rgba(0, 0, 0, 0.85);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(12px);
}
.qp-modal-overlay .qp-modal {
    background: linear-gradient(180deg, #1d1d38f2, #17172df2);
    border: 1px solid rgba(255, 77, 166, 0.25);
    border-radius: 24px;
    width: 95%;
    max-width: 440px;
    padding: 32px;
    box-shadow: 0 40px 100px rgba(0, 0, 0, 0.7);
    animation: qp-modal-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes qp-modal-in {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.qp-modal-header h3 {
    margin: 0 0 16px 0;
    color: #ffffff;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -0.02em;
}
.qp-modal-body p {
    margin: 0 0 32px 0;
    color: #a5a3b8;
    line-height: 1.7;
    font-size: 1.05rem;
}
.qp-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 16px;
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
