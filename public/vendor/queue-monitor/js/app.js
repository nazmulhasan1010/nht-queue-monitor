document.addEventListener('DOMContentLoaded', () => {
    const checkAll = document.querySelector('[data-qp-check-all]');
    const checkItems = document.querySelectorAll('[data-qp-check-item]');

    if (checkAll) {
        checkAll.addEventListener('change', () => {
            checkItems.forEach((item) => {
                item.checked = checkAll.checked;
            });
        });
    }

    document.querySelectorAll('[data-qp-tab-button]').forEach((button) => {
        button.addEventListener('click', () => {
            const tabName = button.getAttribute('data-qp-tab-button');

            document.querySelectorAll('[data-qp-tab-button]').forEach((btn) => {
                btn.classList.remove('active');
            });

            document.querySelectorAll('[data-qp-tab-panel]').forEach((panel) => {
                panel.classList.remove('active');
            });

            button.classList.add('active');

            const target = document.querySelector(`[data-qp-tab-panel="${tabName}"]`);
            if (target) {
                target.classList.add('active');
            }
        });
    });

    document.querySelectorAll('[data-qp-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const selector = button.getAttribute('data-qp-copy');
            const target = document.querySelector(selector);

            if (!target) return;

            const text = target.innerText || target.textContent || '';

            try {
                await navigator.clipboard.writeText(text);
                const originalText = button.innerText;
                button.innerText = 'Copied';
                setTimeout(() => {
                    button.innerText = originalText;
                }, 1200);
            } catch (error) {
                alert('Copy failed. Please copy manually.');
            }
        });
    });
});
