@extends('layouts.candidate', ['activeNav' => 'jobs'])

@section('title', 'Saved Jobs')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'job_recommendations.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('page-main')
<div class="mb-10">
<h2 class="font-headline-lg text-headline-lg text-primary mb-2">Saved Jobs</h2>
<p class="font-body-md text-on-surface-variant">Jobs you have bookmarked for later.</p>
</div>
<div class="grid grid-cols-1 gap-6">
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
<div class="bg-white p-12 rounded-xl border border-outline-variant text-center">
<span class="material-symbols-outlined text-5xl text-outline mb-4">bookmark_border</span>
<p class="font-body-md text-on-surface-variant">No saved jobs yet. Browse recommendations and save roles you like.</p>
<a href="{{ route('user.jobs.recommendations') }}" class="inline-block mt-6 px-8 py-3 rounded-xl bg-secondary text-white font-title-md">Browse Jobs</a>
</div>
@endforelse
</div>
@if ($savedJobs->hasPages())
<div class="pt-8">{{ $savedJobs->links() }}</div>
@endif
<div id="toast-root" aria-live="polite"></div>
@endsection

@push('page-scripts')
<script>
window.savedJobsConfig = { csrf: document.querySelector('meta[name="csrf-token"]')?.content };
</script>
<script src="{{ asset('js/saved-jobs.js') }}"></script>
@endpush
