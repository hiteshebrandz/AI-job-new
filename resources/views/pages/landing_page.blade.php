@extends('layouts.app')

@section('title', 'TalentSync AI — AI-Powered Talent & Hiring Platform')

@section('body-class', 'bg-background text-on-surface font-body-md overflow-x-hidden')

@section('page-css', 'landing_page.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
@include('partials.nav.public-header')

{{-- ================================================================
     HERO SECTION
     ================================================================ --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden hero-gradient pt-24">
    {{-- Soft gradient blobs --}}
    <div class="blob blob-violet w-[600px] h-[600px] -top-40 -left-32 animate-blob" style="opacity:0.06;"></div>
    <div class="blob blob-cyan w-[500px] h-[500px] -bottom-32 -right-32 animate-blob" style="opacity:0.05; animation-delay:3s;"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left: Copy --}}
            <div class="animate-fade-in">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-8 section-label">
                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">auto_awesome</span>
                    <span>Next-Gen HR Technology</span>
                </div>

                <h1 class="text-[48px] lg:text-[62px] font-extrabold leading-[1.1] tracking-tight mb-6" style="font-family:'Plus Jakarta Sans',sans-serif; color: var(--text-heading);">
                    One Platform for<br>
                    <span class="gradient-text-violet">Smarter Hiring</span> &amp;<br>
                    Better Teams
                </h1>

                <p class="text-[17px] leading-relaxed mb-10 max-w-lg" style="color: var(--text-muted);">
                    {{ config('app.name') }} brings together AI-powered resume analysis, intelligent candidate matching, job management, attendance, payroll, and performance tracking — all in one modern platform.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-10">
                    <a href="{{ route('register') }}" class="btn-primary py-4 px-8 text-[15px] font-semibold">
                        <span class="material-symbols-outlined">rocket_launch</span>
                        Get Started Free
                    </a>
                    <a href="{{ route('register') }}?role=hr" class="btn-ghost py-4 px-8 text-[15px]">
                        <span class="material-symbols-outlined">business_center</span>
                        I'm an Employer
                    </a>
                </div>

                {{-- Audience badges --}}
                <div class="flex flex-wrap gap-3">
                    @foreach ([
                        ['person_search', 'For Job Seekers',  'rgba(70,72,212,0.06)', 'rgba(70,72,212,0.18)', '#575acb'],
                        ['corporate_fare','For HR Teams',     'rgba(96,99,238,0.06)', 'rgba(96,99,238,0.18)', '#6669db'],
                        ['manage_accounts','For Admins',      'rgba(4,120,87,0.06)',  'rgba(4,120,87,0.18)',  '#0b7a5d'],
                    ] as $b)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold" style="background:{{ $b[2] }}; border:1px solid {{ $b[3] }}; color:{{ $b[4] }};">
                        <span class="material-symbols-outlined text-[14px]">{{ $b[0] }}</span>{{ $b[1] }}
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Right: AI matching card --}}
            <div class="animate-fade-in-delay-2">
                <div class="glass-card p-8 relative overflow-hidden animate-float-slow">
                    <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full pointer-events-none" style="background:rgba(70,72,212,0.08); filter:blur(32px);"></div>

                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest mb-1" style="color:var(--brand-secondary);">Live Engine</p>
                            <h3 class="text-[18px] font-bold" style="color:var(--text-primary);">AI Matching in Progress</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 animate-pulse-glow" style="background:var(--brand-gradient);">
                            <span class="material-symbols-outlined text-white text-[22px]" style="font-variation-settings:'FILL' 1;">model_training</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach ([
                            ['Principal Product Designer','TechFlow Systems', 98],
                            ['VP of Engineering','Quantix AI', 92],
                            ['Chief Data Officer','Stellar Corp', 87],
                        ] as $match)
                        <div class="flex items-center gap-3 p-3.5 rounded-xl border" style="background:var(--bg-surface-low); border-color:var(--border-subtle);">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--brand-gradient);">
                                <span class="material-symbols-outlined text-white text-[16px]">person</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-semibold truncate" style="color:var(--text-primary);">{{ $match[0] }}</p>
                                <p class="text-[11px]" style="color:var(--text-muted);">Matching · {{ $match[1] }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-[15px] font-extrabold" style="color:var(--brand-primary);">{{ $match[2] }}%</span>
                                <div class="progress-bar-track w-14 mt-1">
                                    <div class="progress-bar-fill" style="width:{{ $match[2] }}%;"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-5 pt-4 flex items-center justify-between" style="border-top: 1px solid var(--border-subtle);">
                        <span class="text-[12px]" style="color:var(--text-muted);">
                            {{ $stats['candidates'] > 0 ? number_format($stats['candidates']) . ' candidates active' : 'Processing candidates' }}
                        </span>
                        <span class="badge-ai">Live</span>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-2 justify-center lg:justify-start">
                    @foreach (['Resume AI', 'ATS Checker', 'AI Hiring', 'Payroll', 'Analytics'] as $chip)
                    <span class="px-3 py-1.5 rounded-lg text-[12px] font-medium" style="color:var(--text-muted); border:1px solid var(--border-subtle); background:var(--bg-surface);">{{ $chip }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================================================================
     LIVE STATS BAR (DB-driven)
     ================================================================ --}}
