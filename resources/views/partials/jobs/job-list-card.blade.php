@props(['job', 'matchScore' => null, 'savedAt' => null, 'showRemove' => false, 'hasApplied' => false, 'application' => null])

<div class="glass-card glass-card-lift p-6 group relative overflow-hidden animate-fade-in">
    <!-- Accent top line on hover -->
    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-secondary to-[#6063ee] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

    <div class="flex items-start justify-between gap-4">
        <!-- Left: Company + Info -->
        <div class="flex gap-4 flex-1 min-w-0">
            <!-- Company Logo -->
            <div class="w-14 h-14 rounded-xl glass-card flex items-center justify-center flex-shrink-0 border border-outline-variant">
                <span class="font-bold text-[16px] gradient-text-violet">{{ $job->companyInitials() }}</span>
            </div>

            <div class="min-w-0 flex-1">
                <h3 class="text-[18px] font-bold text-on-surface group-hover:text-secondary transition-colors mb-1 truncate">{{ $job->title }}</h3>
                <p class="text-[14px] text-on-surface-variant mb-3">{{ $job->company_name }}</p>

                <!-- Meta tags -->
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-secondary" data-icon="location_on">location_on</span>
                        <span class="text-[13px]">{{ $job->displayLocation() }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-secondary" data-icon="payments">payments</span>
                        <span class="text-[13px]">{{ $job->displaySalary() }}</span>
                    </div>
                    @if ($savedAt)
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-[var(--badge-warning-text)]" data-icon="bookmark">bookmark</span>
                        <span class="text-[13px]">Saved {{ $savedAt->diffForHumans() }}</span>
                    </div>
                    @endif
                    @if ($application)
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-[var(--badge-success-text)]" data-icon="schedule">schedule</span>
                        <span class="text-[13px]">Applied {{ $application->applied_at->format('M j, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Match score + actions -->
        <div class="flex flex-col items-end gap-3 flex-shrink-0">
            @if ($matchScore !== null)
            <div class="relative w-[68px] h-[68px] flex items-center justify-center">
                <svg class="w-full h-full score-ring -rotate-90" viewBox="0 0 68 68">
                    <circle cx="34" cy="34" r="28" fill="transparent" stroke="var(--border-default)" stroke-width="5"/>
                    <circle cx="34" cy="34" r="28" fill="transparent"
                        stroke="url(#matchGrad{{ $job->id }})"
                        stroke-width="5"
                        stroke-linecap="round"
                        stroke-dasharray="{{ round(175.9) }}"
                        stroke-dashoffset="{{ round(175.9 * (1 - $matchScore / 100), 1) }}"
                    />
                    <defs>
                        <linearGradient id="matchGrad{{ $job->id }}" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#4648d4"/>
                            <stop offset="100%" stop-color="#6063ee"/>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-[15px] font-extrabold text-secondary">{{ $matchScore }}%</span>
                    <span class="text-[8px] text-on-surface-variant uppercase tracking-wide">Match</span>
                </div>
            </div>
            @endif

            <div class="flex flex-col gap-2">
                @if ($showRemove)
                <button type="button" data-remove-job="{{ $job->id }}" class="btn-ghost py-2 px-4 text-[13px] remove-saved-job">Remove</button>
                @endif

                @if ($hasApplied)
                <span class="px-4 py-2 rounded-xl bg-surface-container-high text-on-surface-variant text-[13px] font-medium text-center border border-outline-variant">Applied</span>
                @else
                <a href="{{ route('user.jobs.show', ['job' => $job, 'apply' => 1]) }}" class="btn-primary py-2 px-4 text-[13px]">Apply Now</a>
                @endif

                <a href="{{ route('user.jobs.show', $job) }}" class="btn-secondary py-2 px-4 text-[13px]">View Details</a>
            </div>
        </div>
    </div>
</div>
