@extends('layouts.candidate', ['activeNav' => 'dashboard'])

@section('title', 'Candidate Dashboard')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'candidate_dashboard.css')

@section('tailwind-config', 'tailwind-config-candidate.js')

@push('candidate-header-actions')
<a href="{{ route('user.resume.upload') }}" class="btn-primary py-2 px-4 text-[13px]">
    <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
    Upload Resume
</a>
@endpush

@section('page-main')
<!-- Welcome Banner -->
<section class="dashboard-hero relative overflow-hidden rounded-3xl p-8 lg:p-10 mb-8 animate-fade-in">
    <!-- Floating orbs -->
    <div class="blob blob-violet w-48 h-48 -top-10 -right-10 opacity-30 animate-blob"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="badge-ai">AI Powered</span>
                <span class="text-[12px]" style="color: var(--text-muted);">Profile Active</span>
            </div>
            <h1 class="text-[32px] lg:text-[40px] font-extrabold mb-3 tracking-tight" style="color: var(--text-hero);">
                Welcome back, <span class="gradient-text-violet">{{ $user->name }}</span>
            </h1>
            <p class="text-[15px] max-w-lg leading-relaxed" style="color: var(--text-hero-muted);">Your profile is active and being matched with opportunities. Upload your resume to boost your AI match score.</p>
        </div>

        <!-- AI Score Ring -->
        <div class="flex items-center gap-6 flex-shrink-0">
            <div class="text-center">
                <div class="relative w-24 h-24 mx-auto mb-2">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 96 96">
                        <circle cx="48" cy="48" r="40" fill="transparent" stroke="#334155" stroke-width="6"/>
                        <circle cx="48" cy="48" r="40" fill="transparent"
                            stroke="url(#scoreGrad)"
                            stroke-width="6"
                            stroke-linecap="round"
                            stroke-dasharray="251.3"
                            stroke-dashoffset="50"
                            class="score-ring"
                        />
                        <defs>
                            <linearGradient id="scoreGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#7C3AED"/>
                                <stop offset="100%" stop-color="#06B6D4"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-[22px] font-extrabold text-[#C4B5FD]" id="score-counter">80</span>
                        <span class="text-[9px] text-[#475569] uppercase tracking-wide">Score</span>
                    </div>
                </div>
                <p class="text-[11px] text-[#64748B] uppercase tracking-wide">AI Profile Score</p>
            </div>
        </div>
    </div>

    <div class="relative z-10 flex flex-wrap gap-3 mt-6">
        <a href="{{ route('user.resume.upload') }}" class="btn-primary py-2.5 px-5 text-[14px]">
            <span class="material-symbols-outlined text-[16px]">upload_file</span>
            Upload Resume
        </a>
        <a href="{{ route('user.jobs.recommendations') }}" class="btn-secondary py-2.5 px-5 text-[14px]">
            <span class="material-symbols-outlined text-[16px]">work</span>
            Explore Jobs
        </a>
        <a href="{{ route('user.resume.analytics') }}" class="btn-ghost py-2.5 px-5 text-[14px]">
            <span class="material-symbols-outlined text-[16px]">analytics</span>
            Analytics
        </a>
    </div>
</section>

<!-- Quick Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-fade-in-delay-1">
    <div class="glass-card stat-card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-[#1E1B4B] flex items-center justify-center">
                <span class="material-symbols-outlined text-[#8B5CF6] text-[20px]">work</span>
            </div>
            <span class="badge-ai text-[10px]">Live</span>
        </div>
        <p class="text-[28px] font-extrabold text-[#E2E8F0] mb-1" data-counter="{{ $matchScore }}">0</p>
        <p class="text-[12px] text-[#64748B]">AI Resume Score</p>
    </div>

    <div class="glass-card stat-card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-[#164E63] flex items-center justify-center">
                <span class="material-symbols-outlined text-[#06B6D4] text-[20px]">assignment_turned_in</span>
            </div>
        </div>
        <p class="text-[28px] font-extrabold text-[#E2E8F0] mb-1" data-counter="{{ $appliedCount }}">0</p>
        <p class="text-[12px] text-[#64748B]">Applications</p>
    </div>

    <div class="glass-card stat-card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-[#14532D] flex items-center justify-center">
                <span class="material-symbols-outlined text-[#34D399] text-[20px]">bookmark</span>
            </div>
        </div>
        <p class="text-[28px] font-extrabold text-[#E2E8F0] mb-1" data-counter="{{ $savedCount }}">0</p>
        <p class="text-[12px] text-[#64748B]">Saved Jobs</p>
    </div>

    <div class="glass-card stat-card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-[#451A03] flex items-center justify-center">
                <span class="material-symbols-outlined text-[#FBBF24] text-[20px]">notifications</span>
            </div>
            @if($unreadNotifs > 0)
                <span class="badge-ai text-[10px]">{{ $unreadNotifs }} new</span>
            @endif
        </div>
        <p class="text-[28px] font-extrabold text-[#E2E8F0] mb-1" data-counter="{{ $activeApplications }}">0</p>
        <p class="text-[12px] text-[#64748B]">Active Applications</p>
    </div>
</div>

<!-- Action Cards -->
<div class="grid md:grid-cols-2 gap-5 mb-8 animate-fade-in-delay-2">
    <a href="{{ route('user.resume.upload') }}" class="glass-card glass-card-lift p-7 group relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-[#7C3AED] to-[#06B6D4] opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-12 h-12 rounded-2xl gradient-violet flex items-center justify-center mb-5 group-hover:scale-110 transition-transform ai-glow">
            <span class="material-symbols-outlined text-white text-[24px]">upload_file</span>
        </div>
        <h2 class="text-[18px] font-bold text-[#E2E8F0] mb-2">Upload Resume</h2>
        <p class="text-[14px] text-[#64748B]">Parse and optimize your resume with our AI engine for maximum match accuracy.</p>
        <div class="flex items-center gap-2 mt-5 text-[#8B5CF6] text-[13px] font-semibold">
            <span>Get started</span>
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </div>
    </a>

    <a href="{{ route('user.jobs.recommendations') }}" class="glass-card glass-card-lift p-7 group relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-[#06B6D4] to-[#7C3AED] opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-12 h-12 rounded-2xl gradient-cyan-violet flex items-center justify-center mb-5 group-hover:scale-110 transition-transform ai-glow-cyan">
            <span class="material-symbols-outlined text-white text-[24px]">work</span>
        </div>
        <h2 class="text-[18px] font-bold text-[#E2E8F0] mb-2">AI Job Matches</h2>
        <p class="text-[14px] text-[#64748B]">Browse AI-curated opportunities that align with your skills and experience.</p>
        <div class="flex items-center gap-2 mt-5 text-[#06B6D4] text-[13px] font-semibold">
            <span>Browse jobs</span>
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </div>
    </a>
</div>

<!-- Quick links row -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 animate-fade-in-delay-3">
    <a href="{{ route('user.resume.analytics') }}" class="glass-card p-4 flex flex-col items-center text-center gap-2 hover:border-[#8B5CF6]/40 transition-all group">
        <span class="material-symbols-outlined text-[#8B5CF6] text-[26px] group-hover:scale-110 transition-transform">analytics</span>
        <span class="text-[13px] font-medium text-[#94A3B8]">Analytics</span>
    </a>
    <a href="{{ route('user.saved-jobs') }}" class="glass-card p-4 flex flex-col items-center text-center gap-2 hover:border-[#8B5CF6]/40 transition-all group">
        <span class="material-symbols-outlined text-[#FBBF24] text-[26px] group-hover:scale-110 transition-transform">bookmark</span>
        <span class="text-[13px] font-medium text-[#94A3B8]">Saved Jobs</span>
    </a>
    <a href="{{ route('user.applied-jobs') }}" class="glass-card p-4 flex flex-col items-center text-center gap-2 hover:border-[#8B5CF6]/40 transition-all group">
        <span class="material-symbols-outlined text-[#34D399] text-[26px] group-hover:scale-110 transition-transform">assignment_turned_in</span>
        <span class="text-[13px] font-medium text-[#94A3B8]">Applications</span>
    </a>
    <a href="{{ route('user.settings.notifications') }}" class="glass-card p-4 flex flex-col items-center text-center gap-2 hover:border-[#8B5CF6]/40 transition-all group">
        <span class="material-symbols-outlined text-[#94A3B8] text-[26px] group-hover:scale-110 transition-transform">settings</span>
        <span class="text-[13px] font-medium text-[#94A3B8]">Settings</span>
    </a>
</div>

@include('partials.nav.dashboard-footer')
@endsection

@push('page-scripts')
<script>
// Animated counters
document.addEventListener('DOMContentLoaded', function () {
    var counters = document.querySelectorAll('[data-counter]');
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                var el = entry.target;
                var target = parseInt(el.getAttribute('data-counter'), 10);
                var duration = 800;
                var start = 0;
                var step = Math.ceil(target / (duration / 16));
                var timer = setInterval(function () {
                    start += step;
                    if (start >= target) { start = target; clearInterval(timer); }
                    el.textContent = start;
                }, 16);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(function (c) { observer.observe(c); });
});
</script>
@endpush
