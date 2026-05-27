@extends('layouts.app')

@section('title', 'Try AI Resume Tools — No Login Required')

@section('body-class', 'bg-background text-on-surface font-body-md overflow-x-hidden')

@section('page-css', 'guest_tools.css')

@section('tailwind-config', 'tailwind-config-default.js')

@push('head-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('partials.nav.public-header')

<section class="guest-hero pt-24 pb-16 min-h-screen">
    <div class="max-w-5xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-10 animate-fade-in">
            <span class="section-label mb-4 inline-flex">
                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">verified_user</span>
                Free Guest Trial
            </span>
            <h1 class="text-[38px] lg:text-[48px] font-extrabold tracking-tight mb-4" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">
                Test Your Resume <span class="gradient-text-violet">Without Signing Up</span>
            </h1>
            <p class="text-[16px] max-w-2xl mx-auto leading-relaxed" style="color:var(--text-muted);">
                Try our AI Resume Tester and ATS Compatibility Checker instantly. You get <strong>3 free attempts per tool</strong> — then sign in to keep going.
            </p>
        </div>

        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl text-[14px]" style="background:rgba(185,28,28,0.08); border:1px solid rgba(185,28,28,0.25); color:#b91c1c;">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center mb-8">
            <button type="button" data-guest-tab="resume" class="guest-tab {{ $activeTool === 'resume' ? 'active' : '' }} flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-[14px] font-semibold">
                <span class="material-symbols-outlined text-[18px]">description</span>
                Resume Test
            </button>
            <button type="button" data-guest-tab="ats" class="guest-tab {{ $activeTool === 'ats' ? 'active' : '' }} flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-[14px] font-semibold">
                <span class="material-symbols-outlined text-[18px]">analytics</span>
                ATS Compatibility
            </button>
        </div>

        {{-- Resume Test Panel --}}
        <div id="panel-resume" class="guest-panel {{ $activeTool === 'resume' ? 'active' : '' }}">
            <div class="glass-card p-8 lg:p-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-[22px] font-bold" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">AI Resume Tester</h2>
                        <p class="text-[13px] mt-1" style="color:var(--text-muted);">Upload PDF, DOC, DOCX, or TXT for an instant AI score and skill insights.</p>
                    </div>
                    <span id="resume-attempt-badge" class="guest-attempt-badge {{ ($attempts['resume_test']['remaining'] ?? 3) <= 1 && !($attempts['resume_test']['locked'] ?? false) ? 'warning' : '' }} {{ ($attempts['resume_test']['locked'] ?? false) ? 'locked' : '' }}">
                        @if ($attempts['resume_test']['locked'])
                            Limit reached — login required
                        @elseif ($attempts['resume_test']['remaining'] === 1)
                            1 attempt left
                        @else
                            {{ $attempts['resume_test']['used'] }}/{{ $maxAttempts }} used
                        @endif
                    </span>
                </div>

                <div id="resume-lock-banner" class="guest-login-banner p-5 mb-6 {{ $attempts['resume_test']['locked'] ? '' : 'hidden' }}">
                    <p class="text-[14px] font-semibold mb-2" style="color:var(--text-heading);">You've used all 3 free resume tests</p>
                    <p class="text-[13px] mb-4" style="color:var(--text-muted);">Create a free account to continue testing resumes and save your results.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="btn-primary py-2.5 px-5 text-[13px]">Log In</a>
                        <a href="{{ route('register') }}" class="btn-ghost py-2.5 px-5 text-[13px]">Create Account</a>
                    </div>
                </div>

                <div id="resume-error" class="hidden mb-4 p-4 rounded-xl text-[13px]" style="background:rgba(185,28,28,0.08); border:1px solid rgba(185,28,28,0.25); color:#b91c1c;"></div>

                <form id="resume-upload-form" action="{{ route('tools.guest.resume.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="resume-drop-zone"
                         class="guest-drop-zone p-10 text-center cursor-pointer mb-4 {{ $attempts['resume_test']['locked'] ? 'locked' : '' }}"
                         onclick="document.getElementById('resume-file-input').click()">
                        <span class="material-symbols-outlined text-[40px]" style="color:var(--brand-primary);">cloud_upload</span>
                        <p class="font-semibold mt-3 text-[14px]" style="color:var(--text-primary);">Click or drag & drop your resume</p>
                        <p class="text-[12px] mt-1" style="color:var(--text-muted);">PDF, DOC, DOCX, TXT · Max {{ (int) (config('resume.max_upload_kb') / 1024) }} MB</p>
                        <input type="file" id="resume-file-input" name="resume" accept=".pdf,.doc,.docx,.txt,application/pdf" class="hidden" {{ $attempts['resume_test']['locked'] ? 'disabled' : '' }}>
                    </div>

                    <div id="resume-file-preview" class="hidden mb-4 p-3 rounded-xl flex items-center gap-3" style="background:var(--bg-surface-low); border:1px solid var(--border-subtle);">
                        <span class="material-symbols-outlined" style="color:var(--brand-primary);">description</span>
                        <div class="flex-1 min-w-0 text-left">
                            <p id="resume-file-name" class="text-[13px] font-semibold truncate"></p>
                            <p id="resume-file-size" class="text-[11px]" style="color:var(--text-muted);"></p>
                        </div>
                        <button type="button" onclick="guestToolsClearFile('resume')" class="text-on-surface-variant">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>

                    <div id="resume-upload-progress" class="hidden mb-4">
                        <div class="h-2 rounded-full overflow-hidden" style="background:var(--bg-surface-high);">
                            <div id="resume-upload-progress-bar" class="h-full progress-bar-fill transition-all" style="width:0%"></div>
                        </div>
                    </div>

                    <div id="resume-processing" class="hidden mb-4 flex items-center gap-2 text-[13px]" style="color:var(--text-muted);">
                        <span class="material-symbols-outlined animate-spin text-[18px]" style="color:var(--brand-secondary);">sync</span>
                        Analyzing your resume…
                    </div>

                    <button type="submit" id="resume-upload-btn" class="hidden w-full btn-primary py-3 text-[14px] font-semibold justify-center" {{ $attempts['resume_test']['locked'] ? 'disabled' : '' }}>
                        <span id="resume-upload-btn-label" class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">upload_file</span>
                            Test My Resume
                        </span>
                    </button>
                </form>

                <div id="resume-result" class="hidden mt-6"></div>
            </div>
        </div>

        {{-- ATS Panel --}}
        <div id="panel-ats" class="guest-panel {{ $activeTool === 'ats' ? 'active' : '' }}">
            <div class="glass-card p-8 lg:p-10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-[22px] font-bold" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">ATS Compatibility Checker</h2>
                        <p class="text-[13px] mt-1" style="color:var(--text-muted);">See how applicant tracking systems read your resume and what to improve.</p>
                    </div>
                    <span id="ats-attempt-badge" class="guest-attempt-badge {{ ($attempts['ats_check']['remaining'] ?? 3) <= 1 && !($attempts['ats_check']['locked'] ?? false) ? 'warning' : '' }} {{ ($attempts['ats_check']['locked'] ?? false) ? 'locked' : '' }}">
                        @if ($attempts['ats_check']['locked'])
                            Limit reached — login required
                        @elseif ($attempts['ats_check']['remaining'] === 1)
                            1 attempt left
                        @else
                            {{ $attempts['ats_check']['used'] }}/{{ $maxAttempts }} used
                        @endif
                    </span>
                </div>

                <div id="ats-lock-banner" class="guest-login-banner p-5 mb-6 {{ $attempts['ats_check']['locked'] ? '' : 'hidden' }}">
                    <p class="text-[14px] font-semibold mb-2" style="color:var(--text-heading);">You've used all 3 free ATS checks</p>
                    <p class="text-[13px] mb-4" style="color:var(--text-muted);">Sign in to run unlimited checks and generate an optimized resume PDF.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="btn-primary py-2.5 px-5 text-[13px]">Log In</a>
                        <a href="{{ route('register') }}" class="btn-ghost py-2.5 px-5 text-[13px]">Create Account</a>
                    </div>
                </div>

                <div id="ats-error" class="hidden mb-4 p-4 rounded-xl text-[13px]" style="background:rgba(185,28,28,0.08); border:1px solid rgba(185,28,28,0.25); color:#b91c1c;"></div>

                <form id="ats-upload-form" action="{{ route('tools.guest.ats.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="ats-drop-zone"
                         class="guest-drop-zone p-10 text-center cursor-pointer mb-4 {{ $attempts['ats_check']['locked'] ? 'locked' : '' }}"
                         onclick="document.getElementById('ats-file-input').click()">
                        <span class="material-symbols-outlined text-[40px]" style="color:#6063ee;">fact_check</span>
                        <p class="font-semibold mt-3 text-[14px]" style="color:var(--text-primary);">Click or drag & drop your resume</p>
                        <p class="text-[12px] mt-1" style="color:var(--text-muted);">PDF, DOC, or DOCX · Max {{ (int) (config('resume.optimizer_max_upload_kb', config('resume.max_upload_kb')) / 1024) }} MB</p>
                        <input type="file" id="ats-file-input" name="resume" accept=".pdf,.doc,.docx,application/pdf" class="hidden" {{ $attempts['ats_check']['locked'] ? 'disabled' : '' }}>
                    </div>

                    <div id="ats-file-preview" class="hidden mb-4 p-3 rounded-xl flex items-center gap-3" style="background:var(--bg-surface-low); border:1px solid var(--border-subtle);">
                        <span class="material-symbols-outlined" style="color:#6063ee;">description</span>
                        <div class="flex-1 min-w-0 text-left">
                            <p id="ats-file-name" class="text-[13px] font-semibold truncate"></p>
                            <p id="ats-file-size" class="text-[11px]" style="color:var(--text-muted);"></p>
                        </div>
                        <button type="button" onclick="guestToolsClearFile('ats')" class="text-on-surface-variant">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>

                    <div id="ats-upload-progress" class="hidden mb-4">
                        <div class="h-2 rounded-full overflow-hidden" style="background:var(--bg-surface-high);">
                            <div id="ats-upload-progress-bar" class="h-full progress-bar-fill transition-all" style="width:0%; background:linear-gradient(90deg,#6063ee,#4648d4);"></div>
                        </div>
                    </div>

                    <div id="ats-processing" class="hidden mb-4 flex items-center gap-2 text-[13px]" style="color:var(--text-muted);">
                        <span class="material-symbols-outlined animate-spin text-[18px]" style="color:#6063ee;">sync</span>
                        Running ATS compatibility analysis…
                    </div>

                    <button type="submit" id="ats-upload-btn" class="hidden w-full btn-secondary py-3 text-[14px] font-semibold justify-center" {{ $attempts['ats_check']['locked'] ? 'disabled' : '' }}>
                        <span id="ats-upload-btn-label" class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">fact_check</span>
                            Check ATS Compatibility
                        </span>
                    </button>
                </form>

                <div id="ats-result" class="hidden mt-6"></div>
            </div>
        </div>

        <div class="mt-10 text-center">
            <p class="text-[13px] mb-4" style="color:var(--text-muted);">Already have an account?</p>
            <a href="{{ route('login') }}" class="btn-ghost py-2.5 px-6 text-[13px] inline-flex">Sign In</a>
            <span class="mx-2" style="color:var(--text-muted);">·</span>
            <a href="{{ route('register') }}" class="text-[13px] font-semibold" style="color:var(--brand-primary);">Create free account</a>
        </div>
    </div>
</section>

@include('partials.nav.public-footer')
@endsection

@push('scripts')
<script>
    window.guestToolsConfig = {
        loginUrl: @json(route('login')),
        registerUrl: @json(route('register')),
        resumeStatusUrlTemplate: @json(route('tools.guest.resume.status', ['log' => '__ID__'])),
        atsStatusUrlTemplate: @json(route('tools.guest.ats.status', ['run' => '__ID__'])),
        attempts: @json($attempts),
        activeTool: @json($activeTool),
    };
</script>
<script src="{{ asset('js/guest-tools.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var active = window.guestToolsConfig.activeTool;
        if (active === 'ats') {
            document.querySelector('[data-guest-tab="ats"]')?.click();
        }
    });
</script>
@endpush