<section class="stats-bar py-10">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-0">
            @php
            $statsDisplay = [
                [$stats['candidates']  > 0 ? number_format($stats['candidates'])  . '+' : '—', 'Candidates Registered',  'people',    '#4648d4'],
                [$stats['activeJobs']  > 0 ? number_format($stats['activeJobs'])  . '+' : '—', 'Active Job Openings',    'work',      '#6063ee'],
                [$stats['companies']   > 0 ? number_format($stats['companies'])   . '+' : '—', 'Companies Onboarded',    'business',  '#047857'],
                [$stats['applications']> 0 ? number_format($stats['applications']).'+' : '—',  'Applications Processed', 'description','#b45309'],
            ];
            @endphp
            @foreach ($statsDisplay as $i => $s)
                @if ($i > 0)
                    <div class="stat-divider mx-6 lg:mx-10 hidden sm:block"></div>
                @endif
                <div class="stat-card flex flex-col items-center py-4 px-6 min-w-[140px]">
                    <div class="w-10 h-10 rounded-xl mb-3 flex items-center justify-center" style="background:{{ $s[3] }}14;">
                        <span class="material-symbols-outlined text-[20px]" style="color:{{ $s[3] }};">{{ $s[2] }}</span>
                    </div>
                    <p class="text-[32px] font-extrabold leading-none mb-1 stat-number" style="color:var(--text-heading); font-family:'Plus Jakarta Sans',sans-serif;" data-value="{{ $s[0] }}">{{ $s[0] }}</p>
                    <p class="text-[12px] text-center" style="color:var(--text-muted);">{{ $s[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================================================================
     TRUSTED BY
     ================================================================ --}}
<section class="py-8" style="border-bottom: 1px solid var(--border-subtle);">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <p class="text-center text-[11px] font-semibold uppercase tracking-widest mb-6" style="color:var(--text-muted);">Trusted by Global Leaders</p>
        <div class="flex flex-wrap justify-center items-center gap-10 lg:gap-20" style="opacity:0.45;">
            @foreach (['VOLVO', 'ORACLE', 'STRIPE', 'AIRBNB', 'ADOBE', 'NOTION'] as $brand)
            <span class="text-[17px] font-extrabold tracking-tighter cursor-default transition-all hover:opacity-100" style="color:var(--text-secondary);">{{ $brand }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- ================================================================
     AI TOOLS SPOTLIGHT
     ================================================================ --}}
<section id="ai-tools" class="py-24 section-bg-base">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="section-label mb-4 inline-flex">
                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">auto_awesome</span>
                AI-Powered Tools
            </span>
            <h2 class="text-[38px] font-extrabold tracking-tight mt-4 mb-4" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Supercharge Your Career &amp; Hiring</h2>
            <p class="text-[16px] max-w-2xl mx-auto" style="color:var(--text-muted);">Our two flagship AI tools give candidates the edge they need and help employers find the perfect match — instantly.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- Resume Tester --}}
            <div class="ai-tool-card p-8">
                <div class="ai-tool-glow w-64 h-64 -top-16 -right-16" style="background:#4648d4;"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background:rgba(70,72,212,0.10); border:1px solid rgba(70,72,212,0.22);">
                            <span class="material-symbols-outlined text-[30px]" style="color:#4648d4; font-variation-settings:'FILL' 1;">description</span>
                        </div>
                        <span class="section-label text-[10px]">
                            <span class="w-2 h-2 rounded-full inline-block animate-pulse" style="background:#4648d4;"></span>
                            Live AI
                        </span>
                    </div>
                    <h3 class="text-[24px] font-bold mb-3" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Resume Tester</h3>
                    <p class="text-[14px] leading-relaxed mb-6" style="color:var(--text-muted);">Upload your resume and receive an instant AI score with detailed skill gap analysis, experience evaluation, and actionable improvement suggestions.</p>

                    <div class="flex flex-wrap gap-2 mb-8">
                        @foreach (['AI Score', 'Skill Gap Analysis', 'Experience Rating', 'Instant Feedback'] as $pill)
                        <span class="feature-pill feature-pill-violet">
                            <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                            {{ $pill }}
                        </span>
                        @endforeach
                    </div>

                    <div class="rounded-xl p-5 mb-6" style="background:var(--bg-surface-low); border:1px solid var(--border-subtle);">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[12px] font-semibold uppercase tracking-wide" style="color:var(--text-muted);">Sample Score</span>
                            <span class="text-[22px] font-extrabold" style="color:#4648d4;">87<span class="text-[14px]" style="color:var(--text-muted);">/100</span></span>
                        </div>
                        <div class="progress-bar-track w-full h-2.5 rounded-full">
                            <div class="progress-bar-fill rounded-full" style="width:87%; height:100%;"></div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-[11px]" style="color:var(--text-muted);">Skills Match</span>
                            <span class="text-[11px] font-semibold" style="color:#047857;">Strong candidate</span>
                        </div>
                    </div>

                    <a href="{{ route('tools.guest', ['tool' => 'resume']) }}" class="btn-primary py-3 px-6 text-[14px] font-semibold w-full justify-center">
                        <span class="material-symbols-outlined">upload_file</span>
                        Test Your Resume Now
                    </a>
                </div>
            </div>

            {{-- ATS Resume Checker --}}
            <div class="ai-tool-card ai-tool-card-cyan p-8">
                <div class="ai-tool-glow w-64 h-64 -top-16 -left-16" style="background:#6063ee;"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background:rgba(96,99,238,0.10); border:1px solid rgba(96,99,238,0.22);">
                            <span class="material-symbols-outlined text-[30px]" style="color:#6063ee; font-variation-settings:'FILL' 1;">analytics</span>
                        </div>
                        <span class="section-label section-label-cyan text-[10px]">
                            <span class="w-2 h-2 rounded-full inline-block animate-pulse" style="background:#6063ee;"></span>
                            ATS Engine
                        </span>
                    </div>
                    <h3 class="text-[24px] font-bold mb-3" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">ATS Resume Checker</h3>
                    <p class="text-[14px] leading-relaxed mb-6" style="color:var(--text-muted);">Check your resume against Applicant Tracking System filters. Get keyword optimization tips, formatting fixes, and a compatibility score before you apply.</p>

                    <div class="flex flex-wrap gap-2 mb-8">
                        @foreach (['ATS Compatibility', 'Keyword Optimization', 'Format Check', 'AI Optimizer'] as $pill)
                        <span class="feature-pill feature-pill-cyan">
                            <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                            {{ $pill }}
                        </span>
                        @endforeach
                    </div>

                    <div class="rounded-xl p-5 mb-6" style="background:var(--bg-surface-low); border:1px solid var(--border-subtle);">
                        <div class="grid grid-cols-3 gap-3 text-center">
                            @foreach ([['94%','ATS Score','#4648d4'], ['12','Keywords','#047857'], ['3','Fixes','#b45309']] as $m)
                            <div>
                                <p class="text-[20px] font-extrabold" style="color:{{ $m[2] }};">{{ $m[0] }}</p>
                                <p class="text-[11px]" style="color:var(--text-muted);">{{ $m[1] }}</p>
            </div>
            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('tools.guest', ['tool' => 'ats']) }}" class="btn-secondary py-3 px-6 text-[14px] font-semibold w-full justify-center">
                        <span class="material-symbols-outlined">fact_check</span>
                        Check ATS Compatibility
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================================================================
     HR MODULES GRID
     ================================================================ --}}
