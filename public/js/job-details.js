(function () {
    const config = window.jobDetailsConfig;
    if (!config) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const applyBtn = document.getElementById('job-apply-btn');
    const saveBtn = document.getElementById('job-save-btn');
    const loader = document.getElementById('job-action-loader');
    const toastRoot = document.getElementById('toast-root');
    const applySection = document.getElementById('apply-section');

    function toast(message, type = 'success') {
        const el = document.createElement("div");
        el.className = `resume-toast resume-toast--${type}`;
        el.textContent = message;
        toastRoot?.appendChild(el);
        requestAnimationFrame(() => el.classList.add('resume-toast--visible'));
        setTimeout(() => {
            el.classList.remove('resume-toast--visible');
            setTimeout(() => el.remove(), 300);
        }, 4000);
    }

    function setLoading(show) {
        loader?.classList.toggle('hidden', !show);
        if (applyBtn) applyBtn.disabled = show || applyBtn.dataset.applied === '1';
        if (saveBtn) saveBtn.disabled = show;
    }

    async function postAction(url) {
        setLoading(true);
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({}),
            });
            const data = await res.json().catch(() => ({}));
            return { res, data };
        } finally {
            setLoading(false);
        }
    }

    saveBtn?.addEventListener('click', async () => {
        if (saveBtn.dataset.saved === '1') {
            toast('Already saved', 'error');
            return;
        }

        const { res, data } = await postAction(config.saveUrl);
        if (res.ok && data.success) {
            saveBtn.dataset.saved = '1';
            saveBtn.textContent = 'Saved';
            saveBtn.classList.add('opacity-70');
            toast(data.message || 'Job saved successfully');
            return;
        }

        if (data.saved) {
            saveBtn.dataset.saved = '1';
            saveBtn.textContent = 'Saved';
            saveBtn.classList.add('opacity-70');
        }
        toast(data.message || 'Could not save job', 'error');
    });

    applyBtn?.addEventListener('click', async () => {
        if (applyBtn.dataset.applied === '1') {
            toast('You already applied for this job.', 'error');
            return;
        }

        const { res, data } = await postAction(config.applyUrl);
        if (res.ok && data.success) {
            applyBtn.dataset.applied = '1';
            applyBtn.textContent = 'Applied';
            applyBtn.disabled = true;
            applyBtn.classList.add('opacity-70', 'cursor-not-allowed');
            toast(data.message || 'Your application has been submitted successfully.');
            return;
        }

        if (data.applied) {
            applyBtn.dataset.applied = '1';
            applyBtn.textContent = 'Applied';
            applyBtn.disabled = true;
            applyBtn.classList.add('opacity-70', 'cursor-not-allowed');
        }
        toast(data.message || 'Could not submit application', 'error');
    });

    if (config.highlightApply && applySection) {
        applySection.scrollIntoView({ behavior: 'smooth', block: 'center' });
        applySection.classList.add('ring-2', 'ring-secondary/40');
        setTimeout(() => applySection.classList.remove('ring-2', 'ring-secondary/40'), 3000);
    }
})();
