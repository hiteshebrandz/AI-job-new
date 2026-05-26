@extends('layouts.employer', ['activeNav' => 'dashboard'])

@section('title', 'HR Dashboard')

@section('body-class', 'bg-background text-on-surface min-h-screen')

@section('page-css', 'employer_dashboard.css')

@section('tailwind-config', 'tailwind-config-employer.js')

@push('candidate-header-actions')
<a href="{{ route('hr.jobs.create') }}" class="btn-primary py-2 px-4 text-[13px]">
    <span class="material-symbols-outlined text-[16px]">post_add</span>
    Post a Job
</a>
@endpush

@section('employer-main')
@if (session('success'))
<div class="mb-6 p-4 rounded-2xl bg-[var(--badge-success-bg)]/40 border border-[var(--badge-success-text)]/30 text-[var(--badge-success-text)] text-[14px] flex items-center gap-3 animate-fade-in">
    <span class="material-symbols-outlined">check_circle</span>
    {{ session('success') }}
</div>
@endif

<!-- Welcome -->
<div class="mb-8 animate-fade-in">
    <div class="flex items-center gap-3 mb-2">
        <span class="badge-violet">Employer Portal</span>
    </div>
    <h1 class="text-[32px] font-extrabold text-on-surface tracking-tight">Welcome, <span class="gradient-text-violet">{{ $user->name }}</span></h1>
    <p class="text-[15px] text-on-surface-variant mt-1">Manage your talent pipeline and job listings.</p>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-fade-in-delay-1">
    <div class="glass-card kpi-card p-5">
        <div class="w-10 h-10 rounded-xl bg-secondary-fixed flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-secondary text-[20px]">work</span>
        </div>
        <p class="text-[28px] font-extrabold text-on-surface">{{ $activeJobs }}</p>
        <p class="text-[12px] text-on-surface-variant mt-1">Active Listings</p>
    </div>
    <div class="glass-card kpi-card-cyan p-5">
        <div class="w-10 h-10 rounded-xl bg-secondary-fixed flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-secondary text-[20px]">group</span>
        </div>
        <p class="text-[28px] font-extrabold text-on-surface">{{ $totalApplicants }}</p>
        <p class="text-[12px] text-on-surface-variant mt-1">Total Applicants</p>
    </div>
    <div class="glass-card kpi-card-green p-5">
        <div class="w-10 h-10 rounded-xl bg-[var(--badge-success-bg)] flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[var(--badge-success-text)] text-[20px]">work_history</span>
        </div>
        <p class="text-[28px] font-extrabold text-on-surface">{{ $draftJobs }}</p>
        <p class="text-[12px] text-on-surface-variant mt-1">Draft Jobs</p>
    </div>
    <div class="glass-card kpi-card-amber p-5">
        <div class="w-10 h-10 rounded-xl bg-[var(--badge-warning-bg)] flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[var(--badge-warning-text)] text-[20px]">trending_up</span>
        </div>
        <p class="text-[28px] font-extrabold text-on-surface">{{ $jobs->count() }}</p>
        <p class="text-[12px] text-on-surface-variant mt-1">Total Jobs Posted</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid md:grid-cols-2 gap-5 mb-8 animate-fade-in-delay-2">
    <a href="{{ route('hr.jobs.create') }}" class="glass-card glass-card-lift p-7 group relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-secondary to-[#6063ee] opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-12 h-12 rounded-2xl gradient-violet flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-white text-[24px]">post_add</span>
        </div>
        <h2 class="text-[18px] font-bold text-on-surface mb-2">Post a New Job</h2>
        <p class="text-[14px] text-on-surface-variant">Create a new job listing and let AI match the best candidates automatically.</p>
        <div class="flex items-center gap-2 mt-5 text-secondary text-[13px] font-semibold">
            <span>Create listing</span>
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </div>
    </a>
    <a href="{{ route('hr.applicants') }}" class="glass-card glass-card-lift p-7 group relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-[#6063ee] to-secondary opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="w-12 h-12 rounded-2xl gradient-cyan-violet flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-white text-[24px]">group</span>
        </div>
        <h2 class="text-[18px] font-bold text-on-surface mb-2">View Applicants</h2>
        <p class="text-[14px] text-on-surface-variant">Review your candidate pipeline and manage applications with AI insights.</p>
        <div class="flex items-center gap-2 mt-5 text-secondary text-[13px] font-semibold">
            <span>View pipeline</span>
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </div>
    </a>
</div>

<!-- Job Listings Table -->
<section class="glass-card overflow-hidden animate-fade-in-delay-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 border-b border-outline-variant">
        <div>
            <h2 class="text-[18px] font-bold text-on-surface">Your Job Listings</h2>
            <p class="text-[13px] text-on-surface-variant mt-0.5">{{ $jobs->count() }} {{ $jobs->count() === 1 ? 'position' : 'positions' }} total</p>
        </div>
        <a href="{{ route('hr.jobs.create') }}" class="btn-primary py-2 px-4 text-[13px]">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Add Job
        </a>
    </div>

    @if ($jobs->isEmpty())
    <div class="empty-state py-16">
        <div class="empty-state-icon">
            <span class="material-symbols-outlined">work_off</span>
        </div>
        <h3 class="text-[16px] font-semibold text-on-surface-variant mb-2">No jobs posted yet</h3>
        <p class="text-[14px] text-on-surface-variant mb-6">Create your first job listing to start receiving applications.</p>
        <a href="{{ route('hr.jobs.create') }}" class="btn-primary py-2.5 px-6 text-[14px]">
            <span class="material-symbols-outlined text-[16px]">add</span>
            Post your first job
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Openings</th>
                    <th>Status</th>
                    <th>Posted</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jobs as $job)
                <tr>
                    <td>
                        <p class="text-[14px] font-semibold text-on-surface">{{ $job->title }}</p>
                        <p class="text-[12px] text-on-surface-variant">{{ $job->company_name }}</p>
                    </td>
                    <td class="text-[13px]">{{ $job->location }}</td>
                    <td class="text-[13px]">{{ $job->job_type }}</td>
                    <td class="text-[13px]">{{ $job->number_of_openings ?? 1 }}</td>
                    <td>
                        @if ($job->status === \App\Models\Job::STATUS_ACTIVE)
                        <span class="badge-success">Active</span>
                        @else
                        <span class="badge-warning">Inactive</span>
                        @endif
                    </td>
                    <td class="text-[13px] text-on-surface-variant">{{ $job->created_at->format('M j, Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-2 flex-wrap">
                            <form method="POST" action="{{ route('hr.jobs.toggle-status', $job) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-ghost py-1.5 px-3 text-[12px]">
                                    {{ $job->status === \App\Models\Job::STATUS_ACTIVE ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <a href="{{ route('hr.jobs.edit', $job) }}" class="btn-secondary py-1.5 px-3 text-[12px]">Edit</a>
                            <form method="POST" action="{{ route('hr.jobs.destroy', $job) }}" onsubmit="return confirm('Delete this job permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger py-1.5 px-3 text-[12px]">Delete</button>
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

@include('partials.nav.dashboard-footer')
@endsection
