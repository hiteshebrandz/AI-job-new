@extends('layouts.app')

@section('title', 'Job Applications')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'admin_analytics_dashboard.css')

@section('tailwind-config', 'tailwind-config-admin.js')

@push('head-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('partials.nav.admin-sidebar')
<main class="ml-[280px] min-h-screen">
<header class="fixed top-0 right-0 w-[calc(100%-280px)] h-16 z-40 bg-surface/80 backdrop-blur-lg border-b border-outline-variant flex justify-between items-center px-8">
<h1 class="font-title-md text-title-md text-primary">Job Applications</h1>
<a href="{{ route('admin.dashboard') }}" class="text-secondary font-label-caps hover:underline">Dashboard</a>
</header>
<section class="pt-24 pb-12 px-8 max-w-[1440px] mx-auto">
<form method="GET" class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm mb-6 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
<input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, email, job..." class="md:col-span-2 w-full bg-surface-container-low border-none rounded-lg px-4 py-2 text-body-sm focus:ring-2 focus:ring-secondary/50"/>
<select name="status" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-2 text-body-sm">
<option value="">All statuses</option>
@foreach ($statuses as $value => $label)
<option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
@endforeach
</select>
<select name="job_id" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-2 text-body-sm">
<option value="">All jobs</option>
@foreach ($jobs as $jobOption)
<option value="{{ $jobOption->id }}" @selected(($filters['job_id'] ?? '') == $jobOption->id)>{{ $jobOption->title }}</option>
@endforeach
</select>
<input type="text" name="company" value="{{ $filters['company'] ?? '' }}" placeholder="Company" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-2 text-body-sm"/>
<input type="number" name="min_score" value="{{ $filters['min_score'] ?? '' }}" placeholder="Min match %" min="0" max="100" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-2 text-body-sm"/>
<input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-2 text-body-sm"/>
<input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-2 text-body-sm"/>
<button type="submit" class="px-4 py-2 bg-secondary text-white rounded-lg font-label-caps">Filter</button>
</form>
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-surface-container-low">
<tr>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Candidate</th>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Email</th>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Phone</th>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Job</th>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Company</th>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Match</th>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Resume</th>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Status</th>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Applied</th>
<th class="px-4 py-3 font-label-caps text-on-surface-variant text-[12px]">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
@forelse ($applications as $application)
@php $candidate = $application->user->candidate; @endphp
<tr class="hover:bg-surface-container-low/50">
<td class="px-4 py-3 font-body-sm">{{ $candidate?->full_name ?? $application->user->name }}</td>
<td class="px-4 py-3 font-body-sm text-on-surface-variant">{{ $application->user->email }}</td>
<td class="px-4 py-3 font-body-sm text-on-surface-variant">{{ $candidate?->phone ?? '—' }}</td>
<td class="px-4 py-3 font-body-sm">{{ $application->job->title }}</td>
<td class="px-4 py-3 font-body-sm text-on-surface-variant">{{ $application->job->company_name }}</td>
<td class="px-4 py-3 font-body-sm text-secondary font-bold">{{ $application->match_score ?? '—' }}@if($application->match_score)%@endif</td>
<td class="px-4 py-3 font-body-sm">
@if ($candidate?->resume_path)
<a href="{{ route('admin.job-applications.resume', $application) }}" class="text-secondary hover:underline">Download</a>
@else
—
@endif
</td>
<td class="px-4 py-3">
<select data-application-id="{{ $application->id }}" class="application-status-select text-body-sm bg-surface-container-low border-none rounded-lg px-2 py-1">
@foreach ($statuses as $value => $label)
<option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>
@endforeach
</select>
</td>
<td class="px-4 py-3 font-body-sm whitespace-nowrap">{{ $application->applied_at->format('M j, Y') }}</td>
<td class="px-4 py-3">
<a href="{{ route('admin.job-applications.show', $application) }}" class="text-secondary font-label-caps hover:underline">View Details</a>
</td>
</tr>
@empty
<tr><td colspan="10" class="px-6 py-12 text-center text-on-surface-variant">No applications found.</td></tr>
@endforelse
</tbody>
</table>
</div>
<div class="mt-6">{{ $applications->links() }}</div>
</section>
</main>
<div id="toast-root" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
window.adminApplicationsConfig = {
    statusUrlTemplate: @json(route('admin.job-applications.status', ['application' => '__ID__'])),
    csrf: document.querySelector('meta[name="csrf-token"]')?.content,
};
</script>
<script src="{{ asset('js/admin-applications.js') }}"></script>
@endpush
