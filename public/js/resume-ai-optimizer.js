(function () {
    const config = window.resumeOptimizerConfig || {};
    const pollIntervalMs = config.pollIntervalMs || 4000;
    const maxPollSeconds = config.maxPollSeconds || 120;
    let pollTimer = null;
    let pollStartedAt = null;
    let pollCount = 0;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function showError(msg) {
        const el = document.getElementById('optimizer-error');
        if (!el) return;
        el.textContent = msg;
        el.classList.remove('hidden');
    }

    function hideError() {
        const el = document.getElementById('optimizer-error');
        if (el) el.classList.add('hidden');
    }

    function formatSeconds(sec) {
        const s = Math.max(0, Math.floor(sec));
        if (s < 60) return s + 's';
        const m = Math.floor(s / 60);
        const r = s % 60;
        return m + 'm ' + r + 's';
    }

    function updateProgressUI(json) {
        const progress = json.progress || {};
        const elapsed = progress.elapsed_seconds ?? 0;
        const remaining = progress.estimated_remaining_seconds ?? 0;
        const percent = progress.progress_percent ?? 5;
        const phaseLabel = progress.phase_label || 'Processing…';

        const elapsedEl = document.getElementById('optimizer-elapsed');
        const remainingEl = document.getElementById('optimizer-remaining');
        const barEl = document.getElementById('optimizer-progress-bar');
        const phaseEl = document.getElementById('optimizer-progress-phase');
        const pollStatusEl = document.getElementById('optimizer-poll-status');

        if (elapsedEl) elapsedEl.textContent = formatSeconds(elapsed);
        if (remainingEl) {
            remainingEl.textContent = remaining > 0 ? '~' + formatSeconds(remaining) : 'almost done';
        }
        if (barEl) barEl.style.width = Math.min(95, Math.max(5, percent)) + '%';
        if (phaseEl) phaseEl.textContent = phaseLabel;
        if (pollStatusEl) {
            pollStatusEl.textContent = 'Status: ' + (json.status || 'processing') +
                ' · check #' + pollCount;
        }
    }

    function redirectWhenDone(runId, status) {
        const base = config.pageUrl || window.location.pathname;
        const url = base + (base.includes('?') ? '&' : '?') + 'run=' + runId;
        if (status === 'completed' || status === 'analyzed') {
            window.location.replace(url);
        } else if (status === 'failed') {
            window.location.replace(url);
        }
    }

    function startPolling(runId) {
        stopPolling();
        pollStartedAt = Date.now();
        pollCount = 0;
        pollTimer = setInterval(() => pollStatus(runId), pollIntervalMs);
        pollStatus(runId);
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    function pollStatus(runId) {
        const url = (config.statusUrlTemplate || '').replace('__RUN__', runId);
        if (!url) return;

        pollCount += 1;

        const elapsedTotal = pollStartedAt
            ? Math.floor((Date.now() - pollStartedAt) / 1000)
            : 0;

        if (elapsedTotal > maxPollSeconds) {
            stopPolling();
            showError(
                'This is taking longer than expected. Refresh the page — your resume may still finish in the background.'
            );
            return;
        }

        fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store',
        })
            .then((r) => r.json())
            .then((json) => {
                updateProgressUI(json);

                if (json.status === 'completed') {
                    stopPolling();
                    redirectWhenDone(runId, 'completed');
                } else if (json.status === 'analyzed' && config.processingMode === 'analyzing') {
                    stopPolling();
                    redirectWhenDone(runId, 'analyzed');
                } else if (json.status === 'failed' || json.success === false) {
                    stopPolling();
                    redirectWhenDone(runId, 'failed');
                }
            })
            .catch(() => {
                const pollStatusEl = document.getElementById('optimizer-poll-status');
                if (pollStatusEl) pollStatusEl.textContent = 'Connection issue — retrying…';
            });
    }

    function onFileSelected(input) {
        const file = input.files[0];
        const preview = document.getElementById('file-preview');
        const btn = document.getElementById('upload-btn');
        if (!file) {
            preview?.classList.add('hidden');
            btn?.classList.add('hidden');
            return;
        }
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-size').textContent =
            (file.size / 1024 / 1024).toFixed(2) + ' MB';
        preview?.classList.remove('hidden');
        btn?.classList.remove('hidden');
        hideError();
    }

    function clearFile() {
        const input = document.getElementById('resume-input');
        if (input) input.value = '';
        document.getElementById('file-preview')?.classList.add('hidden');
        document.getElementById('upload-btn')?.classList.add('hidden');
    }

    function handleDrop(event) {
        event.preventDefault();
        document.getElementById('drop-zone')?.classList.remove('border-secondary');
        const file = event.dataTransfer.files[0];
        if (!file) return;
        const input = document.getElementById('resume-input');
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        onFileSelected(input);
    }

    function uploadWithProgress(form) {
        const fileInput = document.getElementById('resume-input');
        const file = fileInput?.files[0];
        if (!file) return;

        hideError();
        const btn = document.getElementById('upload-btn');
        const btnText = document.getElementById('btn-text');
        const progressWrap = document.getElementById('upload-progress');
        const progressBar = document.getElementById('upload-progress-bar');
        const progressLabel = document.getElementById('upload-progress-label');

        btn.disabled = true;
        btnText.innerHTML =
            '<span class="material-symbols-outlined text-[18px] animate-spin">sync</span> Uploading…';
        progressWrap?.classList.remove('hidden');

        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken());
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable && progressBar) {
                const pct = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = pct + '%';
                if (progressLabel) progressLabel.textContent = 'Uploading… ' + pct + '%';
            }
        };

        xhr.onload = function () {
            let json = {};
            try {
                json = JSON.parse(xhr.responseText);
            } catch (e) {
                showError('Invalid server response.');
                resetUploadBtn();
                return;
            }
            if (xhr.status >= 200 && xhr.status < 300 && json.success) {
                if (json.run_id) {
                    const base = config.pageUrl || '';
                    window.location.href = base + '?run=' + json.run_id + '&analyzing=1';
                } else {
                    window.location.reload();
                }
            } else {
                showError(json.message || json.error || 'Upload failed.');
                resetUploadBtn();
            }
        };

        xhr.onerror = function () {
            showError('Network error. Please try again.');
            resetUploadBtn();
        };

        const data = new FormData(form);
        xhr.send(data);

        function resetUploadBtn() {
            btn.disabled = false;
            btnText.innerHTML =
                '<span class="material-symbols-outlined text-[18px]">auto_awesome</span> Analyze My Resume';
            progressWrap?.classList.add('hidden');
            if (progressBar) progressBar.style.width = '0%';
        }
    }

    function bindGenerate() {
        const btn = document.getElementById('generate-btn');
        if (!btn) return;

        btn.addEventListener('click', function () {
            const runId = btn.dataset.runId;
            if (!runId || btn.disabled) return;

            btn.disabled = true;
            btn.innerHTML =
                '<span class="material-symbols-outlined text-[18px] animate-spin">sync</span> Starting…';

            fetch(config.generateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ run_id: parseInt(runId, 10) }),
            })
                .then((r) => r.json())
                .then((json) => {
                    if (json.success) {
                        const target = json.redirect_url ||
                            (config.pageUrl + '?run=' + runId + '&generating=1');
                        window.location.href = target;
                    } else {
                        showError(json.message || 'Generation failed.');
                        btn.disabled = false;
                        btn.innerHTML =
                            '<span class="material-symbols-outlined text-[18px]">description</span> Generate My New Resume';
                    }
                })
                .catch(() => {
                    showError('Network error. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML =
                        '<span class="material-symbols-outlined text-[18px]">description</span> Generate My New Resume';
                });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('upload-form');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                uploadWithProgress(form);
            });
        }

        bindGenerate();

        if (config.pollRunId && document.getElementById('optimizer-processing-section')) {
            startPolling(config.pollRunId);
        }
    });

    window.onFileSelected = onFileSelected;
    window.clearFile = clearFile;
    window.handleDrop = handleDrop;
})();
