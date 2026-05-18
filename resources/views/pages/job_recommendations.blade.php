@extends('layouts.candidate', ['activeNav' => 'jobs'])

@section('title', 'Job Recommendations')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'job_recommendations.css')

@section('tailwind-config', 'tailwind-config-default.js')

@push('candidate-header-left')
<div class="relative w-full max-w-xl focus-within:ring-2 focus-within:ring-secondary/50 rounded-lg transition-all">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant" data-icon="search">search</span>
<input class="w-full bg-surface-container-low border-none rounded-lg py-2 pl-10 pr-4 font-body-sm focus:ring-0" placeholder="Search for opportunities..." type="search" name="search" form="job-filters" value="{{ $search }}"/>
</div>
@endpush

@section('page-main-full')
<form id="job-filters" method="GET" action="{{ route('user.jobs.recommendations') }}">
<div class="pt-24 pb-12 px-container-margin max-w-[1440px]">
<!-- Header Section -->
<div class="mb-10">
<h2 class="font-headline-lg text-headline-lg text-primary mb-2">Job Recommendations</h2>
<p class="font-body-md text-body-md text-on-surface-variant">Curated opportunities based on your executive profile and AI-driven skill matching.</p>
</div>
<div class="flex gap-gutter">
<!-- Filter Sidebar -->
<aside class="w-72 flex-shrink-0 space-y-8">
<div class="bg-white p-card-padding rounded-xl border border-outline-variant shadow-sm">
<div class="flex items-center justify-between mb-6">
<h3 class="font-title-md text-title-md">Filters</h3>
<a href="{{ route('user.jobs.recommendations') }}" class="text-secondary font-label-caps text-label-caps hover:underline">Clear all</a>
</div>
<!-- Salary Range -->
<div class="mb-8">
<label class="font-label-caps text-label-caps block mb-4 uppercase text-on-surface-variant">Salary Range</label>
<div class="space-y-3">
@foreach (['100-150' => '$100k - $150k', '150-200' => '$150k - $200k', '200-plus' => '$200k+'] as $value => $label)
<label class="flex items-center gap-3 cursor-pointer group">
<input name="salary[]" value="{{ $value }}" @checked(in_array($value, $salaryBands, true)) class="rounded border-outline-variant text-secondary focus:ring-secondary filter-auto-submit" type="checkbox"/>
<span class="font-body-sm text-body-sm group-hover:text-secondary transition-colors">{{ $label }}</span>
</label>
@endforeach
</div>
</div>
<!-- Job Type -->
<div class="mb-8">
<label class="font-label-caps text-label-caps block mb-4 uppercase text-on-surface-variant">Job Type</label>
<div class="flex flex-wrap gap-2">
@foreach (['Full-time', 'Contract', 'Remote'] as $type)
@php
    $isActive = in_array($type, $jobTypes, true);
    $btnClass = $isActive
        ? 'px-3 py-1.5 rounded-full border border-secondary bg-secondary text-white font-label-caps text-label-caps'
        : 'px-3 py-1.5 rounded-full border border-outline-variant text-on-surface-variant font-label-caps text-label-caps hover:border-secondary hover:text-secondary';
