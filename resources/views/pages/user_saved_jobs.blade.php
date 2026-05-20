@extends('layouts.candidate', ['activeNav' => 'saved'])

@section('title', 'Saved Jobs')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'job_recommendations.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('page-main')
<div class="mb-8 animate-fade-in">
    <div class="flex items-center gap-3 mb-2">
        <span class="badge-warning text-[11px]">Bookmarked</span>
    </div>
    <h2 class="text-[28px] font-extrabold text-[#E2E8F0]">Saved Jobs</h2>
    <p class="text-[14px] text-[#64748B] mt-1">Jobs you have bookmarked for later review.</p>
</div>

<div class="space-y-4">
    @forelse ($savedJobs as $record)
    @if ($record->job)
    @include('partials.jobs.job-list-card', [
        'job' => $record->job,
        'matchScore' => $record->job->match_score ?? null,
        'savedAt' => $record->created_at,
        'showRemove' => true,
        'hasApplied' => $record->job->has_applied ?? false,
    ])
    @endif
    @empty
    <div class="glass-card text-center py-16 animate-fade-in">
        <div class="empty-state-icon mx-auto mb-5">
            <span class="material-symbols-outlined text-[36px] text-[#FBBF24]">bookmark_border</span>
        </div>
        <h3 class="text-[16px] font-semibold text-[#94A3B8] mb-2">No saved jobs yet</h3>
        <p class="text-[13px] text-[#475569] mb-6">Browse recommendations and bookmark roles you like.</p>
        <a href="{{ route('user.jobs.recommendations') }}" class="btn-primary py-2.5 px-7 text-[14px]">Browse Jobs</a>
    </div>
    @endforelse
</div>

@if ($savedJobs->hasPages())
<div class="pt-8">{{ $savedJobs->links() }}</div>
@endif

<div id="toast-root" aria-live="polite"></div>

@include('partials.nav.dashboard-footer')
@endsection

@push('page-scripts')
<script>
window.savedJobsConfig = { csrf: document.querySelector('meta[name="csrf-token"]')?.content };
</script>
<script src="{{ asset('js/saved-jobs.js') }}"></script>
@endpush
