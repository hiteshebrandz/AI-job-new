(function () {
    document.querySelectorAll('.ai-match-score-ring[data-score]').forEach((el) => {
        const score = parseInt(el.getAttribute('data-score'), 10) || 0;
        el.style.setProperty('--score', String(score));
    });

    const panel = document.getElementById('jd-status-panel');
    if (!panel) return;

    const statusUrl = panel.getAttribute('data-status-url');
    const initialStatus = panel.getAttribute('data-initial-status');
    if (!statusUrl || initialStatus === 'completed' || initialStatus === 'failed') {
        if (initialStatus === 'failed') {
            document.getElementById('jd-processing')?.classList.add('hidden');
            document.getElementById('jd-failed')?.classList.remove('hidden');
        }
        return;
    }

    const processing = document.getElementById('jd-processing');
    const content = document.getElementById('jd-matches-content');
    const failed = document.getElementById('jd-failed');
    const statusLabel = document.getElementById('jd-status-label');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function poll() {
        fetch(statusUrl, { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf || '' } })
            .then((r) => r.json())
            .then((data) => {
                if (statusLabel) statusLabel.textContent = data.status_label || data.status;

                if (data.failed) {
                    processing?.classList.add('hidden');
                    failed?.classList.remove('hidden');
                    return;
                }

                if (data.ready) {
                    processing?.classList.add('hidden');
                    content?.classList.remove('hidden');
                    window.location.reload();
                    return;
                }

                setTimeout(poll, 3000);
            })
            .catch(() => setTimeout(poll, 5000));
    }

    poll();
})();
