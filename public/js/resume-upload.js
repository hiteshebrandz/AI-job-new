(function () {
    const config = window.resumeUploadConfig;
    if (!config) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const dropZone = document.getElementById('resume-drop-zone');
    const fileInput = document.getElementById('resume-file-input');
    const selectBtn = document.getElementById('resume-select-btn');
    const newUploadBtn = document.getElementById('resume-new-upload-btn');
    const progressWrap = document.getElementById('upload-progress-wrap');
    const progressBar = document.getElementById('upload-progress-bar');
    const progressLabel = document.getElementById('upload-progress-label');
    const progressPercent = document.getElementById('upload-progress-percent');
    const parsingOverlay = document.getElementById('parsing-overlay');
    const previewEmpty = document.getElementById('preview-empty');
    const previewForm = document.getElementById('preview-form');
    const skillsContainer = document.getElementById('skills-container');
    const addSkillBtn = document.getElementById('add-skill-btn');
    const createProfileBtn = document.getElementById('create-profile-btn');
    const cancelBtn = document.getElementById('cancel-profile-btn');
    const refreshBtn = document.getElementById('refresh-parse-btn');
    const previewResumeBtn = document.getElementById('preview-resume-btn');
    const resumeModal = document.getElementById('resume-preview-modal');
    const closeModalBtn = document.getElementById('close-resume-modal');
    const toastRoot = document.getElementById('toast-root');
    const parsingLogId = document.getElementById('parsing_log_id');
    const accuracyBadge = document.getElementById('skill-accuracy-badge');
    const matchScoreEl = document.getElementById('match-score-text');
    const matchRing = document.getElementById('match-score-ring');
    const matchTitle = document.getElementById('match-title');
    const matchDesc = document.getElementById('match-description');
    const candidateIdBadge = document.getElementById('candidate-id-badge');

    let state = {
        logId: null,
        resumeUrl: null,
        fileName: null,
        parsing: false,
    };

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

    function setProgress(percent, label) {
        if (!progressWrap) return;
        progressWrap.classList.remove('hidden');
        progressBar.style.width = `${percent}%`;
        progressPercent.textContent = `${percent}%`;
        if (label) progressLabel.textContent = label;
    }

    function resetProgress() {
        progressBar.style.width = '0%';
        progressPercent.textContent = '0%';
        progressLabel.textContent = 'Ready to upload';
        progressWrap?.classList.add('hidden');
    }

    function showParsing(show) {
        state.parsing = show;
        parsingOverlay?.classList.toggle('hidden', !show);
        dropZone?.classList.toggle('pointer-events-none', show);
        dropZone?.classList.toggle('opacity-60', show);
    }

    function fillForm(data) {
        const map = {
            full_name: data.name,
            email: data.email,
            phone: data.phone,
            location: data.location,
            current_title: data.title,
            experience_years: data.experience_years,
            seniority_level: data.seniority_level,
            previous_companies: data.previous_companies,
            education: data.education,
            university: data.university,
            graduation_year: data.graduation_year,
            ai_recommendation: data.ai_recommendation,
            ai_score: data.ai_score,
        };

        Object.entries(map).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el && value !== undefined && value !== null) {
                el.value = value;
            }
        });

        renderSkills(data.skills || []);
        const score = data.ai_score || 85;
        const accuracy = data.skill_accuracy || 90;
        accuracyBadge.textContent = `${accuracy}% Accuracy`;
        matchScoreEl.textContent = `${score}%`;
        matchRing?.setAttribute('stroke-dasharray', `${score}, 100`);
        matchTitle.textContent = score >= 90 ? 'Exceptional Candidate Match' : score >= 80 ? 'Strong Candidate Match' : 'Moderate Candidate Match';
        matchDesc.textContent = data.ai_recommendation || '';
        const aiScoreInput = document.getElementById('ai_score');
        const aiRecInput = document.getElementById('ai_recommendation');
        if (aiScoreInput) aiScoreInput.value = score;
        if (aiRecInput) aiRecInput.value = data.ai_recommendation || '';

        const nameInput = document.getElementById('full_name');
        if (nameInput) nameInput.dispatchEvent(new Event('input'));

        previewEmpty?.classList.add('hidden');
        previewForm?.classList.remove('hidden');
        candidateIdBadge?.classList.remove('hidden');
    }

    function renderSkills(skills) {
        skillsContainer.innerHTML = '';
        skills.forEach((skill) => addSkillTag(skill));
    }

    function addSkillTag(skill) {
        const tag = document.createElement('span');
        tag.className = 'skill-tag px-3 py-1 bg-surface-container-highest rounded-full text-body-sm flex items-center gap-2 border border-outline-variant';
        tag.dataset.skill = skill;
        tag.innerHTML = `${escapeHtml(skill)} <button type="button" class="material-symbols-outlined text-[14px] remove-skill" data-icon="close">close</button>`;
        tag.querySelector('.remove-skill')?.addEventListener('click', () => tag.remove());
        skillsContainer.appendChild(tag);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getSkills() {
        return [...skillsContainer.querySelectorAll('.skill-tag')]
            .map((tag) => tag.dataset.skill || '')
            .filter(Boolean);
    }

    function animateProgressWhileParsing() {
        let p = 10;
        setProgress(p, 'Uploading document...');
        const timer = setInterval(() => {
            if (!state.parsing) {
                clearInterval(timer);
                return;
            }
            p = Math.min(p + Math.random() * 12, 92);
            setProgress(Math.round(p), p < 40 ? 'Uploading document...' : p < 75 ? 'Scanning document...' : 'AI extracting fields...');
        }, 400);
        return timer;
    }

    async function uploadFile(file) {
        if (!file) return;
        const allowed = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
        const ext = file.name.split('.').pop()?.toLowerCase();
        if (!['pdf', 'docx', 'txt'].includes(ext || '')) {
            toast('Only PDF, DOCX, and TXT files are allowed.', 'error');
            return;
        }
        if (file.size > config.maxBytes) {
            toast('File exceeds 10MB limit.', 'error');
            return;
        }

        showParsing(true);
        const timer = animateProgressWhileParsing();
        state.fileName = file.name;

        const formData = new FormData();
        formData.append('resume', file);

        try {
            const xhr = new XMLHttpRequest();
            const uploadPromise = new Promise((resolve, reject) => {
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const uploadPct = Math.round((e.loaded / e.total) * 35);
                        setProgress(uploadPct, 'Uploading document...');
                    }
                });
                xhr.addEventListener('load', () => {
                    try {
                        const body = JSON.parse(xhr.responseText);
                        if (xhr.status >= 400) {
                            reject(new Error(body.message || body.error || 'Upload failed'));
                        } else {
                            resolve(body);
                        }
                    } catch {
                        reject(new Error('Invalid server response'));
                    }
                });
                xhr.addEventListener('error', () => reject(new Error('Upload failed')));
                xhr.open('POST', config.uploadUrl);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.send(formData);
            });

            const result = await uploadPromise;
            clearInterval(timer);
            showParsing(false);

            if (!result.success) {
                throw new Error(result.message || 'Parsing failed');
            }

            setProgress(100, 'Parsing complete');
            state.logId = result.log_id;
            state.resumeUrl = result.resume_url;
            parsingLogId.value = result.log_id;
            fillForm(result.data);
            toast(result.message || 'Resume parsed successfully.');
            setTimeout(resetProgress, 1500);
        } catch (err) {
            clearInterval(timer);
            showParsing(false);
            resetProgress();
            toast(err.message || 'Failed to parse resume.', 'error');
        }
    }

    selectBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput?.click();
    });

    newUploadBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput?.click();
    });

    fileInput?.addEventListener('change', () => {
        if (fileInput.files?.[0]) uploadFile(fileInput.files[0]);
    });

    ['dragenter', 'dragover'].forEach((evt) => {
        dropZone?.addEventListener(evt, (e) => {
            e.preventDefault();
            dropZone.classList.add('ring-2', 'ring-secondary/40');
        });
    });

    ['dragleave', 'drop'].forEach((evt) => {
        dropZone?.addEventListener(evt, (e) => {
            e.preventDefault();
            dropZone.classList.remove('ring-2', 'ring-secondary/40');
        });
    });

    dropZone?.addEventListener('drop', (e) => {
        const file = e.dataTransfer?.files?.[0];
        if (file) uploadFile(file);
    });

    addSkillBtn?.addEventListener('click', () => {
        const skill = prompt('Enter skill name');
        if (skill?.trim()) addSkillTag(skill.trim());
    });

    cancelBtn?.addEventListener('click', () => {
        if (confirm('Discard current parsed data?')) {
            previewForm?.classList.add('hidden');
            previewEmpty?.classList.remove('hidden');
            parsingLogId.value = '';
            state.logId = null;
            resetProgress();
        }
    });

    refreshBtn?.addEventListener('click', () => fileInput?.click());

    previewResumeBtn?.addEventListener('click', () => {
        if (!state.resumeUrl) {
            toast('Upload a resume first.', 'error');
            return;
        }
        const frame = document.getElementById('resume-preview-frame');
        frame.src = state.resumeUrl;
        resumeModal?.classList.remove('hidden');
    });

    closeModalBtn?.addEventListener('click', () => {
        resumeModal?.classList.add('hidden');
        document.getElementById('resume-preview-frame').src = '';
    });

    createProfileBtn?.addEventListener('click', async () => {
        if (!parsingLogId.value) {
            toast('Upload and parse a resume first.', 'error');
            return;
        }

        createProfileBtn.disabled = true;
        createProfileBtn.classList.add('opacity-70');

        const payload = {
            parsing_log_id: parsingLogId.value,
            full_name: document.getElementById('full_name')?.value,
            email: document.getElementById('email')?.value,
            phone: document.getElementById('phone')?.value,
            location: document.getElementById('location')?.value,
            current_title: document.getElementById('current_title')?.value,
            experience_years: document.getElementById('experience_years')?.value,
            seniority_level: document.getElementById('seniority_level')?.value,
            previous_companies: document.getElementById('previous_companies')?.value,
            education: document.getElementById('education')?.value,
            university: document.getElementById('university')?.value,
            graduation_year: document.getElementById('graduation_year')?.value,
            ai_recommendation: document.getElementById('ai_recommendation')?.value,
            ai_score: document.getElementById('ai_score')?.value,
            skills: getSkills(),
            create_new_account: config.isHr,
        };

        try {
            const res = await fetch(config.profileUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    Accept: 'application/json',
                },
                body: JSON.stringify(payload),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) {
                const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
                throw new Error(data.message || firstError || 'Could not create profile');
            }
            toast(data.message);
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 800);
        } catch (err) {
            toast(err.message, 'error');
            createProfileBtn.disabled = false;
            createProfileBtn.classList.remove('opacity-70');
        }
    });

    if (config.prefill) {
        fillForm(config.prefill);
        candidateIdBadge?.classList.remove('hidden');
        document.getElementById('candidate-id-text').textContent = config.prefill.candidate_code || 'Pending';
    }
})();
