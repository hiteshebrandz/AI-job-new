@extends('layouts.app')

@php
    $rp = $resumeRoutePrefix ?? (auth()->user()->isHr() ? 'hr' : 'user');
@endphp

@section('title', 'Resume Upload & Parsing')

@section('body-class', 'bg-background font-body-md text-on-surface min-h-screen')

@section('page-css', 'resume_upload_parsing.css')

@section('tailwind-config', 'tailwind-config-forms.js')

@push('head-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<!-- Mobile sidebar overlay -->
<div id="sidebar-overlay" class="sidebar-overlay lg:hidden" onclick="closeSidebar()"></div>

@if (auth()->user()->isHr())
    @include('partials.nav.employer-sidebar', ['activeNav' => 'candidates'])
@else
    @include('partials.nav.candidate-sidebar', ['activeNav' => 'resume'])
@endif

<main class="lg:ml-[280px] min-h-screen">
    <!-- Topbar -->
    @if (auth()->user()->isHr())
    <header class="glass-panel border-b border-outline-variant h-[64px] fixed top-0 right-0 lg:w-[calc(100%-280px)] w-full z-40 flex justify-between items-center px-6 lg:px-8">
        <button type="button" class="lg:hidden p-2 rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-all" onclick="openSidebar()">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <div class="relative flex-1 max-w-sm mx-4">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]" data-icon="search">search</span>
            <input class="input-dark pl-11 py-2.5 text-[14px]" placeholder="Search candidates..." type="text"/>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" id="resume-new-upload-btn" class="btn-primary py-2 px-4 text-[13px]">
                <span class="material-symbols-outlined text-[16px]" data-icon="cloud_upload">cloud_upload</span>
                New Upload
            </button>
            @include('partials.nav.profile-dropdown')
        </div>
    </header>
    @else
    @include('partials.nav.candidate-topbar')
    @push('candidate-header-actions')
    <button type="button" id="resume-new-upload-btn" class="btn-primary py-2 px-4 text-[13px]">
        <span class="material-symbols-outlined text-[16px]" data-icon="cloud_upload">cloud_upload</span>
        New Upload
    </button>
    @endpush
    @endif

    <div class="max-w-[1200px] mx-auto px-6 lg:px-8 pt-[90px] pb-16">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 mb-6 text-[12px] text-on-surface-variant">
            <span>Candidates</span>
            <span class="material-symbols-outlined text-[14px]" data-icon="chevron_right">chevron_right</span>
            <span class="text-secondary">Resume Parsing</span>
        </div>

        <!-- Header -->
        <div class="mb-8 flex flex-wrap items-start justify-between gap-4 animate-fade-in">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="badge-ai">AI Engine</span>
                    <span id="candidate-id-badge" class="hidden inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-fixed text-secondary text-[11px] font-semibold border border-secondary/25">
                        <span class="material-symbols-outlined text-[14px]" data-icon="badge">badge</span>
                        ID: <span id="candidate-id-text">{{ $existingCandidate?->candidate_code ?? 'Auto-generated on save' }}</span>
                    </span>
                </div>
                <h2 class="text-[28px] font-extrabold text-on-surface mb-2">Resume Intelligence</h2>
                <p class="text-[14px] text-on-surface-variant max-w-2xl">Upload candidate resumes to automatically extract technical skills, professional experience, and educational background using our executive-grade AI engine.</p>
            </div>
        </div>

        @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-[var(--badge-success-bg)]/40 border border-[var(--badge-success-text)]/30 text-[var(--badge-success-text)] text-[14px] flex items-center gap-3 animate-fade-in">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-delay-1">
            <!-- Left: Upload area -->
            <div class="lg:col-span-5 space-y-5">
                <!-- Drop Zone -->
                <div id="resume-drop-zone" class="glass-card relative overflow-hidden rounded-2xl p-8 flex flex-col items-center justify-center text-center min-h-[360px] border-2 border-dashed border-outline-variant transition-all duration-300 cursor-pointer" style="border-color: rgba(51,65,85,0.8);">
                    <!-- AI parsing overlay -->
                    <div id="parsing-overlay" class="hidden absolute inset-0 rounded-2xl z-20 flex flex-col items-center justify-center gap-4" style="background: rgba(15,23,42,0.95); backdrop-filter: blur(16px);">
                        <div class="relative w-16 h-16">
                            <div class="w-16 h-16 border-4 border-outline-variant border-t-secondary rounded-full animate-spin"></div>
                            <div class="absolute inset-2 border-3 border-transparent border-b-[#06B6D4] rounded-full animate-spin" style="border-width: 3px; animation-direction: reverse; animation-duration: 1.5s;"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="material-symbols-outlined text-secondary text-[18px]" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                            </div>
                        </div>
                        <p class="text-[14px] font-bold text-secondary">AI Parsing in Progress...</p>
                        <p class="text-[12px] text-on-surface-variant">Extracting identity, skills & experience</p>
                        <div class="flex gap-1.5 mt-2">
                            @for ($i = 0; $i < 3; $i++)
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary animate-bounce" style="animation-delay: {{ $i * 0.15 }}s;"></div>
                            @endfor
                        </div>
                    </div>

                    <!-- Upload Icon -->
                    <div class="w-20 h-20 rounded-2xl bg-secondary-fixed border border-secondary/20 flex items-center justify-center mb-6 relative" id="drop-icon-wrap">
                        <span class="material-symbols-outlined text-secondary text-[38px]" data-icon="upload_file">upload_file</span>
                        <div class="absolute inset-x-3 h-[2px] bg-secondary/50 overflow-hidden rounded hidden" id="scan-anim-wrap">
                            <div class="h-full w-full bg-secondary resume-scan-line"></div>
                        </div>
                    </div>

                    <h3 class="text-[18px] font-bold text-on-surface mb-2">Drag & Drop Resume</h3>
                    <p class="text-[13px] text-on-surface-variant mb-6 max-w-[240px] leading-relaxed">Supports PDF, DOCX, and TXT files. Maximum file size 10MB.</p>

                    <input type="file" id="resume-file-input" class="hidden" accept=".pdf,.docx,.txt"/>
                    <button type="button" id="resume-select-btn" class="btn-primary py-2.5 px-7 text-[14px]">
                        <span class="material-symbols-outlined text-[16px]">folder_open</span>
                        Select File
                    </button>

                    <!-- Progress -->
                    <div id="upload-progress-wrap" class="mt-8 pt-6 border-t border-outline-variant w-full hidden">
                        <div class="flex items-center justify-between text-[12px] mb-2">
                            <span id="upload-progress-label" class="text-secondary font-semibold">Ready to upload</span>
                            <span id="upload-progress-percent" class="text-on-surface-variant">0%</span>
                        </div>
                        <div class="progress-bar-track">
                            <div id="upload-progress-bar" class="progress-bar-fill transition-all duration-300" style="width:0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Logs -->
                <div class="glass-card p-5">
                    <h4 class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-widest mb-4">Recent Processing</h4>
                    <div class="space-y-3" id="recent-logs-list">
                        @forelse ($recentLogs as $log)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-container-low border border-outline-variant">
                            <div class="w-9 h-9 bg-secondary-fixed rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-secondary text-[18px]" data-icon="description">description</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-semibold text-on-surface truncate">{{ $log->file_name }}</p>
                                <p class="text-[11px] text-on-surface-variant">{{ $log->created_at->diffForHumans() }} · {{ ucfirst($log->parsing_status) }}</p>
                            </div>
                            @if ($log->parsing_status === 'completed')
                            <span class="material-symbols-outlined text-[var(--badge-success-text)] text-[18px]" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            @elseif ($log->parsing_status === 'failed')
                            <span class="material-symbols-outlined text-[#F87171] text-[18px]" data-icon="error">error</span>
                            @else
                            <span class="material-symbols-outlined text-secondary text-[18px] animate-spin" data-icon="progress_activity">progress_activity</span>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-6 text-[13px] text-on-surface-variant">No resumes processed yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right: Parsed results form -->
            <div class="lg:col-span-7">
                <div class="glass-card overflow-hidden flex flex-col min-h-[600px]">
                    <!-- Card header -->
                    <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low/50">
                        <div>
                            <h3 class="text-[16px] font-bold text-on-surface">Parsing Preview</h3>
                            <p class="text-[11px] text-on-surface-variant mt-0.5 uppercase tracking-wide">Verifying extracted metadata</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" id="preview-resume-btn" class="p-2 hover:bg-surface-container-high rounded-xl text-on-surface-variant hover:text-secondary transition-all" title="Preview resume">
                                <span class="material-symbols-outlined text-[20px]" data-icon="visibility">visibility</span>
                            </button>
                            <button type="button" id="refresh-parse-btn" class="p-2 hover:bg-surface-container-high rounded-xl text-on-surface-variant hover:text-secondary transition-all" title="Re-upload">
                                <span class="material-symbols-outlined text-[20px]" data-icon="refresh">refresh</span>
                            </button>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div id="preview-empty" class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                        <div class="w-20 h-20 rounded-2xl bg-secondary-fixed border border-secondary/15 flex items-center justify-center mb-5">
                            <span class="material-symbols-outlined text-[36px] text-secondary opacity-60" data-icon="person_search">person_search</span>
                        </div>
                        <h4 class="text-[17px] font-bold text-on-surface-variant mb-2">Awaiting Resume Upload</h4>
                        <p class="text-[13px] text-on-surface-variant max-w-sm leading-relaxed">Upload a resume to see AI-extracted candidate data appear here in real time.</p>
                    </div>

                    <!-- Parsed data form -->
                    <div id="preview-form" class="hidden flex flex-col flex-1">
                        <div class="p-6 space-y-7 flex-1 overflow-y-auto max-h-[520px] no-scrollbar">
                            <!-- Identity -->
                            <section>
                                <h4 class="text-[11px] font-semibold text-secondary uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px]" data-icon="person">person</span>
                                    Identity Profile
                                </h4>
                                <div class="flex items-center gap-4 mb-5">
                                    <div class="w-14 h-14 rounded-full gradient-violet flex items-center justify-center text-white font-bold text-[18px] flex-shrink-0" id="profile-avatar">?</div>
                                    <p class="text-[12px] text-on-surface-variant">Profile auto-generated from parsed initials</p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2 space-y-1.5">
                                        <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="full_name">Full Name</label>
                                        <input class="input-dark" type="text" id="full_name" name="full_name"/>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="current_title">Current Title</label>
                                        <input class="input-dark" type="text" id="current_title" name="current_title"/>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="email">Email</label>
                                        <input class="input-dark" type="email" id="email" name="email" value="{{ auth()->user()->isUser() ? auth()->user()->email : '' }}"/>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="phone">Phone</label>
                                        <input class="input-dark" type="text" id="phone" name="phone"/>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="location">Location</label>
                                        <input class="input-dark" type="text" id="location" name="location"/>
                                    </div>
                                </div>
                            </section>

                            <!-- Skills -->
                            <section>
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-[11px] font-semibold text-secondary uppercase tracking-widest flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]" data-icon="psychology">psychology</span>
                                        Technical Skills
                                    </h4>
                                    <span id="skill-accuracy-badge" class="badge-ai text-[10px]">—</span>
                                </div>
                                <div id="skills-container" class="flex flex-wrap gap-2 min-h-[32px]"></div>
                                <button type="button" id="add-skill-btn" class="mt-3 px-4 py-1.5 border border-dashed border-outline-variant rounded-full text-[12px] text-on-surface-variant hover:text-secondary hover:border-[#06B6D4] transition-all">+ Add Skill</button>
                            </section>

                            <!-- Experience + Education -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                                <section>
                                    <h4 class="text-[11px] font-semibold text-secondary uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]" data-icon="history">history</span>
                                        Experience
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="space-y-1.5">
                                            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="experience_years">Total Years</label>
                                            <input class="input-dark" type="number" min="0" max="50" id="experience_years" name="experience_years"/>
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="seniority_level">Seniority Level</label>
                                            <input class="input-dark" type="text" id="seniority_level" name="seniority_level" placeholder="e.g. Senior"/>
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="previous_companies">Previous Companies</label>
                                            <textarea class="input-dark" id="previous_companies" name="previous_companies" rows="2" placeholder="Comma-separated"></textarea>
                                        </div>
                                    </div>
                                </section>

                                <section>
                                    <h4 class="text-[11px] font-semibold text-secondary uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]" data-icon="school">school</span>
                                        Education
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="space-y-1.5">
                                            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="education">Degree</label>
                                            <input class="input-dark" type="text" id="education" name="education"/>
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="university">University</label>
                                            <input class="input-dark" type="text" id="university" name="university"/>
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wide" for="graduation_year">Graduation Year</label>
                                            <input class="input-dark" type="number" id="graduation_year" name="graduation_year" min="1950" max="{{ date('Y') + 1 }}"/>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <!-- AI Score -->
                            <section>
                                <h4 class="text-[11px] font-semibold text-secondary uppercase tracking-widest mb-4">AI Compatibility Score</h4>
                                <input type="hidden" id="ai_score" name="ai_score"/>
                                <input type="hidden" id="ai_recommendation" name="ai_recommendation"/>
                                <input type="hidden" id="parsing_log_id" name="parsing_log_id" value=""/>

                                <div class="glass-card kpi-card p-5 flex items-center gap-5">
                                    <div class="relative w-[72px] h-[72px] flex-shrink-0">
                                        <svg class="w-full h-full -rotate-90" viewBox="0 0 72 72">
                                            <circle cx="36" cy="36" r="30" fill="transparent" stroke="var(--border-default)" stroke-width="5"/>
                                            <circle id="match-score-ring" cx="36" cy="36" r="30" fill="transparent"
                                                stroke="url(#scoreGradUpload)"
                                                stroke-width="5"
                                                stroke-linecap="round"
                                                stroke-dasharray="85, 100"
                                                class="transition-all duration-700"
                                            />
                                            <defs>
                                                <linearGradient id="scoreGradUpload" x1="0%" y1="0%" x2="100%" y2="0%">
                                                    <stop offset="0%" stop-color="#4648d4"/>
                                                    <stop offset="100%" stop-color="#6063ee"/>
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                        <div id="match-score-text" class="absolute inset-0 flex items-center justify-center font-extrabold text-secondary text-[17px]">—</div>
                                    </div>
                                    <div class="flex-1">
                                        <p id="match-title" class="text-[15px] font-semibold text-on-surface">Upload resume for AI analysis</p>
                                        <p id="match-description" class="text-[12px] text-on-surface-variant mt-1 leading-relaxed">Our engine will generate a hiring recommendation and compatibility score.</p>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Form actions -->
                        <div class="p-5 border-t border-outline-variant bg-surface-container-low/50 flex justify-end gap-3">
                            <button type="button" id="cancel-profile-btn" class="btn-ghost py-2.5 px-6 text-[14px]">Cancel</button>
                            <button type="button" id="create-profile-btn" class="btn-primary py-2.5 px-8 text-[14px]">
                                <span class="material-symbols-outlined text-[16px]">person_add</span>
                                Create Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.nav.dashboard-footer')
