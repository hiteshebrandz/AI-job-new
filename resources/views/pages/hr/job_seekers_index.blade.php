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
            <h2 class="text-[28px] font-extrabold text-[#E2E8F0] mt-2">Job Seekers</h2>
            <p class="text-sm text-[#64748B] mt-1">All registered candidates on the platform ({{ $totalCount }} total)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('hr.applicants') }}" class="glass-card p-4 rounded-2xl mb-6 flex flex-col md:flex-row gap-3 md:items-center">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#64748B] text-[20px]">search</span>
            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, email, title, location…"
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#334155] bg-[#0F172A] text-sm text-[#E2E8F0] focus:border-[#8B5CF6] focus:ring-1 focus:ring-[#8B5CF6]">
        </div>
        <label class="flex items-center gap-2 text-sm text-[#94A3B8] whitespace-nowrap cursor-pointer">
            <input type="checkbox" name="applied_only" value="1" class="rounded border-[#334155]"
                @checked(!empty($filters['applied_only'])) onchange="this.form.submit()">
            Applied to my jobs only
        </label>
        <button type="submit" class="btn-primary py-2.5 px-5 text-sm">Search</button>
        @if (!empty($filters['search']) || !empty($filters['applied_only']))
            <a href="{{ route('hr.applicants') }}" class="text-sm text-[#94A3B8] hover:text-[#C4B5FD] py-2.5">Clear</a>
        @endif
    </form>

    @if ($jobSeekers->isEmpty())
        <div class="glass-card p-12 rounded-2xl text-center">
            <span class="material-symbols-outlined text-[48px] text-[#64748B]">person_off</span>
            <p class="text-[#94A3B8] mt-4">No job seekers match your search.</p>
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
                   class="glass-card p-5 rounded-2xl block hover:border-[#8B5CF6]/40 border border-transparent transition-all group">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl flex-shrink-0 overflow-hidden flex items-center justify-center {{ $photo ? '' : 'gradient-violet' }}">
                            @if ($photo)
                                <img src="{{ $photo }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-white font-bold text-lg">{{ $seeker->initials() }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-[#E2E8F0] truncate group-hover:text-[#C4B5FD] transition-colors">{{ $name }}</p>
                            <p class="text-xs text-[#64748B] truncate mt-0.5">{{ $title }}</p>
                            <p class="text-xs text-[#475569] truncate mt-1">{{ $seeker->email }}</p>
                        </div>
                        <span class="material-symbols-outlined text-[#64748B] group-hover:text-[#8B5CF6]">chevron_right</span>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-[#334155]/50">
                        @if ($c?->experience_years)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#1E293B] text-[#94A3B8]">{{ $c->experience_years }}+ yrs exp</span>
                        @endif
                        @if ($c?->location)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#1E293B] text-[#94A3B8]">{{ $c->location }}</span>
                        @endif
                        @if ($seeker->hr_applications_count > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#1E1B4B] text-[#C4B5FD]">{{ $seeker->hr_applications_count }} application(s)</span>
                        @endif
                        @if ($c?->ai_score)
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#1E1B4B] text-[#C4B5FD]">AI {{ $c->ai_score }}</span>
                        @endif
                    </div>
                    @if (!empty($c?->skills) && is_array($c->skills))
                        <div class="flex flex-wrap gap-1.5 mt-3">
                            @foreach (array_slice($c->skills, 0, 4) as $skill)
                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#263248] text-[#94A3B8]">{{ is_string($skill) ? $skill : ($skill['name'] ?? '') }}</span>
                            @endforeach
                            @if (count($c->skills) > 4)
                                <span class="text-[10px] text-[#64748B]">+{{ count($c->skills) - 4 }}</span>
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
