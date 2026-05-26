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
<main class="lg:ml-[280px] min-h-screen transition-all duration-300">
<header class="fixed top-0 right-0 w-full lg:w-[calc(100%-280px)] h-16 z-40 glass-panel border-b border-outline-variant flex justify-between items-center px-6 lg:px-8">
    <div class="flex items-center gap-3">
        <span class="badge-violet text-[10px]">Admin</span>
        <h1 class="text-[16px] font-bold text-on-surface">Job Applications</h1>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn-ghost py-1.5 px-4 text-[12px]">Dashboard</a>
</header>

<section class="pt-24 pb-12 px-6 lg:px-8 max-w-[1440px] mx-auto animate-fade-in">

    {{-- Filter form --}}
    <form method="GET" class="glass-card p-5 mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, email, job..." class="input-dark sm:col-span-2"/>
        <select name="status" class="input-dark">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="job_id" class="input-dark">
            <option value="">All jobs</option>
            @foreach ($jobs as $jobOption)
                <option value="{{ $jobOption->id }}" @selected(($filters['job_id'] ?? '') == $jobOption->id)>{{ $jobOption->title }}</option>
            @endforeach
        </select>
        <input type="text"   name="company"   value="{{ $filters['company']   ?? '' }}" placeholder="Company"    class="input-dark"/>
        <input type="number" name="min_score" value="{{ $filters['min_score'] ?? '' }}" placeholder="Min match %" min="0" max="100" class="input-dark"/>
        <input type="date"   name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="input-dark"/>
        <input type="date"   name="to_date"   value="{{ $filters['to_date']   ?? '' }}" class="input-dark"/>
        <button type="submit" class="btn-primary py-2 px-4 text-[13px]">Filter</button>
    </form>

    {{-- Table --}}
    <div class="glass-card overflow-x-auto">
        <table class="w-full text-left text-[13px]">
            <thead>
                <tr class="border-b border-outline-variant">
                    @foreach (['Candidate','Email','Phone','Job','Company','Match','Resume','Status','Applied','Actions'] as $col)
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant whitespace-nowrap">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse ($applications as $application)
                    @php $candidate = $application->user->candidate; @endphp
                    <tr class="hover:bg-surface-container/60 transition-colors">
                        <td class="px-4 py-3 text-on-surface font-medium">{{ $candidate?->full_name ?? $application->user->name }}</td>
                        <td class="px-4 py-3 text-on-surface-variant">{{ $application->user->email }}</td>
                        <td class="px-4 py-3 text-on-surface-variant">{{ $candidate?->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-on-surface">{{ $application->job->title }}</td>
                        <td class="px-4 py-3 text-on-surface-variant">{{ $application->job->company_name }}</td>
                        <td class="px-4 py-3 font-bold text-secondary">{{ $application->match_score ?? '—' }}@if($application->match_score)%@endif</td>
                        <td class="px-4 py-3">
                            @if ($candidate?->resume_path)
                                <a href="{{ route('admin.job-applications.resume', $application) }}" class="text-secondary hover:underline text-[12px]">Download</a>
                            @else
                                <span class="text-on-surface-variant">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <select data-application-id="{{ $application->id }}" class="application-status-select input-dark py-1 px-2 text-[12px]">
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3 text-on-surface-variant whitespace-nowrap">{{ $application->applied_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.job-applications.show', $application) }}" class="btn-ghost py-1 px-3 text-[12px]">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="material-symbols-outlined text-[48px] text-on-surface-variant">assignment</span>
                                <p class="text-on-surface-variant">No applications found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-on-surface-variant text-[13px]">{{ $applications->links() }}</div>
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
