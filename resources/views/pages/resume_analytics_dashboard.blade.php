@extends('layouts.candidate', ['activeNav' => 'analytics'])

@section('title', 'Resume Analytics')

@section('body-class', 'bg-background text-on-surface font-body-md overflow-x-hidden min-h-screen')

@section('page-css', 'resume_analytics_dashboard.css')

@section('tailwind-config', 'tailwind-config-resume-analytics.js')

@section('page-main')
<div class="space-y-8">

{{-- ================================================================ --}}
{{-- STATE: NONE — Upload prompt                                       --}}
{{-- ================================================================ --}}
@if($resumeStatus === 'none')

<section class="flex flex-col items-center justify-center py-16">
    <div class="glass-card p-10 rounded-2xl max-w-xl w-full text-center">
        <div class="w-20 h-20 bg-secondary/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-secondary" style="font-size:40px">description</span>
        </div>
        <h2 class="font-headline-lg text-2xl font-bold mb-3">Get Your AI Resume Analysis</h2>
        <p class="text-sm text-on-surface-variant mb-8 leading-relaxed">
            Upload your resume and our AI will analyze your skills, career trajectory, job fit score, and provide
            personalized tips to help you land your next role.
        </p>

        <form id="upload-form" action="{{ route('user.resume.analytics.upload') }}" method="POST"
              enctype="multipart/form-data">
            @csrf
            <div id="drop-zone"
                 class="border-2 border-dashed border-outline-variant hover:border-secondary rounded-xl p-10 cursor-pointer transition-colors mb-4"
                 onclick="document.getElementById('resume-input').click()"
                 ondragover="event.preventDefault(); this.classList.add('border-secondary')"
                 ondragleave="this.classList.remove('border-secondary')"
                 ondrop="handleDrop(event)">
                <span class="material-symbols-outlined text-secondary" style="font-size:36px">upload_file</span>
                <p class="font-semibold mt-3 text-sm">Click or drag &amp; drop to upload</p>
                <p class="text-xs text-on-surface-variant mt-1">PDF or DOCX · Max 5 MB</p>
                <input type="file" id="resume-input" name="resume" accept=".pdf,.docx" class="hidden"
                       onchange="onFileSelected(this)">
            </div>

            <div id="file-preview" class="hidden mb-4 p-3 rounded-lg bg-secondary/10 flex items-center gap-3 text-left">
                <span class="material-symbols-outlined text-secondary">description</span>
                <div class="flex-1 min-w-0">
                    <p id="file-name" class="text-sm font-medium truncate"></p>
                    <p id="file-size" class="text-xs text-on-surface-variant"></p>
                </div>
                <button type="button" onclick="clearFile()" class="text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div id="upload-error" class="hidden text-red-400 text-sm mb-3 p-3 rounded-lg bg-red-500/10 text-left"></div>

            <button type="submit" id="upload-btn"
                    class="hidden w-full bg-gradient-to-r from-secondary to-purple-600 text-white px-6 py-3 rounded-xl font-semibold text-sm shadow-md hover:opacity-90 transition-opacity disabled:opacity-60">
                <span id="btn-text" class="flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                    Analyze My Resume
                </span>
            </button>
        </form>

        <div class="mt-8 grid grid-cols-3 gap-4 text-center">
            <div class="p-3 rounded-xl bg-surface-container">
                <span class="material-symbols-outlined text-secondary text-[22px]">psychology</span>
                <p class="text-xs mt-1 text-on-surface-variant">AI Scoring</p>
            </div>
            <div class="p-3 rounded-xl bg-surface-container">
                <span class="material-symbols-outlined text-secondary text-[22px]">insights</span>
                <p class="text-xs mt-1 text-on-surface-variant">Skill Gap Analysis</p>
            </div>
            <div class="p-3 rounded-xl bg-surface-container">
                <span class="material-symbols-outlined text-secondary text-[22px]">work_history</span>
                <p class="text-xs mt-1 text-on-surface-variant">Job Fit Score</p>
            </div>
        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- STATE: PROCESSING                                                 --}}
{{-- ================================================================ --}}
@elseif($resumeStatus === 'processing')