@endphp
<label class="cursor-pointer">
<input type="checkbox" name="job_types[]" value="{{ $type }}" @checked($isActive) class="sr-only peer filter-auto-submit"/>
<span class="{{ $btnClass }} peer-checked:border-secondary peer-checked:bg-secondary peer-checked:text-white inline-block">{{ $type }}</span>
</label>
@endforeach
</div>
</div>
<!-- Distance -->
<div>
<label class="font-label-caps text-label-caps block mb-4 uppercase text-on-surface-variant">Distance</label>
<input class="w-full h-1 bg-surface-container-highest rounded-lg appearance-none cursor-pointer accent-secondary filter-auto-submit" type="range" name="distance" min="5" max="50" step="5" value="{{ $distance }}"/>
<div class="flex justify-between mt-2 font-label-caps text-label-caps text-on-surface-variant">
<span>5 miles</span>
<span id="distance-label">{{ $distance >= 50 ? '50+ miles' : $distance.' miles' }}</span>
</div>
</div>
</div>
<div class="bg-gradient-to-br from-secondary to-indigo-800 p-card-padding rounded-xl text-white shadow-lg overflow-hidden relative">
<div class="relative z-10">
<h4 class="font-title-md text-title-md mb-2">Premium Insights</h4>
<p class="font-body-sm text-body-sm opacity-90 mb-4">Unlock deep salary benchmarks and competitor applicant data.</p>
<button type="button" class="bg-white text-secondary w-full py-2 rounded-lg font-title-md text-title-md font-bold hover:bg-surface-bright transition-colors">Upgrade Now</button>
</div>
<div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
</div>
</aside>
<!-- Job Listings Column -->
<div class="flex-1 space-y-6">
<!-- Filter Status -->
<div class="flex items-center justify-between">
<p class="font-body-md text-body-md">Showing <span class="font-bold">{{ $jobs->total() }} {{ $jobs->total() === 1 ? 'Best Match' : 'Best Matches' }}</span></p>
<div class="flex items-center gap-2">
<span class="font-label-caps text-label-caps text-on-surface-variant">Sort by:</span>
<select name="sort" class="bg-transparent border-none font-title-md text-title-md text-secondary focus:ring-0 cursor-pointer filter-auto-submit">
<option value="match" @selected($sort === 'match')>Match Percentage</option>
<option value="salary" @selected($sort === 'salary')>Salary: High to Low</option>
<option value="recent" @selected($sort === 'recent')>Recently Posted</option>
</select>
</div>
</div>
<!-- Job Cards (Bento Grid Style) -->
<div class="grid grid-cols-1 gap-6">
@forelse ($jobs as $job)
<div class="bg-white p-card-padding rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow group relative overflow-hidden">
<div class="flex items-start justify-between">
<div class="flex gap-6">
<div class="w-16 h-16 bg-surface-container rounded-xl flex items-center justify-center p-3">
<span class="font-title-md text-title-md font-bold text-secondary">{{ $job->companyInitials() }}</span>
</div>
<div>
<h3 class="font-headline-lg text-[24px] text-primary group-hover:text-secondary transition-colors mb-1">{{ $job->title }}</h3>
<p class="font-title-md text-title-md text-on-surface-variant mb-4">
    {{ $job->company_name }}@if ($job->isNewPosting()) • <span class="text-secondary font-bold">New</span>@endif
