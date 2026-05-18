(function () {
    const select = document.getElementById('admin-status-select');
    const csrf = window.adminApplicationDetailConfig?.csrf;
    if (!select || !csrf) return;

    select.addEventListener('change', async () => {
        const url = select.dataset.url;
        select.disabled = true;
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ status: select.value }),
            });
            const data = await res.json();
            const root = document.getElementById('toast-root');
            if (root) {
                const el = document.createElement('div');
                el.className = 'resume-toast resume-toast--success resume-toast--visible';
                el.textContent = data.message || 'Updated';
                root.appendChild(el);
                setTimeout(() => el.remove(), 4000);
            }
        } catch {
            alert('Failed to update status');
        }
        select.disabled = false;
    });
})();