<section class="flex flex-col items-center justify-center py-16">
    <div class="glass-card p-10 rounded-2xl max-w-lg w-full text-center">
        <div class="w-20 h-20 bg-secondary/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-secondary animate-spin" style="font-size:40px">sync</span>
        </div>
        <h2 class="text-2xl font-bold mb-2">AI Analysis in Progress</h2>
        <p class="text-sm text-on-surface-variant mb-1">
            Analyzing <strong class="text-on-surface">{{ $latestResume->file_name }}</strong>
        </p>
        <p class="text-xs text-on-surface-variant mb-8">
            This usually takes 15–30 seconds. The page refreshes automatically.
        </p>

        <div class="flex justify-center gap-2 mb-8">
            <div class="w-2.5 h-2.5 bg-secondary rounded-full animate-bounce" style="animation-delay:0s"></div>
            <div class="w-2.5 h-2.5 bg-secondary rounded-full animate-bounce" style="animation-delay:0.18s"></div>
            <div class="w-2.5 h-2.5 bg-secondary rounded-full animate-bounce" style="animation-delay:0.36s"></div>
        </div>

        <div class="space-y-3 text-left text-sm">
            <div class="flex items-center gap-3 p-3 rounded-lg bg-secondary/10">
                <span class="material-symbols-outlined text-secondary text-[18px]">check_circle</span>
                <span>Resume uploaded and saved</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg bg-secondary/5 animate-pulse">
                <span class="material-symbols-outlined text-secondary text-[18px]">hourglass_top</span>
                <span>Running AI analysis with OpenAI…</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg bg-surface-container opacity-40">
                <span class="material-symbols-outlined text-on-surface-variant text-[18px]">dashboard</span>
                <span>Building your analytics dashboard</span>
            </div>
        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- STATE: FAILED                                                     --}}
{{-- ================================================================ --}}
@elseif($resumeStatus === 'failed')

<section class="flex flex-col items-center justify-center py-16">
    <div class="glass-card p-10 rounded-2xl max-w-lg w-full text-center">
        <div class="w-20 h-20 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-red-400" style="font-size:40px">error_outline</span>
        </div>
        <h2 class="text-2xl font-bold mb-3">Analysis Failed</h2>
        <p class="text-sm text-on-surface-variant mb-6 leading-relaxed">
            {{ $latestResume?->error_message ?? 'An unexpected error occurred while analyzing your resume.' }}
        </p>
        <div class="flex flex-col gap-3">
            <form action="{{ route('user.resume.analytics.reanalyze', $latestResume->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full bg-gradient-to-r from-secondary to-purple-600 text-white px-6 py-3 rounded-xl font-semibold text-sm shadow-md hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                    Try Again
                </button>
            </form>
            <form action="{{ route('user.resume.analytics.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" id="upload-new-failed" name="resume" accept=".pdf,.docx" class="hidden"
                       onchange="this.form.submit()">
                <button type="button" onclick="document.getElementById('upload-new-failed').click()"
                        class="w-full bg-surface border border-outline-variant px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-surface-container-high transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    Upload a Different Resume
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ================================================================ --}}
{{-- STATE: COMPLETED — Full analytics dashboard                       --}}
{{-- ================================================================ --}}
@else
@php
    $sg              = $analytics->skill_gap_analysis ?? [];
    $sgLabels        = $sg['labels'] ?? [];
    $sgCandidate     = $sg['candidate_scores'] ?? [];
    $sgBenchmark     = $sg['benchmark_scores'] ?? [];
    $careerGrowth    = $analytics->career_growth ?? [];
    $education       = $analytics->education ?? [];
    $nlp             = $analytics->nlp_analysis ?? [];
    $softSkills      = $analytics->soft_skills ?? [];
    $skills          = $analytics->skills ?? [];
    $missingSkills   = $analytics->missing_skills ?? [];
    $strengths       = $analytics->strengths ?? [];
    $weaknesses      = $analytics->weaknesses ?? [];
    $improvements    = $analytics->resume_improvements ?? [];
    $recommendedJobs = $recommendedJobs ?? collect();
    $appliedJobIds   = $appliedJobIds ?? [];
    $aiScore         = $analytics->ai_score ?? 0;

    $levelDot = [
        'entry'     => '#64748b',
        'mid'       => '#3b82f6',
        'senior'    => '#8b5cf6',
        'lead'      => '#a855f7',
        'executive' => '#f59e0b',
    ];
    $priorityStyle = [
        'high'   => ['border' => 'border-red-500/30',    'badge' => 'bg-red-500/20 text-red-400',    'icon' => 'priority_high'],
        'medium' => ['border' => 'border-amber-500/30',  'badge' => 'bg-amber-500/20 text-amber-400','icon' => 'warning'],
        'low'    => ['border' => 'border-green-500/30',  'badge' => 'bg-green-500/20 text-green-400','icon' => 'info'],
    ];
