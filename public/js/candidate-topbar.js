(function () {
    const btn = document.getElementById('profile-dropdown-btn');
    const menu = document.getElementById('profile-dropdown-menu');
    const wrap = document.getElementById('profile-dropdown-wrap');
    const notifBtn = document.getElementById('notifications-btn');
    const notifPanel = document.getElementById('notifications-panel');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    if (btn && menu) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('hidden');
            notifPanel?.classList.add('hidden');
        });
    }

    document.addEventListener('click', (e) => {
        if (wrap && !wrap.contains(e.target)) menu?.classList.add('hidden');
        if (notifPanel && notifBtn && !notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
            notifPanel.classList.add('hidden');
        }
    });

    async function loadNotifications() {
        if (!notifPanel) return;
        try {
            const res = await fetch('/user/notifications', { headers: { Accept: 'application/json' } });
            const data = await res.json();
            if (!data.success) return;
            notifPanel.innerHTML = data.notifications.length
                ? data.notifications.map((n) => `<p class="font-body-sm py-2 border-b border-outline-variant ${n.is_read ? 'text-on-surface-variant' : 'text-primary font-semibold'}">${n.message}</p>`).join('')
                : '<p class="font-body-sm text-on-surface-variant">No notifications yet.</p>';
            const badge = document.getElementById('notifications-badge');
            if (badge) {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        } catch (_) {}
    }

    notifBtn?.addEventListener('click', async (e) => {
        e.stopPropagation();
        menu?.classList.add('hidden');
        notifPanel?.classList.toggle('hidden');
        if (notifPanel && !notifPanel.classList.contains('hidden')) {
            await loadNotifications();
            if (csrf) {
                fetch('/user/notifications/read', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                });
            }
        }
    });

    if (notifBtn) {
        loadNotifications();
    }
})();
