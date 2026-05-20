@extends('layouts.candidate', ['activeNav' => 'applied'])

@section('title', 'Applied Jobs')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'job_recommendations.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('page-main')
<div class="mb-8 animate-fade-in">
    <div class="flex items-center gap-3 mb-2">
        <span class="badge-success text-[11px]">Applications</span>
    </div>
    <h2 class="text-[28px] font-extrabold text-[#E2E8F0]">Applied Jobs</h2>
    <p class="text-[14px] text-[#64748B] mt-1">Track your applications and recruiter updates.</p>
</div>

<div class="space-y-4">
    @forelse ($applications as $application)
    @php $job = $application->job; @endphp
    @if ($job)
    @php
    $statusBadge = [
        'applied'              => 'badge-violet',
        'under_review'         => 'badge-warning',
        'shortlisted'          => 'badge-success',
        'interview_scheduled'  => 'badge-ai',
        'rejected'             => 'badge-error',
        'hired'                => 'badge-success',
    ][$application->status] ?? 'badge-violet';
    @endphp
    <div class="glass-card glass-card-lift p-6 group animate-fade-in">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="flex gap-4 flex-1 min-w-0">
                <div class="w-14 h-14 rounded-xl glass-card border border-[#334155] flex items-center justify-center flex-shrink-0">
                    <span class="font-bold text-[15px] gradient-text-violet">{{ $job->companyInitials() }}</span>
                </div>
                <div class="min-w-0">
                    <h3 class="text-[18px] font-bold text-[#E2E8F0] group-hover:text-[#C4B5FD] transition-colors mb-1 truncate">{{ $job->title }}</h3>
                    <p class="text-[13px] text-[#64748B] mb-2">{{ $job->company_name }}</p>
                    <div class="flex flex-wrap gap-3 text-[12px] text-[#64748B]">
                        <span>Recruiter: {{ $job->hr->name ?? 'HR Team' }}</span>
                        <span class="text-[#475569]">·</span>
                        <span>Applied {{ $application->applied_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-end gap-3 flex-shrink-0">
                <span class="{{ $statusBadge }}">{{ \App\Models\JobApplication::statusLabel($application->status) }}</span>
                @if ($application->match_score)
                <span class="text-[13px] font-bold text-[#C4B5FD]">{{ $application->match_score }}% Match</span>
                @endif
                <a href="{{ route('user.jobs.show', $job) }}" class="btn-secondary py-2 px-4 text-[13px]">View Job</a>
            </div>
        </div>
    </div>
    @endif
    @empty
    <div class="glass-card text-center py-16 animate-fade-in">
        <div class="empty-state-icon mx-auto mb-5">
            <span class="material-symbols-outlined text-[36px] text-[#34D399]">assignment</span>
        </div>
        <h3 class="text-[16px] font-semibold text-[#94A3B8] mb-2">No applications yet</h3>
        <p class="text-[13px] text-[#475569] mb-6">Start applying to jobs and track your progress here.</p>
        <a href="{{ route('user.jobs.recommendations') }}" class="btn-primary py-2.5 px-7 text-[14px]">Find Jobs</a>
    </div>
    @endforelse
</div>

@if ($applications->hasPages())
<div class="pt-4">{{ $applications->links() }}</div>
@endif

@include('partials.nav.dashboard-footer')
@endsection
