(function () {
    const cfg = window.adminApplicationsConfig;
    if (!cfg) return;

    function toast(msg, ok = true) {
        const root = document.getElementById('toast-root');
        if (!root) return;
        const el = document.createElement('div');
        el.className = ok ? 'resume-toast resume-toast--success resume-toast--visible' : 'resume-toast resume-toast--error resume-toast--visible';
        el.textContent = msg;
        root.appendChild(el);
        setTimeout(() => el.remove(), 4000);
    }

    document.querySelectorAll('.application-status-select').forEach((select) => {
        select.addEventListener('change', async () => {
            const id = select.dataset.applicationId;
            const url = cfg.statusUrlTemplate.replace('__ID__', id);
            select.disabled = true;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrf,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ status: select.value }),
                });
                const data = await res.json();
                toast(data.message || 'Status updated', data.success);
            } catch {
                toast('Update failed', false);
            }
            select.disabled = false;
        });
    });
})();
