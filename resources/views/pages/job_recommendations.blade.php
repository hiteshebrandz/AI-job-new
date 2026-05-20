@extends('layouts.candidate', ['activeNav' => 'jobs'])

@section('title', 'Job Recommendations')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'job_recommendations.css')

@section('tailwind-config', 'tailwind-config-default.js')

@push('candidate-header-left')
<div class="relative w-full max-w-xl">
    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#475569] text-[18px]" data-icon="search">search</span>
    <input class="input-dark pl-11 py-2.5 text-[14px]" placeholder="Search opportunities..." type="search" name="search" form="job-filters" value="{{ $search }}"/>
</div>
@endpush

@section('page-main-full')
<form id="job-filters" method="GET" action="{{ route('user.jobs.recommendations') }}">
<div class="pt-[80px] pb-16 px-6 lg:px-8 max-w-[1440px] mx-auto">
    <!-- Page header -->
    <div class="mb-8 animate-fade-in">
        <div class="flex items-center gap-3 mb-2">
            <span class="badge-ai">AI Curated</span>
        </div>
        <h2 class="text-[28px] font-extrabold text-[#E2E8F0]">Job Recommendations</h2>
        <p class="text-[14px] text-[#64748B] mt-1">Curated opportunities based on your profile and AI-driven skill matching.</p>
    </div>

    <div class="flex gap-6">
        <!-- Filter Sidebar -->
        <aside class="w-64 flex-shrink-0 space-y-5 hidden lg:block">
            <div class="glass-card p-5">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-[14px] font-bold text-[#E2E8F0]">Filters</h3>
                    <a href="{{ route('user.jobs.recommendations') }}" class="text-[12px] text-[#8B5CF6] hover:text-[#C4B5FD] transition-colors font-semibold">Clear all</a>
                </div>

                <!-- Salary Range -->
                <div class="mb-6">
                    <label class="text-[11px] font-semibold text-[#475569] uppercase tracking-widest block mb-3">Salary Range</label>
                    <div class="space-y-2.5">
                        @foreach (['100-150' => '$100k – $150k', '150-200' => '$150k – $200k', '200-plus' => '$200k+'] as $value => $label)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input name="salary[]" value="{{ $value }}" @checked(in_array($value, $salaryBands, true)) class="rounded border-[#334155] bg-[#1E293B] text-[#8B5CF6] focus:ring-[#8B5CF6]/20 filter-auto-submit" type="checkbox"/>
                            <span class="text-[13px] text-[#64748B] group-hover:text-[#C4B5FD] transition-colors">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Job Type -->
                <div class="mb-6">
                    <label class="text-[11px] font-semibold text-[#475569] uppercase tracking-widest block mb-3">Job Type</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['Full-time', 'Contract', 'Remote'] as $type)
                        @php $isActive = in_array($type, $jobTypes, true); @endphp
                        <label class="cursor-pointer">
                            <input type="checkbox" name="job_types[]" value="{{ $type }}" @checked($isActive) class="sr-only peer filter-auto-submit"/>
                            <span class="{{ $isActive ? 'bg-[#8B5CF6]/20 text-[#C4B5FD] border-[#8B5CF6]/40' : 'bg-transparent text-[#64748B] border-[#334155]' }} border px-3 py-1.5 rounded-full text-[12px] font-medium hover:border-[#8B5CF6]/40 hover:text-[#C4B5FD] transition-all inline-block">{{ $type }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Distance -->
                <div>
                    <label class="text-[11px] font-semibold text-[#475569] uppercase tracking-widest block mb-3">Distance</label>
                    <input class="w-full h-1.5 rounded-lg appearance-none cursor-pointer filter-auto-submit accent-[#8B5CF6]" style="background: #334155;" type="range" name="distance" min="5" max="50" step="5" value="{{ $distance }}"/>
                    <div class="flex justify-between mt-2 text-[11px] text-[#475569]">
                        <span>5 miles</span>
                        <span id="distance-label">{{ $distance >= 50 ? '50+ miles' : $distance.' miles' }}</span>
                    </div>
                </div>
            </div>

            <!-- AI Insights card -->
            <div class="glass-card p-5 relative overflow-hidden" style="background: linear-gradient(135deg, rgba(124,58,237,0.15) 0%, rgba(30,41,59,0.9) 100%);">
                <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full bg-[#8B5CF6]/10 blur-xl"></div>
                <div class="w-10 h-10 rounded-xl gradient-violet flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-white text-[20px]" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                </div>
                <h4 class="text-[14px] font-bold text-[#E2E8F0] mb-2">AI Insights</h4>
                <p class="text-[12px] text-[#64748B] mb-4 leading-relaxed">Unlock deep salary benchmarks and competitor applicant data.</p>
                <button type="button" class="btn-primary w-full py-2 text-[13px]">Upgrade</button>
            </div>
        </aside>

        <!-- Job Listings -->
        <div class="flex-1 space-y-5 min-w-0">
            <!-- Results header -->
            <div class="flex items-center justify-between animate-fade-in">
                <p class="text-[14px] text-[#94A3B8]">
                    Showing <span class="font-bold text-[#E2E8F0]">{{ $jobs->total() }} {{ $jobs->total() === 1 ? 'match' : 'matches' }}</span>
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-[12px] text-[#64748B]">Sort by:</span>
                    <select name="sort" class="bg-[#1E293B] border border-[#334155] rounded-xl text-[13px] font-medium text-[#C4B5FD] px-3 py-1.5 focus:ring-[#8B5CF6]/20 cursor-pointer filter-auto-submit">
                        <option value="match" @selected($sort === 'match')>Best Match</option>
                        <option value="salary" @selected($sort === 'salary')>Highest Salary</option>
                        <option value="recent" @selected($sort === 'recent')>Recently Posted</option>
                    </select>
                </div>
            </div>

            <!-- Job Cards -->
            <div class="space-y-4">
                @forelse ($jobs as $job)
                @php $matchPct = $job->matchPercentage(auth()->user()->candidate); @endphp
                <div class="glass-card glass-card-lift p-6 group relative overflow-hidden animate-fade-in">
                    <!-- Hover accent -->
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-[#7C3AED] to-[#06B6D4] opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    <div class="flex items-start justify-between gap-4">
                        <div class="flex gap-4 flex-1 min-w-0">
                            <div class="w-14 h-14 rounded-xl glass-card border border-[#334155] flex items-center justify-center flex-shrink-0">
                                <span class="font-bold text-[15px] gradient-text-violet">{{ $job->companyInitials() }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <h3 class="text-[18px] font-bold text-[#E2E8F0] group-hover:text-[#C4B5FD] transition-colors truncate">{{ $job->title }}</h3>
                                    @if ($job->isNewPosting())
                                    <span class="badge-success text-[10px]">New</span>
                                    @endif
                                </div>
                                <p class="text-[13px] text-[#64748B] mb-3">{{ $job->company_name }}</p>
                                <div class="flex flex-wrap gap-3">
                                    <span class="flex items-center gap-1.5 text-[13px] text-[#64748B]">
                                        <span class="material-symbols-outlined text-[15px] text-[#8B5CF6]" data-icon="payments">payments</span>
                                        {{ $job->displaySalary() }}
                                    </span>
                                    <span class="flex items-center gap-1.5 text-[13px] text-[#64748B]">
                                        <span class="material-symbols-outlined text-[15px] text-[#06B6D4]" data-icon="{{ $job->usesRemoteIcon() ? 'public' : 'location_on' }}">{{ $job->usesRemoteIcon() ? 'public' : 'location_on' }}</span>
                                        {{ $job->displayLocation() }}
                                    </span>
                                    <span class="flex items-center gap-1.5 text-[13px] text-[#64748B]">
                                        <span class="material-symbols-outlined text-[15px] text-[#475569]" data-icon="schedule">schedule</span>
                                        {{ $job->created_at->diffForHumans(short: true) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Match ring + actions -->
                        <div class="flex flex-col items-end gap-3 flex-shrink-0">
                            <div class="relative w-[64px] h-[64px]">
                                <svg class="w-full h-full -rotate-90 score-ring" viewBox="0 0 64 64">
                                    <circle cx="32" cy="32" r="26" fill="transparent" stroke="#334155" stroke-width="5"/>
                                    <circle cx="32" cy="32" r="26" fill="transparent"
                                        stroke="url(#mg{{ $job->id }})"
                                        stroke-width="5"
                                        stroke-linecap="round"
                                        stroke-dasharray="{{ round(163.4) }}"
                                        stroke-dashoffset="{{ round(163.4 * (1 - $matchPct / 100), 1) }}"
                                    />
                                    <defs>
                                        <linearGradient id="mg{{ $job->id }}" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#7C3AED"/>
                                            <stop offset="100%" stop-color="#06B6D4"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-[14px] font-extrabold text-[#C4B5FD]">{{ $matchPct }}%</span>
                                    <span class="text-[8px] text-[#475569] uppercase tracking-wide">Match</span>
                                </div>
                            </div>
                            @if ($loop->first && $jobs->currentPage() === 1)
                            <a href="{{ route('user.jobs.show', ['job' => $job, 'apply' => 1]) }}" class="btn-primary py-2 px-4 text-[13px]">Apply</a>
                            @else
                            <a href="{{ route('user.jobs.show', $job) }}" class="btn-secondary py-2 px-4 text-[13px]">Details</a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="glass-card text-center py-16 animate-fade-in">
                    <div class="empty-state-icon mx-auto mb-5">
                        <span class="material-symbols-outlined text-[36px] text-[#8B5CF6]">work_off</span>
                    </div>
                    <h3 class="text-[16px] font-semibold text-[#94A3B8] mb-2">No jobs match your filters</h3>
                    <p class="text-[13px] text-[#475569] mb-6">Try adjusting your filters or <a href="{{ route('user.jobs.recommendations') }}" class="text-[#8B5CF6] hover:underline font-semibold">clear all</a>.</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            {{ $jobs->onEachSide(1)->links('partials.pagination.job-recommendations') }}
        </div>
    </div>
</div>
</form>

@include('partials.nav.dashboard-footer')
@endsection

@push('page-scripts')
<script>
(function () {
    var form = document.getElementById('job-filters');
    if (!form) return;
    var distanceInput = form.querySelector('input[name="distance"]');
    var distanceLabel = document.getElementById('distance-label');
    var updateLabel = function () {
        if (!distanceInput || !distanceLabel) return;
        var v = parseInt(distanceInput.value, 10);
        distanceLabel.textContent = v >= 50 ? '50+ miles' : v + ' miles';
    };
    updateLabel();
    form.querySelectorAll('.filter-auto-submit').forEach(function (el) {
        el.addEventListener('change', function () { updateLabel(); form.submit(); });
    });
    if (distanceInput) distanceInput.addEventListener('input', updateLabel);
})();
</script>
@endpush