</main>

<!-- Toast container -->
<div id="toast-root" aria-live="polite"></div>

<!-- Resume preview modal -->
<div id="resume-preview-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
    <div class="glass-card rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden border border-outline-variant">
        <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
            <h3 class="text-[16px] font-bold text-on-surface">Resume Preview</h3>
            <button type="button" id="close-resume-modal" class="p-2 rounded-xl hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface transition-all">
                <span class="material-symbols-outlined" data-icon="close">close</span>
            </button>
        </div>
        <iframe id="resume-preview-frame" class="flex-1 w-full min-h-[70vh] bg-surface-container-low" title="Resume preview"></iframe>
    </div>
</div>

@push('scripts')
<script>
window.resumeUploadConfig = {
    uploadUrl: @json(route($rp . '.resume.upload.store')),
    profileUrl: @json(route($rp . '.resume.profile.store')),
    statusUrlTemplate: @json(route($rp . '.resume.parse.status', ['log' => '__ID__'])),
    maxBytes: {{ config('resume.max_upload_kb') * 1024 }},
    isHr: @json(auth()->user()->isHr()),
    prefill: @json($prefillData),
};
</script>
<script src="{{ asset('js/resume-upload.js') }}"></script>
<script src="{{ asset('js/candidate-topbar.js') }}"></script>
<script>
document.getElementById('full_name')?.addEventListener('input', function () {
    var avatar = document.getElementById('profile-avatar');
    if (!avatar) return;
    var parts = this.value.trim().split(/\s+/).filter(Boolean);
    avatar.textContent = parts.length >= 2
        ? (parts[0][0] + parts[1][0]).toUpperCase()
        : (parts[0]?.[0] || '?').toUpperCase();
});
function openSidebar() {
    document.getElementById('app-sidebar')?.classList.add('sidebar-mobile-open');
    document.getElementById('sidebar-overlay')?.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('app-sidebar')?.classList.remove('sidebar-mobile-open');
    document.getElementById('sidebar-overlay')?.classList.remove('active');
    document.body.style.overflow = '';
}
</script>
@endpush
@endsection