<section id="modules" class="py-24 section-bg-low">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="section-label mb-4 inline-flex">
                <span class="material-symbols-outlined text-[14px]">grid_view</span>
                Full Platform
            </span>
            <h2 class="text-[38px] font-extrabold tracking-tight mt-4 mb-4" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Everything HR in One Place</h2>
            <p class="text-[16px] max-w-2xl mx-auto" style="color:var(--text-muted);">From first contact to final paycheck — manage your entire workforce lifecycle with our integrated module suite.</p>
        </div>

        @php
        $modules = [
            ['people',        'Candidate Management',    'Track, filter, and manage your full candidate pipeline with AI-assisted scoring and profile enrichment.',               '#4648d4','rgba(70,72,212,0.10)','rgba(70,72,212,0.20)','module-card-violet', route('register')],
            ['description',   'Resume Screening',        'AI parses every resume, extracts structured data, and ranks candidates by job fit — automatically.',                   '#6063ee','rgba(96,99,238,0.10)','rgba(96,99,238,0.20)','module-card-cyan',   route('register')],
            ['work',          'Job Posting',             'Create rich job listings with screening questions, skills requirements, and one-click publish.',                       '#047857','rgba(4,120,87,0.10)', 'rgba(4,120,87,0.20)', 'module-card-green',  route('register')],
            ['model_training','AI Hiring & Matching',    'Upload a job description and let the AI surface the best-matching candidates with ranked scores.',                    '#4648d4','rgba(70,72,212,0.10)','rgba(70,72,212,0.20)','module-card-violet', route('register')],
            ['event_note',    'Interview Scheduling',    'Schedule, track, and manage interview stages with status updates and automated candidate notifications.',             '#b45309','rgba(180,83,9,0.10)','rgba(180,83,9,0.20)', 'module-card-amber',  route('register')],
            ['badge',         'Employee Management',     'Maintain a complete employee directory with role assignments, department structure, and profile management.',         '#6063ee','rgba(96,99,238,0.10)','rgba(96,99,238,0.20)','module-card-cyan',   route('register')],
            ['schedule',      'Attendance Tracking',     'Monitor daily check-ins, leaves, and work hours with automated alerts and exportable attendance reports.',            '#047857','rgba(4,120,87,0.10)', 'rgba(4,120,87,0.20)', 'module-card-green',  route('register')],
            ['payments',      'Payroll',                 'Process salary, deductions, and reimbursements with configurable pay structures and instant payslip generation.',    '#b91c1c','rgba(185,28,28,0.10)','rgba(185,28,28,0.20)','module-card-rose',   route('register')],
            ['insights',      'Performance & Analytics', 'Visualize hiring funnels, employee KPIs, resume quality scores, and platform-wide HR metrics in real time.',        '#4648d4','rgba(70,72,212,0.10)','rgba(70,72,212,0.20)','module-card-violet', route('register')],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($modules as $mod)
            <a href="{{ $mod[7] }}" class="module-card {{ $mod[6] }} group block">
                <div class="module-icon w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background:{{ $mod[4] }}; border:1px solid {{ $mod[5] }};">
                    <span class="material-symbols-outlined text-[22px]" style="color:{{ $mod[3] }}; font-variation-settings:'FILL' 1;">{{ $mod[0] }}</span>
                </div>
                <h3 class="text-[16px] font-bold mb-2" style="color:var(--text-primary); font-family:'Plus Jakarta Sans',sans-serif;">{{ $mod[1] }}</h3>
                <p class="text-[13px] leading-relaxed mb-5" style="color:var(--text-muted);">{{ $mod[2] }}</p>
                <div class="flex items-center gap-1.5 text-[12px] font-semibold" style="color:{{ $mod[3] }};">
                    <span>Explore module</span>
                    <span class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-1">arrow_forward</span>
            </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ================================================================
     FOR CANDIDATES — USER JOURNEY
     ================================================================ --}}