</p>
<div class="flex flex-wrap gap-4">
<div class="flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-lg" data-icon="payments">payments</span>
<span class="font-body-sm text-body-sm">{{ $job->displaySalary() }}</span>
</div>
<div class="flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-lg" data-icon="{{ $job->usesRemoteIcon() ? 'public' : 'location_on' }}">{{ $job->usesRemoteIcon() ? 'public' : 'location_on' }}</span>
<span class="font-body-sm text-body-sm">{{ $job->displayLocation() }}</span>
</div>
<div class="flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-lg" data-icon="schedule">schedule</span>
<span class="font-body-sm text-body-sm">Posted {{ $job->created_at->diffForHumans(short: true) }}</span>
</div>
</div>
</div>
</div>
<!-- Match Badge -->
<div class="flex flex-col items-center">
<div class="relative w-24 h-24 flex items-center justify-center">
<svg class="w-full h-full">
<circle class="text-surface-variant" cx="48" cy="48" fill="transparent" r="40" stroke="currentColor" stroke-width="6"></circle>
@php $matchPct = $job->matchPercentage(auth()->user()->candidate); @endphp
<circle class="text-secondary progress-ring-circle" cx="48" cy="48" fill="transparent" r="40" stroke="currentColor" stroke-dasharray="251.2" stroke-dashoffset="{{ round(251.2 * (1 - $matchPct / 100), 1) }}" stroke-width="6"></circle>
</svg>
<div class="absolute inset-0 flex flex-col items-center justify-center">
<span class="font-headline-lg text-[22px] text-secondary font-extrabold">{{ $job->matchPercentage(auth()->user()->candidate) }}%</span>
<span class="font-label-caps text-[8px] uppercase tracking-tighter">Match</span>
</div>
</div>
@if ($loop->first && $jobs->currentPage() === 1)
<a href="{{ route('user.jobs.show', ['job' => $job, 'apply' => 1]) }}" class="mt-4 bg-primary text-white px-6 py-2 rounded-xl font-title-md text-title-md hover:bg-secondary transition-all active:scale-95 shadow-sm inline-block text-center">Quick Apply</a>
@else
<a href="{{ route('user.jobs.show', $job) }}" class="mt-4 border border-secondary text-secondary px-6 py-2 rounded-xl font-title-md text-title-md hover:bg-secondary/5 transition-all active:scale-95 inline-block text-center">View Details</a>
@endif
</div>
</div>
</div>
@empty
<div class="bg-white p-card-padding rounded-xl border border-outline-variant shadow-sm text-center py-16">
<span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4">work_off</span>
<p class="font-body-md text-on-surface-variant mb-2">No active jobs match your filters.</p>
<p class="font-body-sm text-on-surface-variant">Try adjusting filters or <a href="{{ route('user.jobs.recommendations') }}" class="text-secondary font-bold hover:underline">clear all</a>.</p>
</div>
@endforelse
</div>
<!-- Pagination -->
{{ $jobs->onEachSide(1)->links('partials.pagination.job-recommendations') }}
</div>
</div>
</div>
</form>
<footer class="bg-surface border-t border-outline-variant py-8 px-container-margin">
<div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
<div class="flex items-center gap-4">
<span class="font-title-md text-title-md font-bold text-primary dark:text-on-primary-fixed">Elements HR</span>
<span class="text-outline">|</span>
<p class="font-body-sm text-body-sm text-on-surface-variant">© 2024 Elements HR Services. All rights reserved.</p>
</div>
<div class="flex gap-8">
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100" href="{{ route('landing') }}">Privacy Policy</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100" href="{{ route('landing') }}">Terms of Service</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100" href="{{ route('login') }}">Contact Support</a>
</div>
</div>
</footer>
@endsection

@push('page-scripts')
<script>
(function () {
    const form = document.getElementById('job-filters');
    if (!form) return;

    const distanceInput = form.querySelector('input[name="distance"]');
    const distanceLabel = document.getElementById('distance-label');

    const updateDistanceLabel = () => {
        if (!distanceInput || !distanceLabel) return;
        const value = parseInt(distanceInput.value, 10);
        distanceLabel.textContent = value >= 50 ? '50+ miles' : value + ' miles';
    };

    updateDistanceLabel();

    form.querySelectorAll('.filter-auto-submit').forEach((el) => {
        el.addEventListener('change', () => {
            updateDistanceLabel();
            form.submit();
        });
    });

    const searchInput = form.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });
    }

    form.querySelectorAll('label:has(input[name="job_types[]"])').forEach((label) => {
        const checkbox = label.querySelector('input[type="checkbox"]');
        const span = label.querySelector('span');
        if (!checkbox || !span) return;

        const syncStyle = () => {
            if (checkbox.checked) {
                span.className = 'px-3 py-1.5 rounded-full border border-secondary bg-secondary text-white font-label-caps text-label-caps inline-block';
            } else {
                span.className = 'px-3 py-1.5 rounded-full border border-outline-variant text-on-surface-variant font-label-caps text-label-caps hover:border-secondary hover:text-secondary inline-block';
            }
        };

        syncStyle();
        checkbox.addEventListener('change', syncStyle);
    });
})();
</script>
@endpush

