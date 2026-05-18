@extends('layouts.app')

@php
    $rp = $resumeRoutePrefix ?? (auth()->user()->isHr() ? 'hr' : 'user');
@endphp

@section('title', 'Resume Upload & Parsing')

@section('body-class', 'bg-background font-body-md text-on-background min-h-screen')

@section('page-css', 'resume_upload_parsing.css')

@section('tailwind-config', 'tailwind-config-forms.js')

@push('head-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

@if (auth()->user()->isHr())
    @include('partials.nav.employer-sidebar', ['activeNav' => 'candidates'])
@else
    @include('partials.nav.candidate-sidebar')
    @push('candidate-header-actions')
    <button type="button" id="resume-new-upload-btn" class="bg-secondary text-on-secondary px-4 py-2 rounded-xl flex items-center gap-2 font-label-caps hover:scale-[1.02] active:scale-[0.98] transition-all">
    <span class="material-symbols-outlined text-[18px]" data-icon="cloud_upload">cloud_upload</span>
    New Upload
    </button>
    @endpush
@endif
<main class="ml-[280px] min-h-screen">
@if (auth()->user()->isHr())
<header class="fixed top-0 right-0 w-[calc(100%-280px)] h-16 z-40 bg-surface/80 backdrop-blur-lg border-b border-outline-variant flex justify-between items-center px-gutter max-w-[1440px] mx-auto">
<div class="flex items-center bg-surface-container-low px-4 py-2 rounded-full border border-outline-variant focus-within:ring-2 focus-within:ring-secondary/50 transition-all w-96">
<span class="material-symbols-outlined text-outline mr-2" data-icon="search">search</span>
<input class="bg-transparent border-none focus:ring-0 text-body-sm w-full" placeholder="Search candidates or resumes..." type="text"/>
</div>
<div class="flex items-center gap-4">
<button type="button" id="resume-new-upload-btn" class="bg-secondary text-on-secondary px-4 py-2 rounded-xl flex items-center gap-2 font-label-caps hover:scale-[1.02] active:scale-[0.98] transition-all">
<span class="material-symbols-outlined text-[18px]" data-icon="cloud_upload">cloud_upload</span>
New Upload
</button>
@include('partials.nav.profile-dropdown')
</div>
</header>
@else
@include('partials.nav.candidate-topbar')
@endif

<div class="max-w-[1200px] mx-auto p-container-margin pt-24">
<div class="flex items-center gap-2 mb-8 text-on-surface-variant font-label-caps">
<span>Candidates</span>
<span class="material-symbols-outlined text-[14px]" data-icon="chevron_right">chevron_right</span>
<span class="text-secondary">Resume Parsing</span>
</div>

<div class="mb-10 flex flex-wrap items-start justify-between gap-4">
<div>
<h2 class="font-headline-lg text-headline-lg text-primary mb-2">Resume Intelligence</h2>
<p class="font-body-md text-on-surface-variant max-w-2xl">Upload candidate resumes to automatically extract technical skills, professional experience, and educational background using our executive-grade AI engine.</p>
</div>
<span id="candidate-id-badge" class="hidden inline-flex items-center gap-2 px-4 py-2 rounded-full bg-secondary/10 text-secondary font-label-caps text-[12px]">
<span class="material-symbols-outlined text-[16px]" data-icon="badge">badge</span>
ID: <span id="candidate-id-text">{{ $existingCandidate?->candidate_code ?? 'Auto-generated on save' }}</span>
</span>
</div>

@if (session('success'))
<div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-body-sm">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
<div class="lg:col-span-5 space-y-6">
<div id="resume-drop-zone" class="gradient-border rounded-xl p-8 flex flex-col items-center justify-center text-center min-h-[400px] border-dashed border-2 relative overflow-hidden transition-all">
<div id="parsing-overlay" class="hidden absolute inset-0 bg-white/90 dark:bg-inverse-surface/90 z-20 flex flex-col items-center justify-center gap-4">
<div class="w-14 h-14 border-4 border-secondary/20 border-t-secondary rounded-full resume-spinner"></div>
<p class="font-label-caps text-secondary font-bold">AI Parsing in progress...</p>
<p class="font-body-sm text-on-surface-variant">Extracting identity, skills & experience</p>
</div>
<div class="w-20 h-20 bg-secondary/10 text-secondary rounded-full flex items-center justify-center mb-6 relative">
<span class="material-symbols-outlined text-[40px]" data-icon="upload_file">upload_file</span>
<div class="absolute inset-x-4 h-1 bg-secondary/30 overflow-hidden rounded hidden" id="scan-anim-wrap">
<div class="h-full w-full bg-secondary resume-scan-line"></div>
</div>
</div>
<h3 class="font-title-md text-title-md mb-2">Drag &amp; Drop Resume</h3>
<p class="font-body-sm text-on-surface-variant mb-6 px-8">Supports PDF, DOCX, and TXT files. Maximum file size 10MB.</p>
<input type="file" id="resume-file-input" class="hidden" accept=".pdf,.docx,.txt"/>
<button type="button" id="resume-select-btn" class="bg-primary text-on-primary px-6 py-3 rounded-xl font-label-caps hover:bg-primary/90 transition-colors">
Select File
</button>
<div id="upload-progress-wrap" class="mt-8 pt-8 border-t border-outline-variant w-full hidden">
<div class="flex items-center justify-between text-label-caps mb-2">
<span id="upload-progress-label" class="text-secondary font-bold">Ready to upload</span>
<span id="upload-progress-percent">0%</span>
</div>
<div class="w-full h-2 bg-surface-container-highest rounded-full overflow-hidden">
<div id="upload-progress-bar" class="h-full bg-secondary rounded-full shadow-[0_0_8px_rgba(70,72,212,0.5)] transition-all duration-300" style="width:0%"></div>
</div>
</div>
</div>

<div class="bg-surface border border-outline-variant rounded-xl p-card-padding">
<h4 class="font-label-caps text-on-surface-variant mb-4">Recent Processing</h4>
<div class="space-y-4" id="recent-logs-list">
@forelse ($recentLogs as $log)
<div class="flex items-center gap-3">
<div class="w-10 h-10 bg-surface-container-high rounded-lg flex items-center justify-center">
<span class="material-symbols-outlined text-outline" data-icon="description">description</span>
</div>
<div class="flex-1 min-w-0">
<p class="font-body-sm font-semibold truncate">{{ $log->file_name }}</p>
<p class="text-[12px] text-on-surface-variant">{{ $log->created_at->diffForHumans() }} · {{ ucfirst($log->parsing_status) }}</p>
</div>
@if ($log->parsing_status === 'completed')
<span class="material-symbols-outlined text-green-600" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
@elseif ($log->parsing_status === 'failed')
<span class="material-symbols-outlined text-red-500" data-icon="error">error</span>
@else
<span class="material-symbols-outlined text-secondary animate-pulse" data-icon="progress_activity">progress_activity</span>
@endif
</div>
@empty
<p class="font-body-sm text-on-surface-variant text-center py-4">No resumes processed yet.</p>
@endforelse
</div>
</div>
</div>

<div class="lg:col-span-7">
<div class="bg-white dark:bg-surface border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col min-h-[600px]">
<div class="px-card-padding py-4 bg-surface-container-low border-b border-outline-variant flex justify-between items-center">
<div>
<h3 class="font-title-md text-title-md">Parsing Preview</h3>
<p class="text-[12px] text-on-surface-variant font-label-caps">Verifying extracted metadata</p>
</div>
<div class="flex gap-2">
<button type="button" id="preview-resume-btn" class="p-2 hover:bg-surface-container-high rounded-lg text-outline" title="Preview resume">
<span class="material-symbols-outlined text-[20px]" data-icon="visibility">visibility</span>
</button>
<button type="button" id="refresh-parse-btn" class="p-2 hover:bg-surface-container-high rounded-lg text-outline" title="Re-upload">
<span class="material-symbols-outlined text-[20px]" data-icon="refresh">refresh</span>
</button>
</div>
</div>

<div id="preview-empty" class="flex-1 flex flex-col items-center justify-center p-12 text-center">
<div class="w-24 h-24 rounded-full bg-surface-container-low flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-5xl text-outline" data-icon="person_search">person_search</span>
</div>
<h4 class="font-title-md text-title-md mb-2">Awaiting Resume Upload</h4>
<p class="font-body-sm text-on-surface-variant max-w-sm">Upload a resume to see AI-extracted candidate data appear here in real time.</p>
</div>

<div id="preview-form" class="hidden flex flex-col flex-1">
<div class="p-card-padding space-y-8 flex-1 overflow-y-auto max-h-[520px]">
<section>
<h4 class="font-label-caps text-secondary mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-[16px]" data-icon="person">person</span>
Identity Profile
</h4>
<div class="flex items-center gap-4 mb-4">
<div class="w-16 h-16 rounded-full bg-gradient-to-br from-secondary to-indigo-600 flex items-center justify-center text-white font-bold text-xl" id="profile-avatar">?</div>
<p class="font-body-sm text-on-surface-variant">Profile image placeholder — auto-generated from initials</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="space-y-1 md:col-span-2">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="full_name">Full Name</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="text" id="full_name" name="full_name"/>
</div>
<div class="space-y-1">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="current_title">Current Job Title</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="text" id="current_title" name="current_title"/>
</div>
<div class="space-y-1">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="email">Email</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="email" id="email" name="email" value="{{ auth()->user()->isUser() ? auth()->user()->email : '' }}"/>
</div>
<div class="space-y-1">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="phone">Phone Number</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="text" id="phone" name="phone"/>
</div>
<div class="space-y-1 md:col-span-2">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="location">Location</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="text" id="location" name="location"/>
</div>
</div>
</section>

<section>
<div class="flex items-center justify-between mb-4">
<h4 class="font-label-caps text-secondary flex items-center gap-2">
<span class="material-symbols-outlined text-[16px]" data-icon="psychology">psychology</span>
Technical Skills
</h4>
<span id="skill-accuracy-badge" class="bg-secondary/10 text-secondary text-[10px] px-2 py-1 rounded font-bold">—</span>
</div>
<div id="skills-container" class="flex flex-wrap gap-2"></div>
<button type="button" id="add-skill-btn" class="mt-3 px-3 py-1 border border-dashed border-outline rounded-full text-body-sm text-outline-variant hover:text-secondary hover:border-secondary transition-colors">+ Add Skill</button>
</section>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<section>
<h4 class="font-label-caps text-secondary mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-[16px]" data-icon="history">history</span>
Experience
</h4>
<div class="space-y-3">
<div class="space-y-1">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="experience_years">Total Years</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="number" min="0" max="50" id="experience_years" name="experience_years"/>
</div>
<div class="space-y-1">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="seniority_level">Seniority Level</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="text" id="seniority_level" name="seniority_level" placeholder="e.g. Senior"/>
</div>
<div class="space-y-1">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="previous_companies">Previous Companies</label>
<textarea class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" id="previous_companies" name="previous_companies" rows="2" placeholder="Comma-separated"></textarea>
</div>
</div>
</section>
<section>
<h4 class="font-label-caps text-secondary mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-[16px]" data-icon="school">school</span>
Education
</h4>
<div class="space-y-3">
<div class="space-y-1">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="education">Degree</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="text" id="education" name="education"/>
</div>
<div class="space-y-1">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="university">University</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="text" id="university" name="university"/>
</div>
<div class="space-y-1">
<label class="text-[11px] font-label-caps text-on-surface-variant" for="graduation_year">Graduation Year</label>
<input class="w-full bg-surface-container-low border-none rounded-lg font-body-sm px-4 py-2 focus:ring-2 focus:ring-secondary/50" type="number" id="graduation_year" name="graduation_year" min="1950" max="{{ date('Y') + 1 }}"/>
</div>
</div>
</section>
</div>

<section class="pt-4">
<h4 class="font-label-caps text-secondary mb-4">AI Compatibility Score</h4>
<input type="hidden" id="ai_score" name="ai_score"/>
<input type="hidden" id="ai_recommendation" name="ai_recommendation"/>
<input type="hidden" id="parsing_log_id" name="parsing_log_id" value=""/>
<div class="bg-primary-container p-6 rounded-xl flex items-center gap-6">
<div class="relative w-20 h-20 flex-shrink-0">
<svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
<path class="stroke-on-primary-container/20" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="3"></path>
<path id="match-score-ring" class="stroke-secondary transition-all duration-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-dasharray="85, 100" stroke-linecap="round" stroke-width="3"></path>
</svg>
<div id="match-score-text" class="absolute inset-0 flex items-center justify-center font-bold text-white text-lg">—</div>
</div>
<div class="flex-1">
<p id="match-title" class="text-white font-title-md">Upload resume for AI analysis</p>
<p id="match-description" class="text-on-primary-container text-body-sm">Our engine will generate a hiring recommendation and compatibility score.</p>
</div>
</div>
</section>
</div>

<div class="p-card-padding border-t border-outline-variant bg-surface flex justify-end gap-3">
<button type="button" id="cancel-profile-btn" class="px-6 py-2 rounded-xl border border-outline-variant font-label-caps hover:bg-surface-container-high transition-all">Cancel</button>
<button type="button" id="create-profile-btn" class="px-8 py-2 rounded-xl bg-secondary text-on-secondary font-label-caps hover:shadow-lg hover:shadow-secondary/20 transition-all">Create Profile</button>
</div>
</div>
</div>
</div>
</div>
</div>

<footer class="w-full py-8 border-t border-outline-variant bg-surface mt-20">
<div class="flex flex-col md:flex-row justify-between items-center px-container-margin max-w-7xl mx-auto">
<div class="mb-4 md:mb-0">
<p class="font-body-sm text-body-sm text-on-surface-variant">© 2024 Elements HR Services. All rights reserved.</p>
</div>
<div class="flex gap-6">
<a class="font-label-caps text-on-surface-variant hover:text-primary transition-opacity opacity-80 hover:opacity-100" href="{{ route('landing') }}">Privacy Policy</a>
<a class="font-label-caps text-on-surface-variant hover:text-primary transition-opacity opacity-80 hover:opacity-100" href="{{ route('landing') }}">Terms of Service</a>
<a class="font-label-caps text-on-surface-variant hover:text-primary transition-opacity opacity-80 hover:opacity-100" href="{{ route('login') }}">Contact Support</a>
</div>
</div>
</footer>
</main>

<div id="toast-root" aria-live="polite"></div>

<div id="resume-preview-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
<div class="bg-white dark:bg-surface rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
<div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant">
<h3 class="font-title-md">Resume Preview</h3>
<button type="button" id="close-resume-modal" class="p-2 rounded-lg hover:bg-surface-container-high">
<span class="material-symbols-outlined" data-icon="close">close</span>
</button>
</div>
<iframe id="resume-preview-frame" class="flex-1 w-full min-h-[70vh] bg-surface-container-low" title="Resume preview"></iframe>
</div>
</div>
@endsection

@push('scripts')
<script>
window.resumeUploadConfig = {
    uploadUrl: @json(route($rp.'.resume.upload.store')),
    profileUrl: @json(route($rp.'.resume.profile.store')),
    maxBytes: {{ config('resume.max_upload_kb') * 1024 }},
    isHr: @json(auth()->user()->isHr()),
    prefill: @json($prefillData),
};
</script>
<script src="{{ asset('js/resume-upload.js') }}"></script>
<script src="{{ asset('js/candidate-topbar.js') }}"></script>
<script>
document.getElementById('full_name')?.addEventListener('input', function () {
    const avatar = document.getElementById('profile-avatar');
    if (!avatar) return;
    const parts = this.value.trim().split(/\s+/).filter(Boolean);
    avatar.textContent = parts.length >= 2
        ? (parts[0][0] + parts[1][0]).toUpperCase()
        : (parts[0]?.[0] || '?').toUpperCase();
});
</script>
@endpush
