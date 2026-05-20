@props([
    'job',
    'matchScore' => null,
    'matchReason' => null,
    'hasApplied' => false,
    'compact' => false,
])

@php
    $matchPct = $matchScore ?? $job->match_score ?? $job->matchPercentage(auth()->user()->candidate);
    $skills = array_slice($job->skillsList(), 0, $compact ? 4 : 6);
    $employmentType = $job->job_type ?: ($job->work_mode ?? '—');
    $reason = $matchReason ?? $job->match_reason ?? null;
@endphp

<div class="p-4 rounded-xl border border-outline-variant/30 bg-surface-container/40
            hover:border-secondary/40 hover:bg-surface-container/70 transition-all">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-secondary/10 flex items-center justify-center">
            <span class="font-bold text-xs text-secondary">{{ $job->companyInitials() }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <h4 class="text-sm font-bold truncate">
                        <a href="{{ route('user.jobs.show', $job) }}" class="hover:text-secondary transition-colors">
                            {{ $job->title }}
                        </a>
                    </h4>
                    <p class="text-xs text-on-surface-variant mt-0.5">{{ $job->company_name }}</p>
                </div>
                <div class="flex-shrink-0 text-right">
                    <span class="text-sm font-extrabold text-secondary">{{ $matchPct }}%</span>
                    <p class="text-[9px] text-on-surface-variant uppercase">Match</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mt-2 text-[11px] text-on-surface-variant">
                <span class="inline-flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[13px]">location_on</span>
                    {{ $job->displayLocation() }}
                </span>
                <span class="inline-flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[13px]">work</span>
                    {{ $employmentType }}
                </span>
                @if(!$compact)
                <span class="inline-flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[13px]">payments</span>
                    {{ $job->displaySalary() }}
                </span>
                @endif
            </div>

            @if(count($skills) > 0)
            <div class="flex flex-wrap gap-1.5 mt-2">
                @foreach($skills as $skill)
                <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-secondary/10 text-secondary border border-secondary/15">
                    {{ $skill }}
                </span>
                @endforeach
            </div>
            @endif

            @if($reason)
            <p class="text-[11px] text-on-surface-variant/80 mt-2 line-clamp-2">{{ $reason }}</p>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 mt-3 pt-3 border-t border-outline-variant/20">
        <a href="{{ route('user.jobs.show', $job) }}" class="text-xs text-on-surface-variant hover:text-secondary">View details</a>
        @if($hasApplied)
            <span class="text-xs text-on-surface-variant px-3 py-1.5">Applied</span>
        @else
            <a href="{{ route('user.jobs.show', ['job' => $job, 'apply' => 1]) }}"
               class="text-xs font-semibold text-white px-3 py-1.5 rounded-lg bg-gradient-to-r from-secondary to-purple-600 hover:opacity-90">
                Apply Now
            </a>
        @endif
    </div>
</div>
