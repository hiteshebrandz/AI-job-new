@extends('layouts.employer', ['activeNav' => 'dashboard'])

@section('title', 'HR Dashboard')

@section('body-class', 'bg-surface text-on-surface min-h-screen')

@section('page-css', 'employer_dashboard.css')

@section('tailwind-config', 'tailwind-config-employer.js')

@push('candidate-header-actions')
<a href="{{ route('hr.jobs.create') }}" class="px-4 py-2 bg-secondary text-white rounded-lg font-label-caps hover:shadow-lg transition-all">
Post a Job
</a>
@endpush

@section('employer-main')
@if (session('success'))
<div class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-body-sm">{{ session('success') }}</div>
@endif

<div class="mb-10">
<h1 class="font-headline-lg text-headline-lg text-primary">Welcome, {{ $user->name }}</h1>
<p class="font-body-md text-on-surface-variant">HR hiring dashboard</p>
</div>

<section class="grid md:grid-cols-2 gap-6 mb-10">
<a href="{{ route('hr.jobs.create') }}" class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant hover:border-secondary hover:shadow-lg transition-all group">
<span class="material-symbols-outlined text-secondary text-4xl mb-4 group-hover:scale-110 transition-transform">post_add</span>
<h2 class="font-title-md text-title-md text-primary mb-2">Post a Job</h2>
<p class="font-body-sm text-on-surface-variant">Create a new job listing for candidates.</p>
</a>
<a href="{{ route('hr.applicants') }}" class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant hover:border-secondary hover:shadow-lg transition-all group">
<span class="material-symbols-outlined text-secondary text-4xl mb-4 group-hover:scale-110 transition-transform">group</span>
<h2 class="font-title-md text-title-md text-primary mb-2">View Applicants</h2>
<p class="font-body-sm text-on-surface-variant">Review and manage applicant pipeline.</p>
</a>
</section>

<section class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-6 border-b border-outline-variant">
<div>
<h2 class="font-title-md text-title-md text-primary">Your Job Listings</h2>
<p class="font-body-sm text-on-surface-variant mt-1">{{ $jobs->count() }} {{ $jobs->count() === 1 ? 'position' : 'positions' }} total</p>
</div>
</div>

@if ($jobs->isEmpty())
<div class="p-12 text-center">
<span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4">work_off</span>
<p class="font-body-md text-on-surface-variant mb-4">No jobs posted yet.</p>
<a href="{{ route('hr.jobs.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-secondary text-white font-title-md text-[14px] hover:shadow-lg transition-all">
<span class="material-symbols-outlined text-[18px]">add</span>
Post your first job
</a>
</div>
@else
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-surface-container-low">
<tr>
<th class="px-6 py-4 font-label-caps text-on-surface-variant text-[12px]">Job</th>
<th class="px-6 py-4 font-label-caps text-on-surface-variant text-[12px]">Location</th>
<th class="px-6 py-4 font-label-caps text-on-surface-variant text-[12px]">Type</th>
<th class="px-6 py-4 font-label-caps text-on-surface-variant text-[12px]">Openings</th>
<th class="px-6 py-4 font-label-caps text-on-surface-variant text-[12px]">Status</th>
<th class="px-6 py-4 font-label-caps text-on-surface-variant text-[12px]">Posted</th>
<th class="px-6 py-4 font-label-caps text-on-surface-variant text-[12px] text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
@foreach ($jobs as $job)
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="px-6 py-4">
<p class="font-title-md text-[14px] text-on-surface">{{ $job->title }}</p>
<p class="font-body-sm text-on-surface-variant">{{ $job->company_name }}</p>
</td>
<td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ $job->location }}</td>
<td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ $job->job_type }}</td>
<td class="px-6 py-4 font-body-sm text-on-surface-variant">{{ $job->number_of_openings ?? 1 }}</td>
<td class="px-6 py-4">
@if ($job->status === \App\Models\Job::STATUS_ACTIVE)
<span class="inline-flex px-3 py-1 rounded-full text-[12px] font-label-caps bg-emerald-100 text-emerald-800 border border-emerald-200">Active</span>
@else
<span class="inline-flex px-3 py-1 rounded-full text-[12px] font-label-caps bg-surface-container-high text-on-surface-variant border border-outline-variant">Inactive</span>
@endif
</td>
<td class="px-6 py-4 font-body-sm text-on-surface-variant whitespace-nowrap">{{ $job->created_at->format('M j, Y') }}</td>
<td class="px-6 py-4">
<div class="flex items-center justify-end gap-2 flex-wrap">
<form method="POST" action="{{ route('hr.jobs.toggle-status', $job) }}">
@csrf
@method('PATCH')
<button type="submit" class="px-3 py-1.5 rounded-lg text-[12px] font-label-caps border border-outline-variant hover:bg-surface-container-high transition-colors">
{{ $job->status === \App\Models\Job::STATUS_ACTIVE ? 'Deactivate' : 'Activate' }}
</button>
</form>
<a href="{{ route('hr.jobs.edit', $job) }}" class="px-3 py-1.5 rounded-lg text-[12px] font-label-caps text-secondary border border-secondary/30 hover:bg-secondary/10 transition-colors">
Edit
</a>
<form method="POST" action="{{ route('hr.jobs.destroy', $job) }}" onsubmit="return confirm('Delete this job permanently?');">
@csrf
@method('DELETE')
<button type="submit" class="px-3 py-1.5 rounded-lg text-[12px] font-label-caps text-error border border-error/30 hover:bg-error/10 transition-colors">
Delete
</button>
</form>
</div>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endif
</section>
@endsection
