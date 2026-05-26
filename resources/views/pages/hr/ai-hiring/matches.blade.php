@extends('layouts.employer', ['activeNav' => 'ai-hiring'])

@section('title', $jobDescription->title . ' — Matches')

@section('page-css', 'ai-hiring.css')

@section('employer-main')
<div class="mb-6">
    <a href="{{ route('hr.ai-hiring.index') }}" class="inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-secondary">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back to AI Hiring
    </a>
</div>

<div class="mb-6" id="jd-status-panel"
     data-status-url="{{ route('hr.ai-hiring.status', $jobDescription) }}"
     data-initial-status="{{ $jobDescription->status }}">
    <span class="badge-violet text-[11px]">Match Results</span>
    <h2 class="text-[24px] font-extrabold text-on-surface mt-2">{{ $jobDescription->title }}</h2>
    <p class="text-sm text-on-surface-variant mt-1" id="jd-status-label">{{ $jobDescription->statusLabel() }}</p>
    @if ($jobDescription->analysis_error)
        <p class="text-xs text-[var(--badge-warning-text)] mt-2">{{ $jobDescription->analysis_error }}</p>
    @endif
</div>

<div id="jd-processing" class="glass-card p-12 rounded-2xl text-center mb-8 @if($jobDescription->status === 'completed') hidden @endif">
    <div class="inline-block w-10 h-10 border-2 border-secondary border-t-transparent rounded-full animate-spin"></div>
    <p class="text-on-surface-variant mt-4" id="jd-processing-text">AI is analyzing your job description and matching candidates…</p>
</div>

<div id="jd-matches-content" class="@if($jobDescription->status !== 'completed') hidden @endif">
    <form method="GET" class="glass-card p-4 rounded-2xl mb-6 flex flex-col lg:flex-row gap-3 lg:items-end flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <label class="text-xs text-on-surface-variant">Search</label>
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, title, email…"
                class="w-full mt-1 px-3 py-2 rounded-xl border border-outline-variant bg-background text-sm text-on-surface">
        </div>
        <div class="w-28">
            <label class="text-xs text-on-surface-variant">Min match %</label>
            <input type="number" name="min_score" min="0" max="100" value="{{ $filters['min_score'] ?? '' }}"
                class="w-full mt-1 px-3 py-2 rounded-xl border border-outline-variant bg-background text-sm text-on-surface">
        </div>
        <div class="w-36">
            <label class="text-xs text-on-surface-variant">Skill</label>
            <input type="text" name="skill" value="{{ $filters['skill'] ?? '' }}"
                class="w-full mt-1 px-3 py-2 rounded-xl border border-outline-variant bg-background text-sm text-on-surface">
        </div>
        <div class="w-28">
            <label class="text-xs text-on-surface-variant">Min years exp</label>
            <input type="number" name="min_experience" min="0" value="{{ $filters['min_experience'] ?? '' }}"
                class="w-full mt-1 px-3 py-2 rounded-xl border border-outline-variant bg-background text-sm text-on-surface">
        </div>
        <div class="w-36">
            <label class="text-xs text-on-surface-variant">Location</label>
            <input type="text" name="location" value="{{ $filters['location'] ?? '' }}"
                class="w-full mt-1 px-3 py-2 rounded-xl border border-outline-variant bg-background text-sm text-on-surface">
        </div>
        <button type="submit" class="btn-primary py-2 px-4 text-sm">Filter</button>
    </form>

    @if ($matches->isEmpty())
        <div class="glass-card p-12 rounded-2xl text-center">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant">person_off</span>
            <p class="text-on-surface-variant mt-4">No candidates match your filters yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($matches as $match)
                @php
                    $user = $match->user;
                    $c = $match->candidate ?? $user?->candidate;
                    $name = $c?->full_name ?: $user?->name;
                    $photo = $user?->profilePhotoUrl();
                    $skills = is_array($c?->skills) ? $c->skills : [];
                @endphp
                <a href="{{ route('hr.ai-hiring.candidate', [$jobDescription, $user]) }}"
                   class="ai-match-card glass-card p-5 rounded-2xl block hover:border-secondary/40 border border-transparent transition-all">
                    <div class="flex items-start gap-4">
                        <div class="ai-match-score-ring" data-score="{{ $match->match_score }}">
                            <span class="ai-match-score-value">{{ $match->match_score }}%</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-on-surface truncate">{{ $name }}</p>
                            <p class="text-xs text-on-surface-variant truncate">{{ $c?->current_title ?? 'Candidate' }}</p>
                            @if ($c?->experience_years)
                                <p class="text-[10px] text-on-surface-variant mt-1">{{ $c->experience_years }}+ years experience</p>
                            @endif
                        </div>
                    </div>
                    @if ($match->ai_reason)
                        <p class="text-xs text-on-surface-variant mt-4 leading-relaxed border-t border-outline-variant/50 pt-3">{{ $match->ai_reason }}</p>
                    @endif
                    @if (!empty($skills))
                        <div class="flex flex-wrap gap-1.5 mt-3">
                            @foreach (array_slice($skills, 0, 4) as $skill)
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-surface-container-high text-on-surface-variant">{{ is_string($skill) ? $skill : ($skill['name'] ?? '') }}</span>
                            @endforeach
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $matches->links() }}</div>
    @endif
</div>

<div id="jd-failed" class="glass-card p-8 rounded-2xl text-center hidden">
    <p class="text-[#F87171]">Analysis failed. Please try uploading again.</p>
    <a href="{{ route('hr.ai-hiring.create') }}" class="btn-primary inline-flex mt-4 py-2 px-4 text-sm">New upload</a>
</div>
@endsection

@push('employer-scripts')
<script src="{{ asset('js/hr-ai-hiring.js') }}"></script>
@endpush
