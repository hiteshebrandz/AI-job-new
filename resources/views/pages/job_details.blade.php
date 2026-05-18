@extends('layouts.candidate', ['activeNav' => 'jobs'])

@section('title', $job->title)

@section('body-class', 'bg-background text-on-surface font-body-md')

@section('page-css', 'job_details.css')

@section('tailwind-config', 'tailwind-config-forms.js')

@section('page-main-full')
<section class="pt-24 pb-12 px-8 max-w-[1440px] mx-auto">
<div class="bg-white rounded-3xl p-8 border border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] flex flex-col md:flex-row justify-between items-start md:items-center gap-8 {{ $highlightApply ? 'ring-2 ring-secondary/40' : '' }}" id="apply-section">
<div class="flex items-center gap-6">
<div class="w-20 h-20 rounded-2xl bg-surface-container-low flex items-center justify-center p-4 border border-outline-variant">
@if ($company->logo)
<img alt="{{ $company->name }} Logo" class="w-full h-full object-contain" src="{{ asset('storage/'.$company->logo) }}"/>
@else
<span class="font-headline-lg text-secondary font-bold">{{ $company->initials() }}</span>
@endif
</div>
<div>
<h1 class="font-headline-lg text-headline-lg text-primary mb-1">{{ $job->title }}</h1>
<div class="flex flex-wrap gap-4 items-center">
<span class="flex items-center gap-1 font-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-base" data-icon="location_on">location_on</span> {{ $job->displayLocation() }}
</span>
<span class="flex items-center gap-1 font-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-base" data-icon="payments">payments</span> {{ $job->displaySalary() }}
</span>
<span class="bg-secondary/10 text-secondary px-3 py-1 rounded-full font-label-caps text-label-caps">{{ $job->job_type }}</span>
@if ($job->experience_required)
<span class="flex items-center gap-1 font-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-base" data-icon="work_history">work_history</span> {{ $job->experience_required }}
</span>
@endif
@if ($job->created_at)
<span class="flex items-center gap-1 font-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-base" data-icon="schedule">schedule</span> Posted {{ $job->created_at->diffForHumans() }}
</span>
@endif
@if ($job->application_deadline)
<span class="flex items-center gap-1 font-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-base" data-icon="event">event</span> Deadline {{ $job->application_deadline->format('M j, Y') }}
</span>
@endif
</div>
</div>
</div>
<div class="flex gap-4 w-full md:w-auto">
<button type="button" id="job-save-btn" data-saved="{{ $hasSaved ? '1' : '0' }}" class="flex-1 md:flex-none min-w-[140px] border {{ $hasSaved ? 'border-secondary bg-secondary/5 text-secondary' : 'border-outline text-on-surface-variant' }} font-title-md py-3 px-8 rounded-xl hover:bg-surface-container-low transition-all">{{ $hasSaved ? 'Saved' : 'Save Job' }}</button>
<button type="button" id="job-apply-btn" data-applied="{{ $hasApplied ? '1' : '0' }}" class="flex-1 md:flex-none min-w-[140px] {{ $hasApplied ? 'bg-surface-container-high text-on-surface-variant cursor-not-allowed' : 'gradient-button text-white shadow-lg' }} font-title-md py-3 px-10 rounded-xl active:scale-[0.98] transition-all text-center" {{ $hasApplied ? 'disabled' : '' }}>{{ $hasApplied ? 'Applied' : 'Apply Now' }}</button>
</div>
</div>
</section>

<section class="px-8 pb-16 max-w-[1440px] mx-auto">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<div class="lg:col-span-8 space-y-gutter">
<div class="bg-white rounded-3xl p-card-padding border border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<h2 class="font-title-md text-title-md text-primary mb-6">About the Role</h2>
<div class="space-y-4 font-body-md text-on-surface-variant leading-relaxed">
@if ($job->description)
@foreach (preg_split('/\r\n\r\n|\n\n/', $job->description) as $paragraph)
@if (trim($paragraph) !== '')
<p>{{ trim($paragraph) }}</p>
@endif
@endforeach
@else
<p>Role details will be updated soon.</p>
@endif
</div>
@if (count($skills) > 0)
<h3 class="font-title-md text-title-md text-primary mt-10 mb-4">Skills Required</h3>
<div class="flex flex-wrap gap-2 mb-2">
@foreach ($skills as $skill)
<span class="bg-secondary/10 text-secondary px-3 py-1 rounded-full font-label-caps text-label-caps">{{ $skill }}</span>
@endforeach
</div>
@endif
<h3 class="font-title-md text-title-md text-primary mt-10 mb-6">Key Responsibilities</h3>
<ul class="space-y-4">
@forelse ($responsibilities as $item)
<li class="flex gap-3 font-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
{{ $item }}
</li>
@empty
<li class="flex gap-3 font-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
Collaborate with cross-functional teams to deliver high-quality outcomes.
</li>
@endforelse
</ul>
<h3 class="font-title-md text-title-md text-primary mt-10 mb-6">Requirements</h3>
<ul class="space-y-4">
@forelse ($requirements as $item)
<li class="flex gap-3 font-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-secondary" data-icon="arrow_forward">arrow_forward</span>
{{ $item }}
</li>
@empty
@if ($job->minimum_qualification)
<li class="flex gap-3 font-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-secondary" data-icon="arrow_forward">arrow_forward</span>
{{ $job->minimum_qualification }}
</li>
@endif
@if ($job->preferred_qualification)
<li class="flex gap-3 font-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-secondary" data-icon="arrow_forward">arrow_forward</span>
{{ $job->preferred_qualification }}
</li>
@endif
@if (! $job->minimum_qualification && ! $job->preferred_qualification)
<li class="flex gap-3 font-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-secondary" data-icon="arrow_forward">arrow_forward</span>
Relevant experience and skills for this role.
</li>
@endif
@endforelse
</ul>
</div>
</div>