@endphp

{{-- ── Profile Header ────────────────────────────────────────────── --}}
<section class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
    <div class="flex items-start gap-6">
        {{-- Avatar + AI Score --}}
        <div class="relative flex-shrink-0">
            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-secondary/30 to-purple-900/40
                        border-2 border-secondary/40 flex items-center justify-center shadow-lg">
                <span class="material-symbols-outlined text-secondary" style="font-size:44px">person</span>
            </div>
            <div class="absolute -bottom-3 -right-3 bg-secondary text-white px-2.5 py-1.5 rounded-xl
                        shadow-lg flex items-center gap-1 text-center">
                <span class="text-[18px] font-extrabold leading-none">{{ $aiScore }}</span>
                <span class="text-[8px] font-bold leading-tight uppercase">AI<br>Score</span>
            </div>
        </div>

        {{-- Name / Role / Contact --}}
        <div class="space-y-1 pt-1">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-bold leading-tight">
                    {{ $analytics->candidate_name ?: ($candidate?->full_name ?? auth()->user()->name) }}
                </h2>
                @if($aiScore >= 80)
                    <span class="bg-secondary/10 text-secondary px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest">
                        Premium Talent
                    </span>
                @elseif($aiScore >= 60)
                    <span class="bg-blue-500/10 text-blue-400 px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest">
                        Strong Candidate
                    </span>
                @endif
            </div>
            <p class="text-sm text-on-surface-variant">
                {{ $analytics->current_role ?: ($candidate?->current_title ?? 'Professional') }}
                @if($analytics->total_experience_years)
                    &nbsp;·&nbsp;{{ number_format($analytics->total_experience_years, 0) }}+ yrs experience
                @endif
            </p>
            <div class="flex flex-wrap gap-4 pt-1">
                @if($analytics->email)
                    <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                        <span class="material-symbols-outlined text-[15px]">mail</span>{{ $analytics->email }}
                    </span>
                @endif
                @if($analytics->phone)
                    <span class="flex items-center gap-1 text-xs text-on-surface-variant">
                        <span class="material-symbols-outlined text-[15px]">phone</span>{{ $analytics->phone }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats + Actions --}}
    <div class="flex flex-col gap-4 items-start md:items-end">
        <div class="flex flex-wrap gap-3">
            <div class="glass-card px-4 py-2 text-center min-w-[76px]">
                <p class="text-[20px] font-extrabold text-secondary">{{ $analytics->top_match_percentage }}%</p>
                <p class="text-[10px] text-on-surface-variant">Top Match</p>
            </div>
            <div class="glass-card px-4 py-2 text-center min-w-[76px]">
                <p class="text-[20px] font-extrabold text-on-surface">{{ $analytics->skill_count }}</p>
                <p class="text-[10px] text-on-surface-variant">Skills</p>
            </div>
            <div class="glass-card px-4 py-2 text-center min-w-[76px]">
                <p class="text-[20px] font-extrabold text-on-surface">{{ count($strengths) }}</p>
                <p class="text-[10px] text-on-surface-variant">Strengths</p>
            </div>
            <div class="glass-card px-4 py-2 text-center min-w-[76px]">
                <p class="text-[20px] font-extrabold text-on-surface">{{ $analytics->application_count }}</p>
                <p class="text-[10px] text-on-surface-variant">Applications</p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <form action="{{ route('user.resume.analytics.reanalyze', $latestResume->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="bg-surface border border-outline-variant px-4 py-2 rounded-xl text-xs font-medium
                               hover:bg-surface-container-high transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px]">refresh</span>Re-analyze
                </button>
            </form>
            <form action="{{ route('user.resume.analytics.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" id="upload-new-input" name="resume" accept=".pdf,.docx" class="hidden"
                       onchange="this.form.submit()">
                <button type="button" onclick="document.getElementById('upload-new-input').click()"
                        class="bg-gradient-to-r from-secondary to-purple-600 text-white px-4 py-2 rounded-xl
                               text-xs font-semibold shadow-md hover:opacity-90 transition-opacity flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px]">upload</span>New Resume
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ── Bento Grid ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-12 gap-6">

    {{-- ── Skill Gap Analysis ────────────────────────────────────────── --}}
    <div class="col-span-12 lg:col-span-5 glass-card p-6 rounded-xl shadow-sm">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="font-semibold text-base">Skill Gap Analysis</h3>
                <p class="text-[11px] text-on-surface-variant uppercase tracking-wider mt-0.5">
                    You vs. Industry Benchmark
                </p>
            </div>
            <span class="material-symbols-outlined text-secondary text-[20px]">radar</span>
        </div>

        @if(count($sgLabels) > 0)
            <div class="space-y-4">
                @foreach($sgLabels as $i => $label)
                    @php
                        $cScore = (int)($sgCandidate[$i] ?? 0);
                        $bScore = (int)($sgBenchmark[$i] ?? 0);
                        $gap    = $cScore - $bScore;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-xs font-medium">{{ $label }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-secondary">{{ $cScore }}%</span>
                                <span class="text-xs text-on-surface-variant">/ {{ $bScore }}%</span>
                                @if($gap > 0)
                                    <span class="text-[10px] text-green-400 font-bold">+{{ $gap }}</span>
                                @elseif($gap < 0)
                                    <span class="text-[10px] text-red-400 font-bold">{{ $gap }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="relative h-2.5 bg-surface-container rounded-full overflow-hidden">
                            {{-- Benchmark (background) --}}
                            <div class="absolute top-0 left-0 h-full rounded-full bg-outline-variant/40"
                                 style="width:{{ $bScore }}%"></div>
                            {{-- Candidate (foreground) --}}
                            <div class="absolute top-0 left-0 h-full rounded-full
                                        {{ $cScore >= $bScore ? 'bg-secondary' : 'bg-amber-500' }}"
                                 style="width:{{ $cScore }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-5 flex gap-4 text-xs text-on-surface-variant">
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-1.5 rounded bg-secondary inline-block"></span>Your Score
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-1.5 rounded bg-outline-variant/40 inline-block"></span>Benchmark
                </span>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center opacity-50">
                <span class="material-symbols-outlined text-[36px] mb-2">bar_chart</span>
                <p class="text-xs">Skill gap data not available</p>
            </div>
        @endif
    </div>

    {{-- ── Career Growth Timeline ─────────────────────────────────────── --}}
    <div class="col-span-12 lg:col-span-7 glass-card p-6 rounded-xl shadow-sm">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="font-semibold text-base">Career Growth Trajectory</h3>
                <p class="text-[11px] text-on-surface-variant uppercase tracking-wider mt-0.5">
                    Work History &amp; Progression
                </p>
            </div>
            <span class="material-symbols-outlined text-secondary text-[20px]">trending_up</span>
        </div>

        @if(count($careerGrowth) > 0)
            <div class="relative space-y-6 before:content-[''] before:absolute before:left-[11px]
                        before:top-2 before:bottom-2 before:w-[2px] before:bg-outline-variant/40">
                @foreach($careerGrowth as $i => $job)
                    @php
                        $dot    = $levelDot[strtolower($job['level'] ?? 'mid')] ?? '#8b5cf6';
                        $isLast = $i === count($careerGrowth) - 1;
                    @endphp
                    <div class="relative pl-9 {{ $isLast ? 'opacity-60' : '' }}">
                        <div class="absolute left-0 top-1.5 w-6 h-6 rounded-full flex items-center justify-center
                                    ring-4 ring-background shadow"
                             style="background-color:{{ $dot }}">
                            @if($i === 0)
                                <span class="material-symbols-outlined text-white text-[13px]">star</span>
                            @else
                                <span class="text-white text-[10px] font-bold">{{ $i + 1 }}</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <h4 class="text-sm font-bold leading-tight">{{ $job['title'] ?? '—' }}</h4>
                                <p class="text-xs text-on-surface-variant mt-0.5">
                                    {{ $job['company'] ?? '' }}
                                    @if(!empty($job['duration'])) · {{ $job['duration'] }} @endif
                                </p>
                            </div>
                            @if(!empty($job['tag']))
                                <span class="flex-shrink-0 text-[9px] font-bold uppercase px-2 py-0.5 rounded
                                             bg-secondary/20 text-secondary">
                                    {{ $job['tag'] }}
                                </span>
                            @endif
                        </div>
                        @if(!empty($job['description']))
                            <p class="mt-1.5 text-xs leading-relaxed text-on-surface-variant/80">
                                {{ Str::limit($job['description'], 140) }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center opacity-50">
                <span class="material-symbols-outlined text-[36px] mb-2">work_history</span>
                <p class="text-xs">Career history not available</p>
            </div>
        @endif
    </div>

    {{-- ── Educational Prestige ───────────────────────────────────────── --}}
    <div class="col-span-12 lg:col-span-4 glass-card p-6 rounded-xl shadow-sm flex flex-col">
        <div class="mb-5">
            <h3 class="font-semibold text-base">Educational Background</h3>
            <p class="text-[11px] text-on-surface-variant uppercase tracking-wider mt-0.5">
                Academic Prestige Profile
            </p>
        </div>

        @if(count($education) > 0)
            <div class="space-y-3 flex-1">
                @foreach($education as $edu)
                    @php $score = (int)($edu['score'] ?? 0); @endphp
                    <div class="flex items-start gap-3 p-3 rounded-xl border border-outline-variant/30 bg-surface-container/50">
                        <div class="w-10 h-10 flex-shrink-0 rounded-lg bg-secondary/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-[20px]">school</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold truncate">{{ $edu['institution'] ?? '—' }}</h4>
                            <p class="text-xs text-on-surface-variant truncate">{{ $edu['degree'] ?? '' }}</p>
                            @if(!empty($edu['prestige_label']))
                                <span class="inline-block mt-1 text-[9px] font-bold uppercase px-2 py-0.5 rounded
                                             bg-secondary/10 text-secondary">
                                    {{ $edu['prestige_label'] }}
                                </span>
                            @endif
                        </div>
                        @if($score > 0)
                            <div class="flex-shrink-0 text-right">
                                <p class="text-sm font-extrabold text-secondary">{{ $score }}</p>
                                <p class="text-[10px] text-on-surface-variant">/100</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center py-8 text-center opacity-50">
                <span class="material-symbols-outlined text-[36px] mb-2">school</span>
                <p class="text-xs">Education data not available</p>
            </div>
        @endif
    </div>

    {{-- ── NLP Analysis + Soft Skills ─────────────────────────────────── --}}
    <div class="col-span-12 lg:col-span-8 glass-card p-6 rounded-xl shadow-sm relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-secondary/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="mb-5">
            <h3 class="font-semibold text-base">NLP Intelligence Analysis</h3>
            <p class="text-[11px] text-on-surface-variant uppercase tracking-wider mt-0.5">
                AI-Extracted Sentiment &amp; Behavioural Traits
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- NLP Bars --}}
            <div class="space-y-5">
                @php
                    $nlpMetrics = [
                        'leadership_sentiment'  => ['label' => 'Leadership Sentiment',   'icon' => 'groups'],
                        'adaptability_score'    => ['label' => 'Adaptability Score',      'icon' => 'sync_alt'],
                        'communication_score'   => ['label' => 'Communication Score',     'icon' => 'chat'],
                        'confidence_score'      => ['label' => 'Confidence Level',        'icon' => 'psychology'],
                    ];
                @endphp
                @foreach($nlpMetrics as $key => $meta)
                    @php $val = (int)($nlp[$key] ?? 0); @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="flex items-center gap-1.5 text-xs font-medium">
                                <span class="material-symbols-outlined text-secondary text-[14px]">{{ $meta['icon'] }}</span>
                                {{ $meta['label'] }}
                            </span>
                            <span class="text-secondary font-bold text-sm">{{ $val }}%</span>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700
                                        {{ $val >= 70 ? 'bg-secondary' : ($val >= 40 ? 'bg-amber-500' : 'bg-red-500') }}"
                                 style="width:{{ $val }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Soft Skills --}}
            <div class="bg-surface-container/50 p-4 rounded-xl border border-outline-variant/20">
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-secondary mb-3">
                    Top Soft Skill Clusters
                </h4>
                @if(count($softSkills) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($softSkills as $skill)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium
                                         bg-secondary/10 text-secondary border border-secondary/20">
                                {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-on-surface-variant opacity-50">No soft skills identified</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── AI Profile Summary ──────────────────────────────────────────── --}}
    @if($analytics->ai_profile_summary)
    <div class="col-span-12 glass-card p-6 rounded-xl shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary text-[22px]">auto_awesome</span>
            </div>
            <div>
                <h3 class="font-semibold text-base mb-2">AI Profile Summary</h3>
                <p class="text-sm leading-relaxed text-on-surface-variant">
                    {{ $analytics->ai_profile_summary }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Resume Improvements ─────────────────────────────────────────── --}}
    <div class="col-span-12 glass-card p-6 rounded-xl shadow-sm">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="font-semibold text-base">Resume Improvements</h3>
                <p class="text-[11px] text-on-surface-variant uppercase tracking-wider mt-0.5">
                    Actionable Suggestions to Stand Out
                </p>
            </div>
            @if(count($improvements) > 0)
                <span class="text-[11px] font-bold text-secondary">{{ count($improvements) }} tips</span>
            @endif
        </div>

        @if(count($improvements) > 0)
            <div class="space-y-3">
                @foreach($improvements as $imp)
                    @php
                        $p  = strtolower($imp['priority'] ?? 'medium');
                        $ps = $priorityStyle[$p] ?? $priorityStyle['medium'];
                    @endphp
                    <div class="p-3 rounded-xl border {{ $ps['border'] }} bg-surface-container/40 flex items-start gap-3">
                        <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5
                                     {{ str_contains($ps['badge'], 'red') ? 'text-red-400' : (str_contains($ps['badge'], 'amber') ? 'text-amber-400' : 'text-green-400') }}">
                            {{ $ps['icon'] }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <h4 class="text-sm font-semibold">{{ $imp['title'] ?? '' }}</h4>
                                <span class="flex-shrink-0 text-[9px] font-bold uppercase px-1.5 py-0.5 rounded {{ $ps['badge'] }}">
                                    {{ ucfirst($p) }}
                                </span>
                            </div>
                            <p class="text-xs text-on-surface-variant leading-relaxed">
                                {{ $imp['description'] ?? '' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center opacity-50">
                <span class="material-symbols-outlined text-[36px] mb-2">task_alt</span>
                <p class="text-xs">No improvement suggestions available</p>
            </div>
        @endif
    </div>

    {{-- ── Skills ──────────────────────────────────────────────────────── --}}
    <div class="col-span-12 md:col-span-6 lg:col-span-3 glass-card p-6 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-sm">Your Skills</h3>
            <span class="text-xs font-bold text-secondary">{{ count($skills) }}</span>
        </div>
        @if(count($skills) > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($skills as $skill)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-secondary/10 text-secondary
                                 border border-secondary/20">{{ $skill }}</span>
                @endforeach
            </div>
        @else
            <p class="text-xs text-on-surface-variant opacity-50">No skills detected</p>
        @endif
    </div>

    {{-- ── Missing Skills ───────────────────────────────────────────────── --}}
    <div class="col-span-12 md:col-span-6 lg:col-span-3 glass-card p-6 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-sm">Skills to Acquire</h3>
            <span class="text-xs font-bold text-amber-400">{{ count($missingSkills) }}</span>
        </div>
        @if(count($missingSkills) > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($missingSkills as $skill)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-500/10 text-amber-400
                                 border border-amber-500/20 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[11px]">add</span>{{ $skill }}
                    </span>
                @endforeach
            </div>
        @else
            <p class="text-xs text-green-400 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">check_circle</span>No skill gaps identified
            </p>
        @endif
    </div>

    {{-- ── Strengths ────────────────────────────────────────────────────── --}}
    <div class="col-span-12 md:col-span-6 lg:col-span-3 glass-card p-6 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-sm">Strengths</h3>
            <span class="material-symbols-outlined text-secondary text-[18px]">thumb_up</span>
        </div>
        @if(count($strengths) > 0)
            <ul class="space-y-2">
                @foreach($strengths as $s)
                    <li class="flex items-start gap-2 text-xs">
                        <span class="material-symbols-outlined text-secondary text-[14px] mt-0.5 flex-shrink-0">check_circle</span>
                        <span>{{ $s }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-xs text-on-surface-variant opacity-50">Not available</p>
        @endif
    </div>

    {{-- ── Weaknesses ───────────────────────────────────────────────────── --}}
    <div class="col-span-12 md:col-span-6 lg:col-span-3 glass-card p-6 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-sm">Areas to Improve</h3>
            <span class="material-symbols-outlined text-amber-400 text-[18px]">trending_up</span>
        </div>
        @if(count($weaknesses) > 0)
            <ul class="space-y-2">
                @foreach($weaknesses as $w)
                    <li class="flex items-start gap-2 text-xs">
                        <span class="material-symbols-outlined text-amber-400 text-[14px] mt-0.5 flex-shrink-0">arrow_forward</span>
                        <span class="text-on-surface-variant">{{ $w }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-xs text-on-surface-variant opacity-50">Not available</p>
        @endif
    </div>

</div>{{-- end bento grid --}}

{{-- ── Recommended Jobs (full width, bottom of page) ─────────────────── --}}
<section class="recommended-jobs-section mt-8 pt-2">
    <div class="glass-card p-6 lg:p-8 rounded-xl shadow-sm">
        <div class="flex items-start justify-between mb-6 gap-3">
            <div>
                <h3 class="font-semibold text-lg">Recommended Jobs</h3>
                <p class="text-[11px] text-on-surface-variant uppercase tracking-wider mt-0.5">
                    Matched from your skills, projects &amp; experience
                </p>
            </div>
            @if($recommendedJobs->count() > 0)
                <a href="{{ route('user.jobs.recommendations') }}"
                   class="text-[11px] font-bold text-secondary hover:underline whitespace-nowrap">
                    View all →
                </a>
            @endif
        </div>

        @if($recommendedJobs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($recommendedJobs as $job)
                    @include('partials.jobs.recommended-job-card', [
                        'job' => $job,
                        'matchScore' => $job->match_score,
                        'matchReason' => $job->match_reason ?? null,
                        'hasApplied' => in_array($job->id, $appliedJobIds, true),
                        'compact' => true,
                    ])
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <span class="material-symbols-outlined text-[40px] text-on-surface-variant mb-3">work_off</span>
                <p class="text-sm text-on-surface-variant mb-3">No matching jobs in our listings yet.</p>
                <p class="text-xs text-on-surface-variant/70 mb-4">Add skills to your profile or check back when new roles are posted.</p>
                <a href="{{ route('user.jobs.recommendations') }}"
                   class="text-xs font-semibold text-secondary hover:underline">Browse all jobs</a>
            </div>
        @endif
    </div>
</section>

@endif{{-- end completed --}}

</div>{{-- end space-y-8 --}}

{{-- ── Footer ─────────────────────────────────────────────────────────────── --}}
<footer class="w-full py-8 mt-12 bg-surface border-t border-outline-variant">
    <div class="flex flex-col md:flex-row justify-between items-center px-8 max-w-7xl mx-auto gap-4">
        <p class="text-xs text-on-surface-variant">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <div class="flex gap-6">
            <a class="text-[11px] uppercase tracking-wider text-on-surface-variant hover:text-secondary transition-colors"
               href="{{ route('landing') }}">Privacy Policy</a>
            <a class="text-[11px] uppercase tracking-wider text-on-surface-variant hover:text-secondary transition-colors"
               href="{{ route('landing') }}">Terms of Service</a>
        </div>
    </div>
</footer>
@endsection

@push('page-scripts')
<script>
(function () {
    // ── Upload form (none state) ───────────────────────────────────────────
    function onFileSelected(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-size').textContent = (file.size / 1024).toFixed(0) + ' KB';
        document.getElementById('file-preview').classList.remove('hidden');
        document.getElementById('upload-btn').classList.remove('hidden');
        document.getElementById('upload-error').classList.add('hidden');
    }

    function clearFile() {
        const input = document.getElementById('resume-input');
        if (input) input.value = '';
        document.getElementById('file-preview').classList.add('hidden');
        document.getElementById('upload-btn').classList.add('hidden');
    }

    function handleDrop(event) {
        event.preventDefault();
        document.getElementById('drop-zone')?.classList.remove('border-secondary');
        const file = event.dataTransfer.files[0];
        if (!file) return;
        const input = document.getElementById('resume-input');
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        onFileSelected(input);
    }

    // AJAX upload
    const form = document.getElementById('upload-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('upload-btn');
            const btnText = document.getElementById('btn-text');
            const errDiv = document.getElementById('upload-error');
            btn.disabled = true;
            btnText.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">sync</span> Uploading…';

            const data = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: data,
            })
            .then(r => r.json())
            .then(json => {
                if (json.success) {
                    window.location.reload();
                } else {
                    errDiv.textContent = json.message || 'Upload failed. Please try again.';
                    errDiv.classList.remove('hidden');
                    btn.disabled = false;
                    btnText.innerHTML = '<span class="material-symbols-outlined text-[18px]">auto_awesome</span> Analyze My Resume';
                }
            })
            .catch(() => {
                errDiv.textContent = 'Network error. Please try again.';
                errDiv.classList.remove('hidden');
                btn.disabled = false;
                btnText.innerHTML = '<span class="material-symbols-outlined text-[18px]">auto_awesome</span> Analyze My Resume';
            });
        });
    }

    // Auto-reload when processing
    @if($resumeStatus === 'processing')
    setTimeout(function () { window.location.reload(); }, 6000);
    @endif

    // Expose helpers to inline handlers
    window.onFileSelected = onFileSelected;
    window.clearFile = clearFile;
    window.handleDrop = handleDrop;
})();
</script>
@endpush
