@extends('layouts.employer', ['activeNav' => 'ai-hiring'])

@section('title', ($candidate?->full_name ?? $jobSeeker->name) . ' — Match Profile')

@section('page-css', 'ai-hiring.css')

@section('employer-main')
@php
    $name = $candidate?->full_name ?? $jobSeeker->name;
    $photo = $jobSeeker->profilePhotoUrl();
    $skills = $candidate?->skills ?? [];
    $projects = $candidate?->projects ?? [];
@endphp

<div class="mb-6 flex flex-wrap items-center gap-3">
    <a href="{{ route('hr.ai-hiring.matches', $jobDescription) }}" class="inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-secondary">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back to matches
    </a>
</div>

@if ($match)
    <div class="glass-card p-4 rounded-2xl mb-6 flex flex-wrap items-center gap-4 border border-secondary/30">
        <div class="ai-match-score-ring ai-match-score-ring-lg" data-score="{{ $match->match_score }}">
            <span class="ai-match-score-value">{{ $match->match_score }}%</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">AI match for {{ $jobDescription->title }}</p>
            @if ($match->ai_reason)
                <p class="text-sm text-secondary mt-1 leading-relaxed">{{ $match->ai_reason }}</p>
            @endif
        </div>
        <form method="POST" action="{{ route('hr.ai-hiring.connect', [$jobDescription, $jobSeeker]) }}">
            @csrf
            <button type="submit" class="btn-primary py-2.5 px-5 text-sm inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">connect_without_contact</span>
                Connect for Job
            </button>
        </form>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-6">
        <div class="glass-card p-6 rounded-2xl text-center">
            <div class="w-24 h-24 rounded-2xl mx-auto overflow-hidden flex items-center justify-center mb-4 {{ $photo ? '' : 'gradient-violet' }}">
                @if ($photo)
                    <img src="{{ $photo }}" alt="" class="w-full h-full object-cover">
                @else
                    <span class="text-white font-bold text-3xl">{{ $jobSeeker->initials() }}</span>
                @endif
            </div>
            <h1 class="text-xl font-bold text-on-surface">{{ $name }}</h1>
            <p class="text-sm text-on-surface-variant mt-1">{{ $candidate?->current_title ?? 'Candidate' }}</p>
        </div>
        <div class="glass-card p-6 rounded-2xl space-y-3 text-sm">
            <h3 class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Contact</h3>
            <div class="flex items-center gap-2 text-on-surface-variant">
                <span class="material-symbols-outlined text-[18px]">mail</span>
                <a href="mailto:{{ $jobSeeker->email }}" class="hover:text-secondary break-all">{{ $jobSeeker->email }}</a>
            </div>
            @if ($jobSeeker->phone || $candidate?->phone)
                <div class="flex items-center gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[18px]">phone</span>
                    <span>{{ $jobSeeker->phone ?? $candidate->phone }}</span>
                </div>
            @endif
            @if ($candidate?->location)
                <div class="flex items-center gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[18px]">location_on</span>
                    <span>{{ $candidate->location }}</span>
                </div>
            @endif
            @if ($candidate?->resume_path)
                <a href="{{ route('hr.applicants.resume', $jobSeeker) }}"
                   class="mt-2 w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-secondary/40 text-secondary text-sm font-medium hover:bg-secondary-fixed">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Download Resume
                </a>
            @endif
        </div>
    </div>
    <div class="lg:col-span-2 space-y-6">
        @if ($candidate?->summary || $candidate?->ai_recommendation)
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-3">Summary</h3>
                <p class="text-sm text-on-surface-variant leading-relaxed whitespace-pre-line">{{ $candidate->summary ?: $candidate->ai_recommendation }}</p>
            </div>
        @endif
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4">Skills</h3>
            @if (!empty($skills))
                <div class="flex flex-wrap gap-2">
                    @foreach ($skills as $skill)
                        <span class="text-xs px-3 py-1 rounded-lg bg-surface-container-high text-on-surface-variant">{{ is_string($skill) ? $skill : ($skill['name'] ?? '') }}</span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-on-surface-variant">—</p>
            @endif
        </div>
        @if (!empty($projects))
            <div class="glass-card p-6 rounded-2xl">
                <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4">Projects</h3>
                <div class="space-y-4">
                    @foreach ($projects as $project)
                        <div class="border-b border-outline-variant/50 pb-4 last:border-0 last:pb-0">
                            <p class="font-medium text-on-surface">{{ $project['title'] ?? 'Project' }}</p>
                            @if (!empty($project['description']))
                                <p class="text-sm text-on-surface-variant mt-1">{{ $project['description'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant mb-4">Education</h3>
            <p class="text-sm text-on-surface">{{ $candidate?->education ?: '—' }}</p>
            @if ($candidate?->university)
                <p class="text-xs text-on-surface-variant mt-1">{{ $candidate->university }}</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('employer-scripts')
<script src="{{ asset('js/hr-ai-hiring.js') }}"></script>
@endpush