<aside class="lg:col-span-4 space-y-gutter">
<div class="bg-white rounded-3xl p-card-padding border border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<h2 class="font-title-md text-title-md text-primary mb-6">Company Information</h2>
<div class="flex items-center gap-4 mb-6">
<div class="w-12 h-12 rounded-lg bg-surface-container-low border border-outline-variant flex items-center justify-center p-2">
@if ($company->logo)
<img alt="{{ $company->name }}" class="w-full h-full object-contain" src="{{ asset('storage/'.$company->logo) }}"/>
@else
<span class="font-title-md text-secondary font-bold">{{ $company->initials() }}</span>
@endif
</div>
<div>
<span class="block font-title-md text-primary">{{ $company->name }}</span>
<span class="font-body-sm text-on-surface-variant">{{ $company->description ?? 'Human Capital Intelligence' }}</span>
</div>
</div>
<div class="space-y-4">
<div class="flex justify-between items-center py-3 border-b border-outline-variant">
<span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Industry</span>
<span class="font-body-sm text-primary font-semibold">{{ $company->industry ?? 'HR Tech' }}</span>
</div>
<div class="flex justify-between items-center py-3 border-b border-outline-variant">
<span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Company Size</span>
<span class="font-body-sm text-primary font-semibold">{{ $company->company_size ?? '500+ Employees' }}</span>
</div>
<div class="flex justify-between items-center py-3 border-b border-outline-variant">
<span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Founded</span>
<span class="font-body-sm text-primary font-semibold">{{ $company->founded ?? '2016' }}</span>
</div>
<div class="flex justify-between items-center py-3 border-b border-outline-variant">
<span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Recruiter</span>
<span class="font-body-sm text-primary font-semibold">{{ $job->hr->name ?? 'HR Team' }}</span>
</div>
<div class="flex justify-between items-center py-3 border-b border-outline-variant">
<span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Contact</span>
<span class="font-body-sm text-primary font-semibold truncate max-w-[160px]">{{ $job->hr->email ?? '—' }}</span>
</div>
<div class="flex justify-between items-center py-3">
<span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Match Score</span>
<span class="font-body-sm text-secondary font-semibold">{{ $matchScore }}%</span>
</div>
</div>
<button type="button" class="w-full mt-6 py-3 border border-secondary text-secondary font-title-md rounded-xl hover:bg-secondary/5 transition-all">View Company Profile</button>
</div>

<div class="bg-white rounded-3xl p-card-padding border border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<h2 class="font-title-md text-title-md text-primary mb-6">Perks &amp; Benefits</h2>
<div class="grid grid-cols-2 gap-4">
@foreach ($benefits as $benefit)
<div class="p-3 rounded-2xl bg-surface-container-low border border-outline-variant text-center">
<span class="material-symbols-outlined text-secondary mb-1" data-icon="{{ $benefit['icon'] }}">{{ $benefit['icon'] }}</span>
<span class="block font-label-caps text-label-caps text-on-surface-variant">{{ $benefit['label'] }}</span>
</div>
@endforeach
</div>
</div>

<div class="bg-white rounded-3xl p-card-padding border border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<h2 class="font-title-md text-title-md text-primary mb-6">Similar Jobs</h2>
<div class="space-y-6">
@forelse ($similarJobs as $similar)
<a class="group block" href="{{ route('user.jobs.show', $similar) }}">
<span class="block font-body-md text-primary font-semibold group-hover:text-secondary transition-colors">{{ $similar->title }}</span>
<span class="font-body-sm text-on-surface-variant">{{ $similar->displayLocation() }} · {{ $similar->displaySalary() }}</span>
</a>
@empty
<p class="font-body-sm text-on-surface-variant">No similar jobs at the moment.</p>
@endforelse
</div>
</div>
</aside>
</div>
</section>

<footer class="w-full py-8 border-t border-outline-variant bg-surface mt-12">
<div class="flex flex-col md:flex-row justify-between items-center px-container-margin max-w-7xl mx-auto gap-4">
<div class="flex items-center gap-2">
<span class="font-title-md text-title-md font-bold text-primary">Elements HR</span>
</div>
<div class="flex gap-8">
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Privacy Policy</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Terms of Service</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('login') }}">Contact Support</a>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">
© 2024 Elements HR Services. All rights reserved.
</p>
</div>
</footer>

<div id="toast-root" aria-live="polite"></div>
<div id="job-action-loader" class="hidden fixed inset-0 z-50 bg-black/20 backdrop-blur-[1px] flex items-center justify-center">
<div class="w-12 h-12 border-4 border-white/30 border-t-white rounded-full animate-spin"></div>
</div>
@endsection

@push('page-scripts')
<script>
window.jobDetailsConfig = {
    applyUrl: @json(route('user.jobs.apply', $job)),
    saveUrl: @json(route('user.jobs.save', $job)),
    highlightApply: @json($highlightApply),
};
</script>
<script src="{{ asset('js/job-details.js') }}"></script>
@endpush