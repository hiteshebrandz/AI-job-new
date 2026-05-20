@extends('layouts.candidate', ['activeNav' => 'jobs'])

@section('title', $job->title)

@section('body-class', 'bg-background text-on-surface font-body-md')

@section('page-css', 'job_details.css')

@section('tailwind-config', 'tailwind-config-forms.js')

@section('page-main-full')

<!-- Job Header -->
<section class="pt-[80px] px-6 lg:px-8 pb-6 max-w-[1440px] mx-auto">
    <div class="glass-card p-7 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 {{ $highlightApply ? 'ring-2 ring-[#8B5CF6]/40' : '' }} animate-fade-in" id="apply-section">
        <div class="flex items-center gap-5">
            <!-- Company Logo -->
            <div class="w-16 h-16 rounded-2xl glass-card border border-[#334155] flex items-center justify-center p-3 flex-shrink-0">
                @if ($company->logo)
                <img alt="{{ $company->name }} Logo" class="w-full h-full object-contain" src="{{ asset('storage/'.$company->logo) }}"/>
                @else
                <span class="font-bold text-[18px] gradient-text-violet">{{ $company->initials() }}</span>
                @endif
            </div>
            <div>
                <h1 class="text-[26px] font-extrabold text-[#E2E8F0] mb-2">{{ $job->title }}</h1>
                <div class="flex flex-wrap gap-3 items-center">
                    <span class="flex items-center gap-1.5 text-[13px] text-[#64748B]">
                        <span class="material-symbols-outlined text-[15px] text-[#8B5CF6]" data-icon="location_on">location_on</span>
                        {{ $job->displayLocation() }}
                    </span>
                    <span class="flex items-center gap-1.5 text-[13px] text-[#64748B]">
                        <span class="material-symbols-outlined text-[15px] text-[#06B6D4]" data-icon="payments">payments</span>
                        {{ $job->displaySalary() }}
                    </span>
                    <span class="badge-violet text-[11px]">{{ $job->job_type }}</span>
                    @if ($job->experience_required)
                    <span class="flex items-center gap-1.5 text-[13px] text-[#64748B]">
                        <span class="material-symbols-outlined text-[15px]" data-icon="work_history">work_history</span>
                        {{ $job->experience_required }}
                    </span>
                    @endif
                    @if ($job->created_at)
                    <span class="flex items-center gap-1.5 text-[13px] text-[#64748B]">
                        <span class="material-symbols-outlined text-[15px]" data-icon="schedule">schedule</span>
                        Posted {{ $job->created_at->diffForHumans() }}
                    </span>
                    @endif
                    @if ($job->application_deadline)
                    <span class="flex items-center gap-1.5 text-[13px] text-[#64748B]">
                        <span class="material-symbols-outlined text-[15px] text-[#F87171]" data-icon="event">event</span>
                        Deadline {{ $job->application_deadline->format('M j, Y') }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action buttons -->
        <div class="flex gap-3 w-full md:w-auto">
            <button type="button" id="job-save-btn" data-saved="{{ $hasSaved ? '1' : '0' }}"
                class="flex-1 md:flex-none {{ $hasSaved ? 'btn-secondary' : 'btn-ghost' }} py-3 px-6 text-[14px]">
                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' {{ $hasSaved ? 1 : 0 }}">bookmark</span>
                {{ $hasSaved ? 'Saved' : 'Save Job' }}
            </button>
            <button type="button" id="job-apply-btn" data-applied="{{ $hasApplied ? '1' : '0' }}"
                class="flex-1 md:flex-none {{ $hasApplied ? 'btn-ghost opacity-60 cursor-not-allowed' : 'btn-primary' }} py-3 px-8 text-[14px]"
                {{ $hasApplied ? 'disabled' : '' }}>
                <span class="material-symbols-outlined text-[18px]">{{ $hasApplied ? 'check_circle' : 'send' }}</span>
                {{ $hasApplied ? 'Applied' : 'Apply Now' }}
            </button>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="px-6 lg:px-8 pb-16 max-w-[1440px] mx-auto animate-fade-in-delay-1">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Job details -->
        <div class="lg:col-span-8 space-y-6">
            <div class="glass-card p-7">
                <h2 class="text-[18px] font-bold text-[#E2E8F0] mb-5">About the Role</h2>
                <div class="space-y-4 text-[14px] text-[#94A3B8] leading-relaxed">
                    @if ($job->description)
                    @foreach (preg_split('/\r\n\r\n|\n\n/', $job->description) as $paragraph)
                    @if (trim($paragraph) !== '')
                    <p>{{ trim($paragraph) }}</p>
                    @endif
                    @endforeach
                    @else
                    <p>Role details will be updated soon.</p>
                    @endif
                </div>

                @if (count($skills) > 0)
                <h3 class="text-[15px] font-bold text-[#E2E8F0] mt-8 mb-4">Skills Required</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($skills as $skill)
                    <span class="skill-tag">{{ $skill }}</span>
                    @endforeach
                </div>
                @endif

                <h3 class="text-[15px] font-bold text-[#E2E8F0] mt-8 mb-4">Key Responsibilities</h3>
                <ul class="space-y-3">
                    @forelse ($responsibilities as $item)
                    <li class="flex gap-3 text-[14px] text-[#94A3B8]">
                        <span class="material-symbols-outlined text-[#8B5CF6] text-[18px] flex-shrink-0 mt-0.5" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        {{ $item }}
                    </li>
                    @empty
                    <li class="flex gap-3 text-[14px] text-[#94A3B8]">
                        <span class="material-symbols-outlined text-[#8B5CF6] text-[18px] flex-shrink-0 mt-0.5" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        Collaborate with cross-functional teams to deliver high-quality outcomes.
                    </li>
                    @endforelse
                </ul>

                <h3 class="text-[15px] font-bold text-[#E2E8F0] mt-8 mb-4">Requirements</h3>
                <ul class="space-y-3">
                    @forelse ($requirements as $item)
                    <li class="flex gap-3 text-[14px] text-[#94A3B8]">
                        <span class="material-symbols-outlined text-[#06B6D4] text-[18px] flex-shrink-0 mt-0.5" data-icon="arrow_forward">arrow_forward</span>
                        {{ $item }}
                    </li>
                    @empty
                    @if ($job->minimum_qualification)
                    <li class="flex gap-3 text-[14px] text-[#94A3B8]">
                        <span class="material-symbols-outlined text-[#06B6D4] text-[18px] flex-shrink-0 mt-0.5">arrow_forward</span>
                        {{ $job->minimum_qualification }}
                    </li>
                    @endif
                    @if ($job->preferred_qualification)
                    <li class="flex gap-3 text-[14px] text-[#94A3B8]">
                        <span class="material-symbols-outlined text-[#06B6D4] text-[18px] flex-shrink-0 mt-0.5">arrow_forward</span>
                        {{ $job->preferred_qualification }}
                    </li>
                    @endif
                    @if (! $job->minimum_qualification && ! $job->preferred_qualification)
                    <li class="flex gap-3 text-[14px] text-[#94A3B8]">
                        <span class="material-symbols-outlined text-[#06B6D4] text-[18px] flex-shrink-0 mt-0.5">arrow_forward</span>
                        Relevant experience and skills for this role.
                    </li>
                    @endif
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Right sidebar -->
        <aside class="lg:col-span-4 space-y-5">
            <!-- Company Info -->
            <div class="glass-card p-6">
                <h2 class="text-[16px] font-bold text-[#E2E8F0] mb-5">Company Information</h2>
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-12 h-12 rounded-xl glass-card border border-[#334155] flex items-center justify-center p-2 flex-shrink-0">
                        @if ($company->logo)
                        <img alt="{{ $company->name }}" class="w-full h-full object-contain" src="{{ asset('storage/'.$company->logo) }}"/>
                        @else
                        <span class="font-bold text-[14px] gradient-text-violet">{{ $company->initials() }}</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-[14px] font-semibold text-[#E2E8F0]">{{ $company->name }}</p>
                        <p class="text-[12px] text-[#64748B]">{{ $company->description ?? 'Human Capital Intelligence' }}</p>
                    </div>
                </div>
                <div class="space-y-0 divide-y divide-[#334155]">
                    @foreach ([
                        ['Industry', $company->industry ?? 'HR Tech'],
                        ['Company Size', $company->company_size ?? '500+ Employees'],
                        ['Founded', $company->founded ?? '2016'],
                        ['Recruiter', $job->hr->name ?? 'HR Team'],
                        ['Contact', $job->hr->email ?? '—'],
                        ['Match Score', $matchScore . '%'],
                    ] as $item)
                    <div class="flex justify-between items-center py-3">
                        <span class="text-[11px] font-semibold text-[#475569] uppercase tracking-wide">{{ $item[0] }}</span>
                        <span class="text-[13px] font-semibold text-[#E2E8F0] truncate max-w-[160px] text-right {{ $item[0] === 'Match Score' ? 'text-[#C4B5FD]' : '' }}">{{ $item[1] }}</span>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn-secondary w-full py-2.5 mt-5 text-[13px]">View Company Profile</button>
            </div>

            <!-- Perks -->
            @if (count($benefits) > 0)
            <div class="glass-card p-6">
                <h2 class="text-[16px] font-bold text-[#E2E8F0] mb-5">Perks & Benefits</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($benefits as $benefit)
                    <div class="p-3 rounded-xl bg-[#162032] border border-[#334155] text-center">
                        <span class="material-symbols-outlined text-[#8B5CF6] text-[22px] mb-1 block" data-icon="{{ $benefit['icon'] }}">{{ $benefit['icon'] }}</span>
                        <span class="text-[11px] text-[#64748B]">{{ $benefit['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Similar Jobs -->
            @if ($similarJobs->isNotEmpty())
            <div class="glass-card p-6">
                <h2 class="text-[16px] font-bold text-[#E2E8F0] mb-5">Similar Jobs</h2>
                <div class="space-y-4">
                    @foreach ($similarJobs as $similar)
                    <a class="group block p-3 rounded-xl bg-[#162032] border border-[#334155] hover:border-[#8B5CF6]/40 transition-all" href="{{ route('user.jobs.show', $similar) }}">
                        <span class="block text-[13px] font-semibold text-[#E2E8F0] group-hover:text-[#C4B5FD] transition-colors mb-1">{{ $similar->title }}</span>
                        <span class="text-[12px] text-[#64748B]">{{ $similar->displayLocation() }} · {{ $similar->displaySalary() }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </aside>
    </div>
</section>

@include('partials.nav.dashboard-footer')

<div id="toast-root" aria-live="polite"></div>
<div id="job-action-loader" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center">
    <div class="glass-card p-6 flex flex-col items-center gap-4">
        <div class="w-10 h-10 border-4 border-[#334155] border-t-[#8B5CF6] rounded-full animate-spin"></div>
        <p class="text-[13px] text-[#64748B]">Processing...</p>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
window.jobDetailsConfig = {
    applyUrl: @json(route('user.jobs.apply', $job)),
    saveUrl: @json(route('user.jobs.save', $job)),
    highlightApply: @json($highlightApply),
};
</script>
<script src="{{ asset('js/job-details.js') }}"></script>
@endpush
