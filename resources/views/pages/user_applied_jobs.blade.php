@extends('layouts.candidate', ['activeNav' => 'jobs'])

@section('title', 'Applied Jobs')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'job_recommendations.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('page-main')
<div class="mb-10">
<h2 class="font-headline-lg text-headline-lg text-primary mb-2">Applied Jobs</h2>
<p class="font-body-md text-on-surface-variant">Track your applications and recruiter updates.</p>
</div>
<div class="space-y-6">
@forelse ($applications as $application)
@php $job = $application->job; @endphp
@if ($job)
<div class="bg-white p-card-padding rounded-xl border border-outline-variant shadow-sm">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
<div class="flex gap-6 flex-1 min-w-0">
<div class="w-16 h-16 bg-surface-container rounded-xl flex items-center justify-center flex-shrink-0">
<span class="font-title-md font-bold text-secondary">{{ $job->companyInitials() }}</span>
</div>
<div class="min-w-0">
<h3 class="font-headline-lg text-[22px] text-primary mb-1">{{ $job->title }}</h3>
<p class="font-title-md text-on-surface-variant mb-2">{{ $job->company_name }}</p>
<p class="font-body-sm text-on-surface-variant">Recruiter: {{ $job->hr->name ?? 'HR Team' }}</p>
<p class="font-body-sm text-on-surface-variant mt-1">Applied {{ $application->applied_at->format('M j, Y') }}</p>
</div>
</div>
<div class="flex flex-col items-end gap-3 flex-shrink-0">
@php
$statusColors = [
    'applied' => 'bg-secondary/10 text-secondary',
    'under_review' => 'bg-amber-100 text-amber-800',
    'shortlisted' => 'bg-emerald-100 text-emerald-800',
    'interview_scheduled' => 'bg-indigo-100 text-indigo-800',
    'rejected' => 'bg-red-100 text-red-800',
    'hired' => 'bg-primary/10 text-primary',
];
$statusClass = $statusColors[$application->status] ?? 'bg-surface-container-high text-on-surface-variant';
@endphp
<span class="px-4 py-1.5 rounded-full font-label-caps text-label-caps {{ $statusClass }}">{{ \App\Models\JobApplication::statusLabel($application->status) }}</span>
@if ($application->match_score)
<span class="font-body-sm text-secondary font-bold">{{ $application->match_score }}% Match</span>
@endif
<a href="{{ route('user.jobs.show', $job) }}" class="border border-secondary text-secondary px-6 py-2 rounded-xl font-title-md hover:bg-secondary/5 transition-all text-center">View Job</a>
</div>
</div>
</div>
@endif
@empty
<div class="bg-white p-12 rounded-xl border border-outline-variant text-center">
<span class="material-symbols-outlined text-5xl text-outline mb-4">assignment</span>
<p class="font-body-md text-on-surface-variant">You have not applied to any jobs yet.</p>
<a href="{{ route('user.jobs.recommendations') }}" class="inline-block mt-6 px-8 py-3 rounded-xl bg-secondary text-white font-title-md">Find Jobs</a>
</div>
@endforelse
</div>
@if ($applications->hasPages())
<div class="pt-4">{{ $applications->links() }}</div>
@endif
@endsection