<section id="candidates" class="py-24 section-bg-base">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left: steps --}}
            <div>
                <span class="section-label mb-6 inline-flex">
                    <span class="material-symbols-outlined text-[14px]">person_search</span>
                    For Job Seekers
                </span>
                <h2 class="text-[38px] font-extrabold tracking-tight mt-4 mb-4" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Your Path to the<br><span class="gradient-text-violet">Perfect Job</span></h2>
                <p class="text-[15px] leading-relaxed mb-10" style="color:var(--text-muted);">{{ config('app.name') }} guides you from resume to offer letter with AI at every step.</p>

                <div class="space-y-6">
                    @foreach ([
                        ['01','upload_file',  'Upload Your Resume',       'Drop your PDF or Word resume. Our AI parses it in seconds, extracting skills, experience, and education.', '#4648d4','rgba(70,72,212,0.10)','rgba(70,72,212,0.22)'],
                        ['02','analytics',    'Get AI-Powered Analysis',  'Receive an instant ATS compatibility score, skill gap report, and personalised improvement recommendations.', '#6063ee','rgba(96,99,238,0.10)','rgba(96,99,238,0.22)'],
                        ['03','auto_awesome', 'Match with Top Jobs',      'Get matched to roles that fit your profile. Apply with one click and track every application in real time.', '#047857','rgba(4,120,87,0.10)','rgba(4,120,87,0.22)'],
                    ] as $step)
                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:{{ $step[5] }}; border:1px solid {{ $step[6] }};">
                            <span class="material-symbols-outlined text-[20px]" style="color:{{ $step[4] }}; font-variation-settings:'FILL' 1;">{{ $step[1] }}</span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest mb-1" style="color:{{ $step[4] }};">Step {{ $step[0] }}</p>
                            <h4 class="text-[16px] font-bold mb-1" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-primary);">{{ $step[2] }}</h4>
                            <p class="text-[13px] leading-relaxed" style="color:var(--text-muted);">{{ $step[3] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-10 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="btn-primary py-3 px-7 text-[14px] font-semibold">
                        <span class="material-symbols-outlined">rocket_launch</span>
                        Start for Free
                    </a>
                    <a href="{{ route('login') }}" class="btn-ghost py-3 px-7 text-[14px]">Sign In</a>
                </div>
            </div>

            {{-- Right: resume score card --}}
            <div class="glass-card p-8 relative overflow-hidden">
                <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full pointer-events-none" style="background:rgba(70,72,212,0.06); filter:blur(32px);"></div>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:var(--brand-gradient);">
                        <span class="material-symbols-outlined text-white text-[18px]" style="font-variation-settings:'FILL' 1;">description</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-widest" style="color:var(--brand-secondary);">Resume Analysis</p>
                        <p class="text-[15px] font-bold" style="color:var(--text-primary); font-family:'Plus Jakarta Sans',sans-serif;">Full Stack Developer</p>
                    </div>
                    <span class="ml-auto badge-ai">AI</span>
                </div>

                <div class="flex items-center gap-6 rounded-xl p-5 mb-5" style="background:var(--bg-surface-low); border:1px solid var(--border-subtle);">
                    <div class="relative w-20 h-20 flex-shrink-0">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="34" fill="none" stroke="var(--bg-surface-high)" stroke-width="8"/>
                            <circle cx="40" cy="40" r="34" fill="none" stroke="#4648d4" stroke-width="8" stroke-dasharray="213.6" stroke-dashoffset="27" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-[18px] font-extrabold" style="color:#4648d4;">87</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-[14px] font-semibold mb-1" style="color:var(--text-primary);">AI Score: 87/100</p>
                        <p class="text-[12px] mb-3" style="color:var(--text-muted);">Strong match for senior roles</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach (['React', 'Node.js', 'AWS', '+8 more'] as $skill)
                            <span class="skill-tag text-[11px] px-2 py-0.5 rounded-md">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-[12px] font-semibold uppercase tracking-wide" style="color:var(--text-muted);">Skill Match Breakdown</p>
                    @foreach ([['Communication',92,'#047857'],['Technical Skills',87,'#4648d4'],['Leadership',74,'#b45309'],['ATS Keywords',68,'#6063ee']] as $sk)
                    <div class="flex items-center gap-3">
                        <span class="text-[12px] w-32 flex-shrink-0" style="color:var(--text-secondary);">{{ $sk[0] }}</span>
                        <div class="flex-1 h-2 rounded-full" style="background:var(--bg-surface-high);">
                            <div class="h-full rounded-full" style="width:{{ $sk[1] }}%; background:{{ $sk[2] }};"></div>
                        </div>
                        <span class="text-[12px] font-semibold w-8 text-right" style="color:{{ $sk[2] }};">{{ $sk[1] }}%</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-4 flex items-center justify-between" style="border-top:1px solid var(--border-subtle);">
                    <span class="text-[12px]" style="color:var(--text-muted);">3 improvement suggestions</span>
                    <span class="text-[12px] font-semibold" style="color:var(--brand-primary);">View Full Report →</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================================================================
     FOR EMPLOYERS — USER JOURNEY
     ================================================================ --}}
