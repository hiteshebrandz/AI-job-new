@extends('layouts.employer', ['activeNav' => 'candidates'])

@section('title', ($candidate?->full_name ?? $jobSeeker->name) . ' — Profile')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'applicant_management.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('employer-main')
@php
    $name = $candidate?->full_name ?? $jobSeeker->name;
    $photo = $jobSeeker->profilePhotoUrl();
    $skills = $candidate?->skills ?? [];
    $projects = $candidate?->projects ?? [];
@endphp

<div class="mb-6">
    <a href="{{ route('hr.applicants') }}" class="inline-flex items-center gap-2 text-sm text-[#94A3B8] hover:text-[#C4B5FD] transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back to Job Seekers
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Sidebar summary --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="glass-card p-6 rounded-2xl text-center">
            <div class="w-24 h-24 rounded-2xl mx-auto overflow-hidden flex items-center justify-center mb-4 {{ $photo ? '' : 'gradient-violet' }}">
                @if ($photo)
                    <img src="{{ $photo }}" alt="" class="w-full h-full object-cover">
                @else
                    <span class="text-white font-bold text-3xl">{{ $jobSeeker->initials() }}</span>
                @endif
            </div>
            <h1 class="text-xl font-bold text-[#E2E8F0]">{{ $name }}</h1>
            <p class="text-sm text-[#64748B] mt-1">{{ $candidate?->current_title ?? 'Candidate' }}</p>
            @if ($candidate?->candidate_code)
                <p class="text-xs text-[#475569] mt-2">ID: {{ $candidate->candidate_code }}</p>
            @endif
            @if ($candidate?->ai_score)
                <div class="mt-4 inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#1E1B4B] text-[#C4B5FD] text-sm font-bold">
                    <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
                    AI Score {{ $candidate->ai_score }}
                </div>
            @endif
        </div>

        <div class="glass-card p-6 rounded-2xl space-y-3 text-sm">
            <h3 class="text-xs font-bold uppercase tracking-widest text-[#64748B] mb-2">Contact</h3>
            <div class="flex items-center gap-2 text-[#94A3B8]">
                <span class="material-symbols-outlined text-[18px]">mail</span>
                <a href="mailto:{{ $jobSeeker->email }}" class="hover:text-[#C4B5FD] break-all">{{ $jobSeeker->email }}</a>
            </div>
            @if ($jobSeeker->phone || $candidate?->phone)
                <div class="flex items-center gap-2 text-[#94A3B8]">
                    <span class="material-symbols-outlined text-[18px]">phone</span>
                    <span>{{ $jobSeeker->phone ?? $candidate->phone }}</span>
                </div>
            @endif
            @if ($candidate?->location)
                <div class="flex items-center gap-2 text-[#94A3B8]">
                    <span class="material-symbols-outlined text-[18px]">location_on</span>
                    <span>{{ $candidate->location }}</span>
                </div>
            @endif
            <div class="flex items-center gap-2 text-[#94A3B8]">
                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                <span>Joined {{ $jobSeeker->created_at?->format('M j, Y') }}</span>
            </div>
            @if ($candidate?->resume_path)
                <a href="{{ route('hr.applicants.resume', $jobSeeker) }}"
                   class="mt-2 w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-[#8B5CF6]/40 text-[#C4B5FD] text-sm font-medium hover:bg-[#1E1B4B] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Download Resume
                </a>
            @endif
        </div>
    </div>

    {{-- Main details --}}
    <div class="lg:col-span-2 space-y-6">
        @if ($candidate?->summary || $candidate?->ai_recommendation)
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-sm font-bold uppercase tracking-widest text-[#64748B] mb-3">Summary</h3>
                <p class="text-sm text-[#94A3B8] leading-relaxed whitespace-pre-line">{{ $candidate->summary ?: $candidate->ai_recommendation }}</p>
            </div>
        @endif

        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-sm font-bold uppercase tracking-widest text-[#64748B] mb-4">Professional Details</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-[#64748B] text-xs">Experience</dt>
                    <dd class="text-[#E2E8F0] font-medium mt-0.5">{{ $candidate?->experience_years ? $candidate->experience_years . ' years' : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-[#64748B] text-xs">Seniority</dt>
                    <dd class="text-[#E2E8F0] font-medium mt-0.5">{{ $candidate?->seniority_level ? ucfirst($candidate->seniority_level) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-[#64748B] text-xs">Education</dt>
                    <dd class="text-[#E2E8F0] font-medium mt-0.5">{{ $candidate?->education ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-[#64748B] text-xs">University</dt>
                    <dd class="text-[#E2E8F0] font-medium mt-0.5">{{ $candidate?->university ?: '—' }}</dd>
                </div>
                @if ($candidate?->graduation_year)
                    <div>
                        <dt class="text-[#64748B] text-xs">Graduation year</dt>
                        <dd class="text-[#E2E8F0] font-medium mt-0.5">{{ $candidate->graduation_year }}</dd>
                    </div>
                @endif
                @if ($candidate?->previous_companies)
                    <div class="sm:col-span-2">
                        <dt class="text-[#64748B] text-xs">Previous companies</dt>
                        <dd class="text-[#E2E8F0] font-medium mt-0.5">{{ $candidate->previous_companies }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        @if (!empty($skills))
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-sm font-bold uppercase tracking-widest text-[#64748B] mb-3">Skills</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($skills as $skill)
                        <span class="px-3 py-1 rounded-lg text-xs font-medium bg-[#1E1B4B] text-[#C4B5FD] border border-[#8B5CF6]/20">
                            {{ is_string($skill) ? $skill : ($skill['name'] ?? json_encode($skill)) }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @if (!empty($projects))
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-sm font-bold uppercase tracking-widest text-[#64748B] mb-4">Projects / Experience</h3>
                <div class="space-y-4">
                    @foreach ($projects as $project)
                        @if (is_array($project))
                            <div class="p-4 rounded-xl bg-[#0F172A] border border-[#334155]/50">
                                <p class="font-semibold text-[#E2E8F0]">{{ $project['title'] ?? $project['name'] ?? 'Project' }}</p>
                                @if (!empty($project['company']))
                                    <p class="text-xs text-[#64748B] mt-0.5">{{ $project['company'] }}</p>
                                @endif
                                @if (!empty($project['description']))
                                    <p class="text-sm text-[#94A3B8] mt-2">{{ $project['description'] }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-sm font-bold uppercase tracking-widest text-[#64748B] mb-4">
                Applications to your jobs ({{ $applications->count() }})
            </h3>
            @if ($applications->isEmpty())
                <p class="text-sm text-[#64748B]">This candidate has not applied to any of your posted jobs yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-[#64748B] text-xs uppercase border-b border-[#334155]">
                                <th class="pb-3 pr-4">Job</th>
                                <th class="pb-3 pr-4">Status</th>
                                <th class="pb-3 pr-4">Match</th>
                                <th class="pb-3">Applied</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $app)
                                <tr class="border-b border-[#334155]/30">
                                    <td class="py-3 pr-4 text-[#E2E8F0]">{{ $app->job->title }}</td>
                                    <td class="py-3 pr-4">
                                        <span class="px-2 py-0.5 rounded-md text-xs bg-[#263248] text-[#94A3B8]">
                                            {{ $statuses[$app->status] ?? $app->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4 text-[#C4B5FD] font-semibold">{{ $app->match_score ?? '—' }}%</td>
                                    <td class="py-3 text-[#64748B]">{{ $app->applied_at?->format('M j, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@include('partials.nav.dashboard-footer')
@endsection
