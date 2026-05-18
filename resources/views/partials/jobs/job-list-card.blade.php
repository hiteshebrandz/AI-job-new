@props(['job', 'matchScore' => null, 'savedAt' => null, 'showRemove' => false, 'hasApplied' => false, 'application' => null])

<div class="bg-white p-card-padding rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow group relative overflow-hidden">
<div class="flex items-start justify-between gap-4">
<div class="flex gap-6 flex-1 min-w-0">
<div class="w-16 h-16 bg-surface-container rounded-xl flex items-center justify-center flex-shrink-0">
<span class="font-title-md text-title-md font-bold text-secondary">{{ $job->companyInitials() }}</span>
</div>
<div class="min-w-0">
<h3 class="font-headline-lg text-[24px] text-primary group-hover:text-secondary transition-colors mb-1 truncate">{{ $job->title }}</h3>
<p class="font-title-md text-title-md text-on-surface-variant mb-3">{{ $job->company_name }}</p>
<div class="flex flex-wrap gap-4">
<div class="flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-lg" data-icon="location_on">location_on</span>
<span class="font-body-sm text-body-sm">{{ $job->displayLocation() }}</span>
</div>
<div class="flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-lg" data-icon="payments">payments</span>
<span class="font-body-sm text-body-sm">{{ $job->displaySalary() }}</span>
</div>
@if ($savedAt)
<div class="flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-lg" data-icon="bookmark">bookmark</span>
<span class="font-body-sm text-body-sm">Saved {{ $savedAt->diffForHumans() }}</span>
</div>
@endif
@if ($application)
<div class="flex items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-lg" data-icon="schedule">schedule</span>
<span class="font-body-sm text-body-sm">Applied {{ $application->applied_at->format('M j, Y') }}</span>
</div>
@endif
</div>
</div>
</div>
@if ($matchScore !== null)
<div class="flex flex-col items-center flex-shrink-0">
<div class="relative w-20 h-20 flex items-center justify-center">
<svg class="w-full h-full">
<circle class="text-surface-variant" cx="48" cy="48" fill="transparent" r="40" stroke="currentColor" stroke-width="6"></circle>
<circle class="text-secondary progress-ring-circle" cx="48" cy="48" fill="transparent" r="40" stroke="currentColor" stroke-dasharray="251.2" stroke-dashoffset="{{ round(251.2 * (1 - $matchScore / 100), 1) }}" stroke-width="6"></circle>
</svg>
<div class="absolute inset-0 flex flex-col items-center justify-center">
<span class="font-headline-lg text-[22px] text-secondary font-extrabold">{{ $matchScore }}%</span>
<span class="font-label-caps text-[8px] uppercase tracking-tighter">Match</span>
</div>
</div>
</div>
@endif
<div class="flex flex-col gap-2 mt-2 flex-shrink-0">
@if ($showRemove)
<button type="button" data-remove-job="{{ $job->id }}" class="border border-outline-variant text-on-surface-variant px-4 py-2 rounded-xl font-label-caps text-label-caps hover:bg-surface-container-low transition-all text-center remove-saved-job">Remove</button>
@endif
@if ($hasApplied)
<span class="px-6 py-2 rounded-xl bg-surface-container-high text-on-surface-variant font-title-md text-title-md text-center">Applied</span>
@else
<a href="{{ route('user.jobs.show', ['job' => $job, 'apply' => 1]) }}" class="bg-primary text-white px-6 py-2 rounded-xl font-title-md text-title-md hover:bg-secondary transition-all active:scale-95 shadow-sm inline-block text-center">Apply</a>
@endif
<a href="{{ route('user.jobs.show', $job) }}" class="border border-secondary text-secondary px-6 py-2 rounded-xl font-title-md text-title-md hover:bg-secondary/5 transition-all active:scale-95 inline-block text-center">View Details</a>
</div>
</div>
</div>
