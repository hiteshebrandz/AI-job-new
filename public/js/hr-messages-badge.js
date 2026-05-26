(function () {
    const badge = document.getElementById('hr-messages-badge');
    const url = '/hr/messages/unread-count';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function refresh() {
        fetch(url, { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf || '' } })
            .then((r) => r.json())
            .then((data) => {
                const n = data.count || 0;
                if (!badge) return;
                if (n > 0) {
                    badge.textContent = n > 99 ? '99+' : String(n);
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            })
            .catch(() => {});
    }

    refresh();
    setInterval(refresh, 15000);
})();
