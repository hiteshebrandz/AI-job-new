@extends('layouts.app')

@section('title', 'Application Details')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'admin_analytics_dashboard.css')

@section('tailwind-config', 'tailwind-config-admin.js')

@push('head-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('partials.nav.admin-sidebar')
<main class="lg:ml-[280px] min-h-screen pb-12 transition-all duration-300">
<header class="fixed top-0 right-0 w-full lg:w-[calc(100%-280px)] h-16 z-40 glass-panel border-b border-outline-variant flex items-center px-6 lg:px-8">
    <a href="{{ route('admin.job-applications.index') }}" class="btn-ghost py-1.5 px-3 text-[13px] flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back
    </a>
</header>

<section class="pt-24 px-6 lg:px-8 max-w-[1200px] mx-auto space-y-5 animate-fade-in">

    {{-- Candidate header card --}}
    <div class="glass-card p-6 flex flex-wrap gap-6 items-start">
        <div class="w-20 h-20 rounded-full flex items-center justify-center flex-shrink-0 text-[28px] font-extrabold text-secondary" style="background: rgba(70,72,212,0.12); border: 2px solid rgba(70,72,212,0.25);">
            {{ $candidate?->initials() ?? strtoupper(substr($application->user->name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-[200px]">
            <h1 class="text-[22px] font-extrabold text-on-surface mb-0.5">{{ $candidate?->full_name ?? $application->user->name }}</h1>
            <p class="text-[14px] text-on-surface-variant">{{ $application->user->email }}</p>
            <p class="text-[13px] text-on-surface-variant">{{ $candidate?->phone ?? '—' }}</p>
            @if($matchScore)
                <p class="mt-2 font-bold text-secondary">{{ $matchScore }}% Match</p>
            @endif
        </div>
        <div class="flex flex-col gap-3 min-w-[200px]">
            <label class="text-[11px] uppercase tracking-wider font-semibold text-on-surface-variant">Update Status</label>
            <select id="admin-status-select" data-url="{{ route('admin.job-applications.status', $application) }}" class="input-dark">
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @if ($candidate?->resume_path)
                <a href="{{ route('admin.job-applications.resume', $application) }}" class="btn-secondary py-2 px-4 text-[13px] text-center">
                    <span class="material-symbols-outlined text-[15px]">download</span> Download Resume
                </a>
            @endif
        </div>
    </div>

    {{-- Details grid --}}
    <div class="grid lg:grid-cols-2 gap-5">
        {{-- Candidate profile --}}
        <div class="glass-card p-5">
            <h2 class="text-[16px] font-bold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[18px]">person</span> Candidate Profile
            </h2>
            <div class="space-y-2 text-[13px]">
                <p><span class="text-on-surface-variant">Title:</span> <span class="text-on-surface">{{ $candidate?->current_title ?? '—' }}</span></p>
                <p><span class="text-on-surface-variant">Experience:</span> <span class="text-on-surface">{{ $candidate?->experience_years ?? '—' }} years</span></p>
                <p><span class="text-on-surface-variant">Education:</span> <span class="text-on-surface">{{ $candidate?->education ?? '—' }}</span></p>
                <p><span class="text-on-surface-variant">University:</span> <span class="text-on-surface">{{ $candidate?->university ?? '—' }}</span></p>
            </div>
            @if ($candidate?->ai_recommendation)
                <p class="text-[13px] text-on-surface-variant mt-4 leading-relaxed border-t border-outline-variant pt-4">
                    <strong class="text-on-surface-variant">AI Summary:</strong> {{ $candidate->ai_recommendation }}
                </p>
            @endif
            @if ($candidate?->skills)
                <div class="flex flex-wrap gap-2 mt-4">
                    @foreach ($candidate->skills as $skill)
                        <span class="skill-tag">{{ $skill }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Job details --}}
        <div class="glass-card p-5">
            <h2 class="text-[16px] font-bold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[18px]">work</span> Applied Job
            </h2>
            <p class="text-[16px] font-semibold text-on-surface mb-1">{{ $application->job->title }}</p>
            <p class="text-[14px] text-on-surface-variant mb-1">{{ $application->job->company_name }}</p>
            <p class="text-[13px] text-on-surface-variant mb-1">{{ $application->job->displayLocation() }} · {{ $application->job->displaySalary() }}</p>
            <p class="text-[13px] text-on-surface-variant">Applied {{ $application->applied_at->format('M j, Y g:i A') }}</p>
            <p class="text-[13px] text-on-surface-variant mt-4 border-t border-outline-variant pt-4">
                Saved jobs: <strong class="text-on-surface">{{ $savedJobsCount }}</strong> ·
                Applied jobs: <strong class="text-on-surface">{{ $appliedJobsCount }}</strong>
            </p>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="glass-card p-5">
        <h2 class="text-[16px] font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary text-[18px]">timeline</span> Application Timeline
        </h2>
        <ul class="space-y-3 text-[13px]">
            <li class="flex items-center gap-2 text-on-surface-variant">
                <span class="material-symbols-outlined text-[var(--badge-success-text)] text-[18px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                Applied — {{ $application->applied_at->format('M j, Y') }}
            </li>
            <li class="flex items-center gap-2 text-on-surface-variant">
                <span class="material-symbols-outlined text-secondary text-[18px]" style="font-variation-settings:'FILL' 1;">info</span>
                Current: {{ \App\Models\JobApplication::statusLabel($application->status) }}
            </li>
        </ul>
    </div>

</section>
</main>
<div id="toast-root" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
window.adminApplicationDetailConfig = {
    csrf: document.querySelector('meta[name="csrf-token"]')?.content,
};
</script>
<script src="{{ asset('js/admin-application-detail.js') }}"></script>
@endpush
