@extends('layouts.employer', ['activeNav' => 'candidates'])

@section('title', 'Job Seekers')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'applicant_management.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('employer-main')
<div class="mb-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <span class="badge-violet text-[11px]">Talent Pool</span>
            <h2 class="text-[28px] font-extrabold text-on-surface mt-2">Job Seekers</h2>
            <p class="text-sm text-on-surface-variant mt-1">All registered candidates on the platform ({{ $totalCount }} total)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('hr.applicants') }}" class="glass-card p-4 rounded-2xl mb-6 flex flex-col md:flex-row gap-3 md:items-center">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, email, title, location…"
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-outline-variant bg-background text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary">
        </div>
        <label class="flex items-center gap-2 text-sm text-on-surface-variant whitespace-nowrap cursor-pointer">
            <input type="checkbox" name="applied_only" value="1" class="rounded border-outline-variant"
                @checked(!empty($filters['applied_only'])) onchange="this.form.submit()">
            Applied to my jobs only
        </label>
        <button type="submit" class="btn-primary py-2.5 px-5 text-sm">Search</button>
        @if (!empty($filters['search']) || !empty($filters['applied_only']))
            <a href="{{ route('hr.applicants') }}" class="text-sm text-on-surface-variant hover:text-secondary py-2.5">Clear</a>
        @endif
    </form>

    @if ($jobSeekers->isEmpty())
        <div class="glass-card p-12 rounded-2xl text-center">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant">person_off</span>
            <p class="text-on-surface-variant mt-4">No job seekers match your search.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($jobSeekers as $seeker)
                @php
                    $c = $seeker->candidate;
                    $name = $c?->full_name ?: $seeker->name;
                    $title = $c?->current_title ?? 'Job seeker';
                    $photo = $seeker->profilePhotoUrl();
                @endphp
                <a href="{{ route('hr.applicants.show', $seeker) }}"
                   class="glass-card p-5 rounded-2xl block hover:border-secondary/40 border border-transparent transition-all group">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl flex-shrink-0 overflow-hidden flex items-center justify-center {{ $photo ? '' : 'gradient-violet' }}">
                            @if ($photo)
                                <img src="{{ $photo }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-white font-bold text-lg">{{ $seeker->initials() }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-on-surface truncate group-hover:text-secondary transition-colors">{{ $name }}</p>
                            <p class="text-xs text-on-surface-variant truncate mt-0.5">{{ $title }}</p>
                            <p class="text-xs text-on-surface-variant truncate mt-1">{{ $seeker->email }}</p>
                        </div>
                        <span class="material-symbols-outlined text-on-surface-variant group-hover:text-secondary">chevron_right</span>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-outline-variant/50">
                        @if ($c?->experience_years)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-surface-container text-on-surface-variant">{{ $c->experience_years }}+ yrs exp</span>
                        @endif
                        @if ($c?->location)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-surface-container text-on-surface-variant">{{ $c->location }}</span>
                        @endif
                        @if ($seeker->hr_applications_count > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-secondary-fixed text-secondary">{{ $seeker->hr_applications_count }} application(s)</span>
                        @endif
                        @if ($c?->ai_score)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-secondary-fixed text-secondary">AI {{ $c->ai_score }}</span>
                        @endif
                    </div>
                    @if (!empty($c?->skills) && is_array($c->skills))
                        <div class="flex flex-wrap gap-1.5 mt-3">
                            @foreach (array_slice($c->skills, 0, 4) as $skill)
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-surface-container-high text-on-surface-variant">{{ is_string($skill) ? $skill : ($skill['name'] ?? '') }}</span>
                            @endforeach
                            @if (count($c->skills) > 4)
                                <span class="text-[10px] text-on-surface-variant">+{{ count($c->skills) - 4 }}</span>
                            @endif
                        </div>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $jobSeekers->links() }}
        </div>
    @endif
</div>
@include('partials.nav.dashboard-footer')
@endsection