<section id="employers" class="py-24 section-bg-low">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left: AI hiring card --}}
            <div class="glass-card p-8 relative overflow-hidden order-2 lg:order-1">
                <div class="absolute -bottom-8 -left-8 w-40 h-40 rounded-full pointer-events-none" style="background:rgba(96,99,238,0.06); filter:blur(32px);"></div>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(96,99,238,0.12); border:1px solid rgba(96,99,238,0.25);">
                        <span class="material-symbols-outlined text-[18px]" style="color:#6063ee; font-variation-settings:'FILL' 1;">model_training</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-widest" style="color:#6063ee;">AI Hiring</p>
                        <p class="text-[15px] font-bold" style="color:var(--text-primary); font-family:'Plus Jakarta Sans',sans-serif;">Senior React Developer</p>
                    </div>
                    <span class="ml-auto section-label section-label-cyan text-[10px] px-2 py-1">Active</span>
                </div>

                <div class="space-y-3 mb-5">
                    <p class="text-[12px] font-semibold uppercase tracking-wide" style="color:var(--text-muted);">Top AI Matches</p>
                    @foreach ([
                        ['Alex K.',  'Senior Dev · 7 yrs',  96, '#047857'],
                        ['Maria R.', 'Full Stack · 5 yrs',  88, '#4648d4'],
                        ['Sam L.',   'React Specialist',    81, '#6063ee'],
                    ] as $c)
                    <div class="flex items-center gap-3 p-3.5 rounded-xl" style="background:var(--bg-surface-low); border:1px solid var(--border-subtle);">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background:var(--brand-gradient);">
                            <span class="text-white font-bold text-[11px]">{{ substr($c[0],0,2) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-semibold" style="color:var(--text-primary);">{{ $c[0] }}</p>
                            <p class="text-[11px]" style="color:var(--text-muted);">{{ $c[1] }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[15px] font-extrabold" style="color:{{ $c[3] }};">{{ $c[2] }}%</span>
                            <div class="progress-bar-track w-14 mt-1">
                                <div class="progress-bar-fill" style="width:{{ $c[2] }}%; background:{{ $c[3] }};"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-3 gap-3 rounded-xl p-4" style="background:var(--bg-surface-low); border:1px solid var(--border-subtle);">
                    @foreach ([
                        [$stats['candidates'] > 0 ? $stats['candidates'] : '—', 'Screened',   '#4648d4'],
                        ['3', 'Shortlisted', '#047857'],
                        ['1', 'Invited',     '#6063ee'],
                    ] as $sv)
                    <div class="text-center">
                        <p class="text-[20px] font-extrabold" style="color:{{ $sv[2] }}; font-family:'Plus Jakarta Sans',sans-serif;">{{ $sv[0] }}</p>
                        <p class="text-[11px]" style="color:var(--text-muted);">{{ $sv[1] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: steps --}}
            <div class="order-1 lg:order-2">
                <span class="section-label section-label-cyan mb-6 inline-flex">
                    <span class="material-symbols-outlined text-[14px]">corporate_fare</span>
                    For Employers & HR Teams
                </span>
                <h2 class="text-[38px] font-extrabold tracking-tight mt-4 mb-4" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Hire Smarter,<br><span class="gradient-text-cyan">Not Harder</span></h2>
                <p class="text-[15px] leading-relaxed mb-10" style="color:var(--text-muted);">Let AI do the heavy lifting. Go from job description to shortlisted candidates in minutes.</p>

                <div class="space-y-6">
                    @foreach ([
                        ['01','post_add',      'Post Job or Upload JD',  'Publish a job listing or upload your job description file. Our AI immediately starts extracting requirements.', '#6063ee','rgba(96,99,238,0.10)','rgba(96,99,238,0.22)'],
                        ['02','model_training','AI Ranks Candidates',    'The AI scans your full candidate pool and ranks matches by compatibility, experience, and cultural fit.',       '#4648d4','rgba(70,72,212,0.10)','rgba(70,72,212,0.22)'],
                        ['03','handshake',     'Connect & Schedule',     'Review ranked profiles, start messaging, schedule interviews, and track applicant progress — all in one dashboard.','#047857','rgba(4,120,87,0.10)','rgba(4,120,87,0.22)'],
                    ] as $step)
                    <div class="flex items-start gap-5">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:{{ $step[5] }}; border:1px solid {{ $step[6] }};">
                            <span class="material-symbols-outlined text-[20px]" style="color:{{ $step[4] }}; font-variation-settings:'FILL' 1;">{{ $step[1] }}</span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest mb-1" style="color:{{ $step[4] }};">Step {{ $step[0] }}</p>
                            <h4 class="text-[16px] font-bold mb-1" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-primary);">{{ $step[2] }}</h4>
                            <p class="text-[13px] leading-relaxed" style="color:var(--text-muted);">{{ $step[3] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-10 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}?role=hr" class="btn-primary py-3 px-7 text-[14px] font-semibold">
                        <span class="material-symbols-outlined">business_center</span>
                        Start Hiring
                    </a>
                    <a href="{{ route('login') }}" class="btn-ghost py-3 px-7 text-[14px]">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================================================================
     FEATURED JOBS (DB-driven)
     ================================================================ --}}
<section id="jobs" class="py-24 section-bg-base">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-12">
            <div>
                <span class="section-label section-label-green mb-4 inline-flex">
                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">work</span>
                    Live Opportunities
                </span>
                <h2 class="text-[38px] font-extrabold tracking-tight mt-4" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Latest Job Openings</h2>
                <p class="text-[15px] mt-2" style="color:var(--text-muted);">
                    @if($stats['activeJobs'] > 0)
                        {{ number_format($stats['activeJobs']) }} active {{ Str::plural('position', $stats['activeJobs']) }} across all departments.
                    @else
                        Be the first to post a position on {{ config('app.name') }}.
                    @endif
                </p>
            </div>
            @if($featuredJobs->isNotEmpty())
            <a href="{{ route('register') }}" class="btn-ghost py-2.5 px-5 text-[13px] whitespace-nowrap flex-shrink-0">
                View All Jobs
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
            @endif
        </div>

        @if($featuredJobs->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($featuredJobs as $job)
                @php
                    $typeMap = [
                        'full-time'  => 'job-type-full-time',
                        'part-time'  => 'job-type-part-time',
                        'contract'   => 'job-type-contract',
                        'remote'     => 'job-type-remote',
                        'internship' => 'job-type-internship',
                    ];
                    $typeClass = $typeMap[strtolower($job->job_type ?? '')] ?? 'job-type-default';
                @endphp
                <a href="{{ route('register') }}" class="job-card group">
                    <div class="flex items-start justify-between gap-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--brand-gradient);">
                            <span class="material-symbols-outlined text-white text-[18px]">work</span>
                        </div>
                        @if($job->job_type)
                        <span class="job-type-badge {{ $typeClass }}">{{ ucwords(str_replace('-', ' ', $job->job_type)) }}</span>
                        @endif
                    </div>

                    <div class="flex-1">
                        <h4 class="text-[15px] font-bold mb-1 transition-colors group-hover:text-secondary" style="color:var(--text-primary); font-family:'Plus Jakarta Sans',sans-serif;">{{ $job->title }}</h4>
                        <p class="text-[13px]" style="color:var(--text-muted);">{{ $job->company_name ?? 'Company' }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if($job->location)
                        <span class="inline-flex items-center gap-1 text-[12px]" style="color:var(--text-muted);">
                            <span class="material-symbols-outlined text-[14px]">location_on</span>{{ $job->location }}
                        </span>
                        @endif
                        @if($job->experience_required)
                        <span class="inline-flex items-center gap-1 text-[12px]" style="color:var(--text-muted);">
                            <span class="material-symbols-outlined text-[14px]">schedule</span>{{ $job->experience_required }}
                        </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-3 mt-auto" style="border-top:1px solid var(--border-subtle);">
                        <span class="text-[11px]" style="color:var(--text-muted);">{{ $job->created_at->diffForHumans() }}</span>
                        <span class="text-[12px] font-semibold flex items-center gap-1 group-hover:gap-2 transition-all" style="color:var(--brand-primary);">
                            Apply Now
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <div class="no-jobs-cta">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:rgba(4,120,87,0.10); border:1px solid rgba(4,120,87,0.22);">
                    <span class="material-symbols-outlined text-[28px]" style="color:#047857; font-variation-settings:'FILL' 1;">work_outline</span>
                </div>
                <h3 class="text-[20px] font-bold mb-2" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">No Active Jobs Yet</h3>
                <p class="text-[14px] mb-6 max-w-md mx-auto" style="color:var(--text-muted);">Be the first employer to post a job on {{ config('app.name') }} and connect with thousands of qualified candidates.</p>
                <a href="{{ route('register') }}?role=hr" class="btn-primary py-3 px-7 text-[14px] font-semibold inline-flex">
                    <span class="material-symbols-outlined">add_circle</span>
                    Post the First Job
                </a>
            </div>
        @endif
    </div>
</section>

{{-- ================================================================
     PLATFORM FEATURES
     ================================================================ --}}
<section id="workflow" class="py-24 section-bg-low">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="section-label section-label-cyan mb-6 inline-flex">
                    <span class="material-symbols-outlined text-[14px]">verified</span>
                    Why Leaders Choose Us
                </span>
                <h2 class="text-[38px] font-extrabold tracking-tight mt-4 mb-6" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Engineered for<br>Excellence</h2>
                <p class="text-[15px] leading-relaxed mb-10" style="color:var(--text-muted);">We don't just fill roles — we engineer high-performing teams. Our platform combines deep-learning algorithms with human-centric design.</p>

                <ul class="features-check-list space-y-0">
                    @foreach ([
                        ['Executive Grade Security',   'Enterprise-level data protection and global privacy compliance.',                          '#4648d4'],
                        ['Diversity-First Logic',       'Algorithmic bias neutralisation for a truly equitable hiring process.',                    '#6063ee'],
                        ['Predictive Analytics',        'Forecast performance and cultural alignment before the first interview.',                   '#047857'],
                        ['Real-Time Matching',          'Live candidate-to-job updates as new resumes and listings are published.',                 '#b45309'],
                        ['End-to-End Workforce Suite',  'From job post to payroll — one login, zero integration headaches.',                       '#b91c1c'],
                        ['Python AI Engine',            'Deep-learning resume parser, JD analyser, and ATS optimizer powered by Python & LLMs.',   '#4648d4'],
                    ] as $feat)
                    <li>
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $feat[2] }}12; border:1px solid {{ $feat[2] }}28;">
                            <span class="material-symbols-outlined text-[15px]" style="color:{{ $feat[2] }}; font-variation-settings:'FILL' 1;">check</span>
                        </div>
                        <div>
                            <h4 class="text-[14px] font-semibold mb-0.5" style="color:var(--text-primary);">{{ $feat[0] }}</h4>
                            <p class="text-[13px] leading-relaxed" style="color:var(--text-muted);">{{ $feat[1] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Stats grid --}}
            <div class="grid grid-cols-2 gap-5">
                @php
                $gridStats = [
                    [$stats['candidates']  > 0 ? number_format($stats['candidates']).'+' : '98%',  $stats['candidates']  > 0 ? 'Registered Candidates'  : 'Match Accuracy', 'violet'],
                    [$stats['activeJobs']  > 0 ? number_format($stats['activeJobs']).'+' : '12ms', $stats['activeJobs']  > 0 ? 'Open Positions'          : 'Match Latency',  'cyan'],
                    [$stats['companies']   > 0 ? number_format($stats['companies']).'+' : '94%',   $stats['companies']   > 0 ? 'Companies Using Us'       : 'Retention Rate', 'violet'],
                    [$stats['applications']> 0 ? number_format($stats['applications']).'+' : '500+',$stats['applications']> 0 ? 'Applications Processed'  : 'Companies',       'cyan'],
                ];
                @endphp
                @foreach ($gridStats as $gs)
                <div class="glass-card p-7 text-center">
                    <p class="text-[42px] font-extrabold mb-2 {{ $gs[2] === 'violet' ? 'gradient-text-violet' : 'gradient-text-cyan' }}" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $gs[0] }}</p>
                    <p class="text-[13px]" style="color:var(--text-muted);">{{ $gs[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>
                </div>
</section>

{{-- ================================================================
     HOW IT WORKS
     ================================================================ --}}
<section class="py-24 section-bg-base">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="section-label mb-4 inline-flex">
                <span class="material-symbols-outlined text-[14px]">route</span>
                How it Works
            </span>
            <h2 class="text-[38px] font-extrabold tracking-tight mt-4 mb-4" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Precision Workflow</h2>
            <p class="text-[15px] max-w-2xl mx-auto" style="color:var(--text-muted);">Our three-step AI engine ensures the highest signal-to-noise ratio in executive search and workforce management.</p>
                </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ([
                ['01','Smart Upload',     'upload_file',   'Drop your resume or job description. Our system accepts all formats and begins immediate AI analysis.', '#4648d4','rgba(70,72,212,0.10)','rgba(70,72,212,0.22)'],
                ['02','Semantic Parsing', 'analytics',     'We extract more than keywords. Our AI understands skills, cultural fit, and professional trajectory.',  '#6063ee','rgba(96,99,238,0.10)','rgba(96,99,238,0.22)'],
                ['03','Neural Matching',  'auto_awesome',  'Receive a curated list of matches with detailed compatibility scoring and predictive performance data.',  '#047857','rgba(4,120,87,0.10)','rgba(4,120,87,0.22)'],
            ] as $step)
            <div class="glass-card glass-card-lift p-8 group">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform" style="background:{{ $step[5] }}; border:1px solid {{ $step[6] }};">
                    <span class="material-symbols-outlined text-[26px]" style="color:{{ $step[4] }}; font-variation-settings:'FILL' 1;">{{ $step[2] }}</span>
                </div>
                <p class="text-[11px] font-bold uppercase tracking-widest mb-3" style="color:{{ $step[4] }};">{{ $step[0] }}</p>
                <h3 class="text-[20px] font-bold mb-3" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-primary);">{{ $step[1] }}</h3>
                <p class="text-[13px] leading-relaxed" style="color:var(--text-muted);">{{ $step[3] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================================================================
     TESTIMONIALS
     ================================================================ --}}
<section class="py-24 section-bg-low">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="section-label mb-4 inline-flex">
                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">star</span>
                Testimonials
            </span>
            <h2 class="text-[38px] font-extrabold tracking-tight mt-4" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Trusted by Professionals</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ([
                ['"Placing our CTO in under two weeks was unimaginable before ' . config('app.name') . '. The AI matching is genuinely remarkable."', 'Sarah K.', 'Chief People Officer, TechFlow', 'SK'],
                ['"The resume parsing accuracy eliminated 80% of our manual screening work. ROI was visible within the first month."',  'Marcus R.', 'Head of Talent, Quantix AI',    'MR'],
                ['"I went from an unoptimised resume to multiple interviews in 3 days. The ATS checker and AI insights are transformative."', 'Priya M.', 'Senior Software Engineer', 'PM'],
            ] as $t)
            <div class="glass-card glass-card-lift p-7 flex flex-col gap-5">
                <div class="flex gap-0.5">
                    @for($s = 0; $s < 5; $s++)
                    <span class="material-symbols-outlined text-[16px]" style="color:#b45309; font-variation-settings:'FILL' 1;">star</span>
                    @endfor
                </div>
                <p class="text-[14px] leading-relaxed flex-1 italic" style="color:var(--text-secondary);">{{ $t[0] }}</p>
                <div class="flex items-center gap-3 pt-4" style="border-top:1px solid var(--border-subtle);">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background:var(--brand-gradient);">
                        <span class="text-white font-bold text-[12px]">{{ $t[3] }}</span>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold" style="color:var(--text-primary);">{{ $t[1] }}</p>
                        <p class="text-[11px]" style="color:var(--text-muted);">{{ $t[2] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================================================================
     CTA — brand gradient section
     ================================================================ --}}
<section class="py-24 relative overflow-hidden" style="background: var(--brand-gradient);">
    <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse 70% 60% at 50% 50%, rgba(255,255,255,0.10) 0%, transparent 60%);"></div>

    <div class="relative z-10 max-w-7xl mx-auto text-center px-6 lg:px-8">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-6" style="background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.30);">
            <span class="material-symbols-outlined text-white text-[14px]">rocket_launch</span>
            <span class="text-[11px] font-bold uppercase tracking-widest text-white">Start Today</span>
        </div>

        <h2 class="text-[46px] font-extrabold text-white tracking-tight mb-6" style="font-family:'Plus Jakarta Sans',sans-serif;">
            Ready to Transform<br>Your Hiring Process?
        </h2>
        <p class="text-[17px] mb-10 leading-relaxed max-w-2xl mx-auto" style="color:rgba(255,255,255,0.84);">Join thousands of HR professionals and job seekers using {{ config('app.name') }} to accelerate careers and build high-performing teams.</p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-10">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-content gap-2 py-4 px-10 text-[16px] font-semibold rounded-xl transition-all" style="background:#ffffff; color:var(--brand-primary); box-shadow:0 8px 24px rgba(15,23,42,0.12);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 12px 30px rgba(15,23,42,0.18)'" onmouseout="this.style.transform=''; this.style.boxShadow='0 8px 24px rgba(15,23,42,0.12)'">
                <span class="material-symbols-outlined">rocket_launch</span>
                Get Started Free
            </a>
            <a href="{{ route('register') }}?role=hr" class="inline-flex items-center justify-center gap-2 py-4 px-10 text-[16px] font-semibold text-white rounded-xl transition-all" style="background:rgba(255,255,255,0.14); border:1.5px solid rgba(255,255,255,0.36);" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.14)'">
                <span class="material-symbols-outlined">business_center</span>
                Post a Job
            </a>
        </div>

        <div class="flex flex-wrap justify-center gap-3">
            @foreach ([
                ['Resume Tester','description'],
                ['ATS Checker',  'analytics'],
                ['AI Hiring',    'model_training'],
                ['Job Board',    'work'],
                ['Analytics',    'insights'],
            ] as $link)
            <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-[12px] font-medium transition-all" style="color:rgba(255,255,255,0.78); border:1px solid rgba(255,255,255,0.26); background:rgba(255,255,255,0.09);" onmouseover="this.style.color='#fff'; this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.color='rgba(255,255,255,0.78)'; this.style.background='rgba(255,255,255,0.09)'">
                <span class="material-symbols-outlined text-[14px]">{{ $link[1] }}</span>{{ $link[0] }}
            </a>
            @endforeach
        </div>
    </div>
</section>

@include('partials.nav.public-footer')
@endsection

@push('scripts')
<script>
(function () {
    var stats = document.querySelectorAll('.stat-number');
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var raw = el.getAttribute('data-value') || '';
            var num = parseInt(raw.replace(/[^0-9]/g, ''), 10);
            if (!num) return;
            var hasPlusSign = raw.includes('+');
            var suffix = raw.replace(/[0-9+]/g, '').trim();
            var start = 0;
            var step = Math.max(1, Math.ceil(num / (1400 / 16)));
            el.textContent = '0' + (hasPlusSign ? '+' : '') + suffix;
            var timer = setInterval(function () {
                start += step;
                if (start >= num) { start = num; clearInterval(timer); }
                el.textContent = start.toLocaleString() + (hasPlusSign ? '+' : '') + suffix;
            }, 16);
            observer.unobserve(el);
        });
    }, { threshold: 0.4 });
    stats.forEach(function (el) { observer.observe(el); });
})();
</script>
@endpush
