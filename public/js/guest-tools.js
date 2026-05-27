(function () {
    const config = window.guestToolsConfig || {};
    const pollIntervalMs = 3500;
    const maxPollSeconds = 120;

    let activePollTimer = null;
    let pollStartedAt = null;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function qs(id) {
        return document.getElementById(id);
    }

    function showPanelError(panelKey, message) {
        const el = qs(panelKey + '-error');
        if (!el) return;
        el.textContent = message;
        el.classList.remove('hidden');
    }

    function hidePanelError(panelKey) {
        const el = qs(panelKey + '-error');
        if (el) el.classList.add('hidden');
    }

    function updateAttemptBadge(tool, attempts) {
        const badge = qs(tool + '-attempt-badge');
        if (!badge || !attempts) return;

        const used = attempts.used ?? 0;
        const max = attempts.max ?? 3;
        const remaining = attempts.remaining ?? 0;

        badge.textContent = used + '/' + max + ' used';
        badge.classList.remove('warning', 'locked');

        if (attempts.locked) {
            badge.classList.add('locked');
            badge.textContent = 'Limit reached — login required';
        } else if (remaining <= 1) {
            badge.classList.add('warning');
            badge.textContent = remaining + ' attempt left';
        }
    }

    function setLockedState(tool, locked) {
        const drop = qs(tool + '-drop-zone');
        const btn = qs(tool + '-upload-btn');
        const input = qs(tool + '-file-input');
        const lockBanner = qs(tool + '-lock-banner');

        if (drop) drop.classList.toggle('locked', locked);
        if (btn) btn.disabled = locked;
        if (input) input.disabled = locked;
        if (lockBanner) lockBanner.classList.toggle('hidden', !locked);
    }

    function stopPolling() {
        if (activePollTimer) {
            clearInterval(activePollTimer);
            activePollTimer = null;
        }
    }

    function formatFileSize(bytes) {
        return (bytes / 1024 / 1024).toFixed(2) + ' MB';
    }

    function onFileSelected(tool, input) {
        const file = input.files[0];
        const preview = qs(tool + '-file-preview');
        const btn = qs(tool + '-upload-btn');

        if (!file) {
            preview?.classList.add('hidden');
            btn?.classList.add('hidden');
            return;
        }

        qs(tool + '-file-name').textContent = file.name;
        qs(tool + '-file-size').textContent = formatFileSize(file.size);
        preview?.classList.remove('hidden');
        btn?.classList.remove('hidden');
        hidePanelError(tool);
    }

    function clearFile(tool) {
        const input = qs(tool + '-file-input');
        if (input) input.value = '';
        qs(tool + '-file-preview')?.classList.add('hidden');
        qs(tool + '-upload-btn')?.classList.add('hidden');
    }

    function handleDrop(tool, event) {
        event.preventDefault();
        qs(tool + '-drop-zone')?.classList.remove('dragover');
        const file = event.dataTransfer.files[0];
        if (!file) return;
        const input = qs(tool + '-file-input');
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        onFileSelected(tool, input);
    }

    function renderResumeResult(data, aiScore) {
        const panel = qs('resume-result');
        if (!panel) return;

        const skills = (data?.skills || []).slice(0, 8);
        const skillsHtml = skills.length
            ? skills.map((s) => '<span class="feature-pill feature-pill-violet text-[11px]">' + s + '</span>').join('')
            : '<span class="text-[12px]" style="color:var(--text-muted);">No skills detected</span>';

        panel.innerHTML =
            '<div class="guest-result-card p-6">' +
            '<div class="flex flex-col sm:flex-row gap-6 items-start">' +
            '<div class="guest-score-ring relative flex-shrink-0 mx-auto sm:mx-0">' +
            '<svg class="w-full h-full -rotate-90" viewBox="0 0 88 88">' +
            '<circle cx="44" cy="44" r="36" fill="none" stroke="var(--bg-surface-high)" stroke-width="8"/>' +
            '<circle cx="44" cy="44" r="36" fill="none" stroke="#4648d4" stroke-width="8" stroke-dasharray="226" stroke-dashoffset="' +
            (226 - (226 * (aiScore || 0)) / 100) +
            '" stroke-linecap="round"/>' +
            '</svg>' +
            '<div class="absolute inset-0 flex items-center justify-center">' +
            '<span class="text-[22px] font-extrabold" style="color:#4648d4;">' + (aiScore || '—') + '</span>' +
            '</div></div>' +
            '<div class="flex-1 min-w-0">' +
            '<p class="text-[11px] font-bold uppercase tracking-widest mb-1" style="color:var(--brand-secondary);">Resume Test Result</p>' +
            '<h3 class="text-[20px] font-bold mb-1" style="color:var(--text-heading);">' + (data?.name || 'Candidate') + '</h3>' +
            '<p class="text-[13px] mb-3" style="color:var(--text-muted);">' + (data?.current_title || data?.title || 'Role not detected') + '</p>' +
            '<p class="text-[13px] leading-relaxed mb-4" style="color:var(--text-secondary);">' +
            (data?.summary || data?.ai_recommendation || 'Analysis complete. Create an account to save your profile and apply to jobs.') +
            '</p>' +
            '<div class="flex flex-wrap gap-2">' + skillsHtml + '</div>' +
            '</div></div></div>';

        panel.classList.remove('hidden');
    }

    function renderAtsResult(analysis, score) {
        const panel = qs('ats-result');
        if (!panel) return;

        const issues = analysis?.ats_issues || analysis?.issues || [];
        const keywords = analysis?.keywords_found || analysis?.keywords || [];
        const fixes = analysis?.formatting_fixes || analysis?.fixes || [];

        const issueHtml = issues.length
            ? '<ul class="space-y-2 text-[13px]" style="color:var(--text-secondary);">' +
              issues.slice(0, 5).map((i) => '<li class="flex gap-2"><span class="material-symbols-outlined text-[16px]" style="color:#b45309;">warning</span><span>' + (typeof i === 'string' ? i : i.message || i.title || 'Issue') + '</span></li>').join('') +
              '</ul>'
            : '<p class="text-[13px]" style="color:var(--text-muted);">No major ATS issues detected.</p>';

        panel.innerHTML =
            '<div class="guest-result-card p-6">' +
            '<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">' +
            '<div class="text-center p-4 rounded-xl" style="background:var(--bg-surface); border:1px solid var(--border-subtle);">' +
            '<p class="text-[28px] font-extrabold" style="color:#4648d4;">' + (score ?? '—') + '%</p><p class="text-[11px]" style="color:var(--text-muted);">ATS Score</p></div>' +
            '<div class="text-center p-4 rounded-xl" style="background:var(--bg-surface); border:1px solid var(--border-subtle);">' +
            '<p class="text-[28px] font-extrabold" style="color:#047857;">' + (keywords.length || '—') + '</p><p class="text-[11px]" style="color:var(--text-muted);">Keywords</p></div>' +
            '<div class="text-center p-4 rounded-xl" style="background:var(--bg-surface); border:1px solid var(--border-subtle);">' +
            '<p class="text-[28px] font-extrabold" style="color:#b45309;">' + (fixes.length || issues.length || 0) + '</p><p class="text-[11px]" style="color:var(--text-muted);">Suggested Fixes</p></div>' +
            '</div>' +
            '<p class="text-[12px] font-semibold uppercase tracking-wide mb-3" style="color:var(--text-muted);">Compatibility Notes</p>' +
            issueHtml +
            '<p class="text-[12px] mt-4" style="color:var(--text-muted);">' + (analysis?.summary || 'Sign in to generate a fully optimized resume PDF.') + '</p>' +
            '</div>';

        panel.classList.remove('hidden');
    }

    function pollUrl(tool, id, providedUrl) {
        if (providedUrl) return providedUrl;
        const template = tool === 'resume' ? config.resumeStatusUrlTemplate : config.atsStatusUrlTemplate;
        return (template || '').replace('__ID__', id);
    }

    function startPolling(tool, id, url, onComplete) {
        stopPolling();
        pollStartedAt = Date.now();

        function tick() {
            const elapsed = pollStartedAt ? Math.floor((Date.now() - pollStartedAt) / 1000) : 0;
            if (elapsed > maxPollSeconds) {
                stopPolling();
                showPanelError(tool, 'This is taking longer than expected. Please refresh and try again.');
                resetUploadBtn(tool);
                return;
            }

            fetch(url, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store',
            })
                .then((r) => r.json())
                .then((json) => {
                    if (tool === 'resume') {
                        if (json.status === 'completed') {
                            stopPolling();
                            renderResumeResult(json.data, json.ai_score);
                            resetUploadBtn(tool);
                        } else if (json.status === 'failed' || json.success === false) {
                            stopPolling();
                            showPanelError(tool, json.message || json.error || 'Parsing failed.');
                            resetUploadBtn(tool);
                        }
                        return;
                    }

                    if (json.status === 'analyzed' || json.status === 'completed') {
                        stopPolling();
                        renderAtsResult(json.analysis, json.score);
                        resetUploadBtn(tool);
                    } else if (json.status === 'failed' || json.success === false) {
                        stopPolling();
                        showPanelError(tool, json.error || 'ATS analysis failed.');
                        resetUploadBtn(tool);
                    }
                })
                .catch(() => {});
        }

        activePollTimer = setInterval(tick, pollIntervalMs);
        tick();
    }

    function resetUploadBtn(tool) {
        const btn = qs(tool + '-upload-btn');
        const label = qs(tool + '-upload-btn-label');
        if (btn) btn.disabled = false;
        if (label) {
            label.innerHTML =
                tool === 'resume'
                    ? '<span class="material-symbols-outlined text-[18px]">upload_file</span> Test My Resume'
                    : '<span class="material-symbols-outlined text-[18px]">fact_check</span> Check ATS Compatibility';
        }
        qs(tool + '-upload-progress')?.classList.add('hidden');
    }

    function uploadFile(tool, uploadUrl) {
        const input = qs(tool + '-file-input');
        const file = input?.files[0];
        if (!file) return;

        hidePanelError(tool);
        const btn = qs(tool + '-upload-btn');
        const label = qs(tool + '-upload-btn-label');
        const progressWrap = qs(tool + '-upload-progress');
        const progressBar = qs(tool + '-upload-progress-bar');

        btn.disabled = true;
        label.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">sync</span> Processing…';
        progressWrap?.classList.remove('hidden');

        const formData = new FormData();
        formData.append('resume', file);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', uploadUrl);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken());
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable && progressBar) {
                progressBar.style.width = Math.round((e.loaded / e.total) * 100) + '%';
            }
        };

        xhr.onload = function () {
            let json = {};
            try {
                json = JSON.parse(xhr.responseText);
            } catch (e) {
                showPanelError(tool, 'Invalid server response.');
                resetUploadBtn(tool);
                return;
            }

            if (json.login_required) {
                window.location.href = json.login_url || config.loginUrl || '/login';
                return;
            }

            if (xhr.status === 403 && json.login_required) {
                window.location.href = json.login_url || config.loginUrl || '/login';
                return;
            }

            if (!json.success && xhr.status >= 400) {
                showPanelError(tool, json.message || 'Upload failed.');
                resetUploadBtn(tool);
                return;
            }

            if (json.attempts) {
                updateAttemptBadge(tool, json.attempts);
                setLockedState(tool, json.attempts.locked);
            }

            if (tool === 'resume' && json.status === 'completed') {
                renderResumeResult(json.data, json.ai_score);
                resetUploadBtn(tool);
                return;
            }

            if (tool === 'ats' && (json.status === 'analyzed' || json.status === 'completed')) {
                renderAtsResult(json.analysis, json.score);
                resetUploadBtn(tool);
                return;
            }

            const id = json.log_id || json.run_id;
            const poll = json.poll_url || pollUrl(tool, id);
            if (id && poll) {
                qs(tool + '-processing')?.classList.remove('hidden');
                startPolling(tool, id, poll);
            } else {
                resetUploadBtn(tool);
            }
        };

        xhr.onerror = function () {
            showPanelError(tool, 'Network error. Please try again.');
            resetUploadBtn(tool);
        };

        xhr.send(formData);
    }

    function initTabs() {
        document.querySelectorAll('[data-guest-tab]').forEach((tab) => {
            tab.addEventListener('click', function () {
                const target = this.getAttribute('data-guest-tab');
                document.querySelectorAll('[data-guest-tab]').forEach((t) => t.classList.remove('active'));
                document.querySelectorAll('.guest-panel').forEach((p) => p.classList.remove('active'));
                this.classList.add('active');
                qs('panel-' + target)?.classList.add('active');
            });
        });
    }

    function initUploads() {
        ['resume', 'ats'].forEach((tool) => {
            const input = qs(tool + '-file-input');
            const form = qs(tool + '-upload-form');
            const drop = qs(tool + '-drop-zone');

            input?.addEventListener('change', () => onFileSelected(tool, input));
            drop?.addEventListener('dragover', (e) => {
                e.preventDefault();
                drop.classList.add('dragover');
            });
            drop?.addEventListener('dragleave', () => drop.classList.remove('dragover'));
            drop?.addEventListener('drop', (e) => handleDrop(tool, e));

            form?.addEventListener('submit', (e) => {
                e.preventDefault();
                const url = form.getAttribute('action');
                uploadFile(tool, url);
            });

            const attempts = config.attempts?.[tool === 'resume' ? 'resume_test' : 'ats_check'];
            if (attempts) {
                updateAttemptBadge(tool, attempts);
                setLockedState(tool, attempts.locked);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initTabs();
        initUploads();
    });

    window.guestToolsClearFile = clearFile;
})();
