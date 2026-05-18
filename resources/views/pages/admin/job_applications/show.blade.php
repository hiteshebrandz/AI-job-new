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
<main class="ml-[280px] min-h-screen pb-12">
<header class="fixed top-0 right-0 w-[calc(100%-280px)] h-16 z-40 bg-surface/80 backdrop-blur-lg border-b border-outline-variant flex justify-between items-center px-8">
<a href="{{ route('admin.job-applications.index') }}" class="text-secondary font-label-caps flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">arrow_back</span> Back</a>
</header>
<section class="pt-24 px-8 max-w-[1200px] mx-auto space-y-6">
<div class="bg-white rounded-3xl p-8 border border-outline-variant shadow-sm flex flex-wrap gap-8 items-start">
<div class="w-24 h-24 rounded-full bg-secondary/10 flex items-center justify-center flex-shrink-0">
<span class="font-headline-lg text-secondary font-bold">{{ $candidate?->initials() ?? strtoupper(substr($application->user->name, 0, 2)) }}</span>
</div>
<div class="flex-1 min-w-[240px]">
<h1 class="font-headline-lg text-headline-lg text-primary mb-1">{{ $candidate?->full_name ?? $application->user->name }}</h1>
<p class="font-body-md text-on-surface-variant">{{ $application->user->email }}</p>
<p class="font-body-md text-on-surface-variant">{{ $candidate?->phone ?? '—' }}</p>
<p class="font-body-sm text-secondary mt-2 font-bold">{{ $matchScore ?? '—' }}@if($matchScore)% Match@endif</p>
</div>
<div class="flex flex-col gap-3">
<label class="font-label-caps text-on-surface-variant">Update Status</label>
<select id="admin-status-select" data-url="{{ route('admin.job-applications.status', $application) }}" class="bg-surface-container-low border-none rounded-lg px-4 py-2 text-body-sm">
@foreach ($statuses as $value => $label)
<option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>
@endforeach
</select>
@if ($candidate?->resume_path)
<a href="{{ route('admin.job-applications.resume', $application) }}" class="px-4 py-2 border border-secondary text-secondary rounded-xl font-label-caps text-center hover:bg-secondary/5">Download Resume</a>
@endif
</div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
<div class="bg-white rounded-3xl p-card-padding border border-outline-variant">
<h2 class="font-title-md text-title-md text-primary mb-4">Candidate Profile</h2>
<p class="font-body-sm text-on-surface-variant mb-2"><strong>Title:</strong> {{ $candidate?->current_title ?? '—' }}</p>
<p class="font-body-sm text-on-surface-variant mb-2"><strong>Experience:</strong> {{ $candidate?->experience_years ?? '—' }} years</p>
<p class="font-body-sm text-on-surface-variant mb-2"><strong>Education:</strong> {{ $candidate?->education ?? '—' }}</p>
<p class="font-body-sm text-on-surface-variant mb-4"><strong>University:</strong> {{ $candidate?->university ?? '—' }}</p>
@if ($candidate?->ai_recommendation)
<p class="font-body-sm text-on-surface-variant mb-4 leading-relaxed"><strong>Resume summary:</strong> {{ $candidate->ai_recommendation }}</p>
@endif
@if ($candidate?->skills)
<div class="flex flex-wrap gap-2">
@foreach ($candidate->skills as $skill)
<span class="px-3 py-1 bg-secondary/10 text-secondary rounded-full text-body-sm">{{ $skill }}</span>
@endforeach
</div>
@endif
</div>
<div class="bg-white rounded-3xl p-card-padding border border-outline-variant">
<h2 class="font-title-md text-title-md text-primary mb-4">Applied Job</h2>
<p class="font-body-md font-semibold text-primary">{{ $application->job->title }}</p>
<p class="font-body-sm text-on-surface-variant mb-2">{{ $application->job->company_name }}</p>
<p class="font-body-sm text-on-surface-variant mb-2">{{ $application->job->displayLocation() }} · {{ $application->job->displaySalary() }}</p>
<p class="font-body-sm text-on-surface-variant">Applied {{ $application->applied_at->format('M j, Y g:i A') }}</p>
<p class="font-body-sm text-on-surface-variant mt-4">Saved jobs: {{ $savedJobsCount }} · Applied jobs: {{ $appliedJobsCount }}</p>
</div>
</div>

<div class="bg-white rounded-3xl p-card-padding border border-outline-variant">
<h2 class="font-title-md text-title-md text-primary mb-4">Application Timeline</h2>
<ul class="space-y-3 font-body-sm text-on-surface-variant">
<li class="flex gap-2"><span class="material-symbols-outlined text-secondary text-[18px]">check_circle</span> Applied — {{ $application->applied_at->format('M j, Y') }}</li>
<li class="flex gap-2"><span class="material-symbols-outlined text-secondary text-[18px]">info</span> Current: {{ \App\Models\JobApplication::statusLabel($application->status) }}</li>
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
