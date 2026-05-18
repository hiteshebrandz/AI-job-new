(function () {
    const csrf = window.savedJobsConfig?.csrf;
    document.querySelectorAll('.remove-saved-job').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const jobId = btn.dataset.removeJob;
            if (!jobId || !confirm('Remove this job from saved list?')) return;
            btn.disabled = true;
            try {
                const res = await fetch(`/user/saved-jobs/${jobId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                });
                const data = await res.json();
                if (data.success) {
                    btn.closest('.bg-white')?.remove();
                } else {
                    alert(data.message || 'Could not remove job');
                    btn.disabled = false;
                }
            } catch {
                alert('Request failed');
                btn.disabled = false;
            }
        });
    });
})();
