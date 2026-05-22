@extends('layouts.candidate', ['activeNav' => 'resume-optimizer'])

@section('title', 'AI Resume Optimizer')

@section('body-class', 'bg-background text-on-surface font-body-md overflow-x-hidden min-h-screen')

@section('page-css', 'resume_ai_optimizer.css')

@section('page-main')
<div class="space-y-8" id="optimizer-app">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="font-headline-lg text-2xl md:text-3xl font-bold">AI Resume Optimizer</h1>
            <p class="text-sm text-on-surface-variant mt-1 max-w-2xl">
                Upload your resume for ATS-friendly analysis and suggestions, then generate an improved PDF you can download.
            </p>
        </div>
    </div>

    <div id="optimizer-error" class="hidden text-red-400 text-sm p-4 rounded-xl bg-red-500/10 border border-red-500/30"></div>

    @if ($errors->any())
        <div class="text-red-400 text-sm p-4 rounded-xl bg-red-500/10 border border-red-500/30">
            {{ $errors->first() }}
        </div>
    @endif

    @if ($pageStatus === 'empty' || $pageStatus === 'failed')
        <section class="flex flex-col items-center justify-center py-12">
            <div class="glass-card p-10 rounded-2xl max-w-xl w-full text-center">
                @if ($pageStatus === 'failed')
                    <div class="w-16 h-16 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-red-400 text-[36px]">error_outline</span>
                    </div>
                    <h2 class="text-xl font-bold mb-2">Something went wrong</h2>
                    <p class="text-sm text-on-surface-variant mb-6">{{ $run?->error_message ?? 'Analysis or generation failed.' }}</p>
                @else
                    <div class="w-20 h-20 bg-secondary/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-secondary text-[40px]">auto_fix_high</span>
                    </div>
                    <h2 class="font-headline-lg text-2xl font-bold mb-3">Optimize Your Resume for ATS</h2>
                    <p class="text-sm text-on-surface-variant mb-8 leading-relaxed">
                        Get actionable feedback on ATS compatibility, formatting, keywords, and bullet points — then generate a polished resume PDF.
                    </p>
                @endif

                <form id="upload-form" action="{{ route('user.resume.ai-optimizer.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="drop-zone"
                         class="border-2 border-dashed border-outline-variant hover:border-secondary rounded-xl p-10 cursor-pointer transition-colors mb-4"
                         onclick="document.getElementById('resume-input').click()"
                         ondragover="event.preventDefault(); this.classList.add('border-secondary')"
                         ondragleave="this.classList.remove('border-secondary')"
                         ondrop="handleDrop(event)">
                        <span class="material-symbols-outlined text-secondary text-[36px]">upload_file</span>
                        <p class="font-semibold mt-3 text-sm">Click or drag &amp; drop to upload</p>
                        <p class="text-xs text-on-surface-variant mt-1">PDF, DOC, or DOCX · Max {{ (int) (config('resume.optimizer_max_upload_kb') / 1024) }} MB</p>
                        <input type="file" id="resume-input" name="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword" class="hidden" onchange="onFileSelected(this)">
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

                    <div id="upload-progress" class="hidden mb-4">
                        <div class="h-2 rounded-full bg-surface-container overflow-hidden">
                            <div id="upload-progress-bar" class="h-full bg-secondary optimizer-progress-bar" style="width:0%"></div>
                        </div>
                        <p id="upload-progress-label" class="text-xs text-on-surface-variant mt-2 text-left">Uploading…</p>
                    </div>

                    <button type="submit" id="upload-btn" class="hidden w-full bg-gradient-to-r from-secondary to-purple-600 text-white px-6 py-3 rounded-xl font-semibold text-sm shadow-md hover:opacity-90 transition-opacity disabled:opacity-60">
                        <span id="btn-text" class="flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                            Analyze My Resume
                        </span>
                    </button>
                </form>
            </div>
        </section>

    @elseif ($pageStatus === 'analyzing' || $pageStatus === 'generating')
        <section id="optimizer-processing-section" class="flex flex-col items-center justify-center py-16"
                 data-mode="{{ $processingMode ?? $pageStatus }}">
            <div class="glass-card p-10 rounded-2xl max-w-lg w-full text-center">
                <div class="w-20 h-20 bg-secondary/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-secondary animate-spin text-[40px]">sync</span>
                </div>
                <h2 class="text-2xl font-bold mb-2" id="optimizer-progress-title">
                    {{ $pageStatus === 'generating' ? 'Generating Your Resume' : 'Analyzing Your Resume' }}
                </h2>
                <p class="text-sm text-on-surface-variant mb-4" id="optimizer-progress-phase">
                    {{ $pageStatus === 'generating' ? 'Preparing your resume content…' : 'Extracting text from your resume…' }}
                </p>

                <div class="mb-4 text-left">
                    <div class="flex justify-between text-xs text-on-surface-variant mb-2">
                        <span>Elapsed: <strong id="optimizer-elapsed" class="text-on-surface">0s</strong></span>
                        <span>Est. remaining: <strong id="optimizer-remaining" class="text-secondary">—</strong></span>
                    </div>
                    <div class="h-2.5 rounded-full bg-surface-container overflow-hidden">
                        <div id="optimizer-progress-bar" class="h-full bg-gradient-to-r from-secondary to-purple-600 optimizer-progress-bar transition-all duration-500" style="width:5%"></div>
                    </div>
                    <p class="text-[11px] text-on-surface-variant mt-2" id="optimizer-progress-hint">
                        @if ($pageStatus === 'generating')
                            Building your PDF — usually under 30 seconds. Please keep this tab open.
                        @else
                            Analysis usually finishes within 30–60 seconds.
                        @endif
                    </p>
                </div>

                <p class="text-xs text-on-surface-variant" id="optimizer-poll-status">Checking status…</p>
            </div>
        </section>

    @elseif (in_array($pageStatus, ['analyzed', 'completed'], true) && $analysis)
        @php
            $score = (int) ($analysis['score'] ?? 0);
            $atsStatus = $analysis['ats_status'] ?? 'needs_improvement';
            $atsLabels = [
                'ats_friendly' => ['label' => 'ATS-Friendly', 'class' => 'bg-green-500/20 text-green-400 border-green-500/30'],
                'needs_improvement' => ['label' => 'Needs Improvement', 'class' => 'bg-amber-500/20 text-amber-400 border-amber-500/30'],
                'critical' => ['label' => 'Critical Issues', 'class' => 'bg-red-500/20 text-red-400 border-red-500/30'],
            ];
            $atsBadge = $atsLabels[$atsStatus] ?? $atsLabels['needs_improvement'];
            $categories = [
                ['key' => 'ats_issues', 'title' => 'ATS Issues', 'icon' => 'rule'],
                ['key' => 'formatting_suggestions', 'title' => 'Formatting', 'icon' => 'format_align_left'],
                ['key' => 'content_suggestions', 'title' => 'Content & Clarity', 'icon' => 'edit_note'],
                ['key' => 'skills_suggestions', 'title' => 'Skills', 'icon' => 'psychology'],
                ['key' => 'experience_suggestions', 'title' => 'Experience', 'icon' => 'work_history'],
            ];
        @endphp

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 glass-card p-6 rounded-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-4">Resume Score</p>
                <div class="flex items-center gap-6">
                    <div class="optimizer-score-ring w-24 h-24 rounded-full flex items-center justify-center flex-shrink-0" style="--score: {{ $score }}">
                        <div class="w-[4.5rem] h-[4.5rem] rounded-full bg-surface flex flex-col items-center justify-center">
                            <span class="text-2xl font-extrabold text-secondary">{{ $score }}</span>
                            <span class="text-[9px] text-on-surface-variant uppercase">/ 100</span>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $atsBadge['class'] }}">
                            {{ $atsBadge['label'] }}
                        </span>
                        <div class="mt-4 h-2 rounded-full bg-surface-container overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-secondary to-purple-600 optimizer-progress-bar" style="width:{{ $score }}%"></div>
                        </div>
                    </div>
                </div>
                @if (!empty($analysis['summary']))
                    <p class="text-sm text-on-surface-variant mt-6 leading-relaxed">{{ $analysis['summary'] }}</p>
                @endif

                <div class="mt-8 space-y-3">
                    @if ($pageStatus === 'completed' && $run?->generated_file_path)
                        <a href="{{ route('user.resume.ai-optimizer.download', $run) }}"
                           class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-secondary to-purple-600 text-white px-6 py-3 rounded-xl font-semibold text-sm shadow-md hover:opacity-90">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Download Optimized Resume
                        </a>
                    @elseif ($pageStatus === 'analyzed')
                        <button type="button" id="generate-btn" data-run-id="{{ $run->id }}"
                                class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-secondary to-purple-600 text-white px-6 py-3 rounded-xl font-semibold text-sm shadow-md hover:opacity-90 disabled:opacity-60">
                            <span class="material-symbols-outlined text-[18px]">description</span>
                            Generate My New Resume
                        </button>
                    @endif
                    <a href="{{ route('user.resume.ai-optimizer', ['new' => 1]) }}"
                       class="w-full flex items-center justify-center gap-2 border border-outline-variant px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-surface-container-high">
                        <span class="material-symbols-outlined text-[18px]">upload_file</span>
                        Upload Another Resume
                    </a>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                @if (!empty($analysis['missing_sections']))
                    <div class="glass-card p-5 rounded-2xl border border-amber-500/30">
                        <h3 class="font-semibold text-sm flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-amber-400 text-[20px]">warning</span>
                            Missing Sections
                        </h3>
                        <ul class="list-disc list-inside text-sm text-on-surface-variant space-y-1">
                            @foreach ($analysis['missing_sections'] as $section)
                                <li>{{ is_string($section) ? $section : ($section['name'] ?? json_encode($section)) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!empty($analysis['recommended_keywords']))
                    <div class="glass-card p-5 rounded-2xl">
                        <h3 class="font-semibold text-sm mb-3">Recommended Keywords</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($analysis['recommended_keywords'] as $kw)
                                <span class="px-3 py-1 rounded-full text-xs bg-secondary/15 text-secondary border border-secondary/25">{{ is_string($kw) ? $kw : ($kw['keyword'] ?? '') }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @foreach ($categories as $cat)
                    @php $items = $analysis[$cat['key']] ?? []; @endphp
                    @if (!empty($items))
                        <div class="glass-card p-5 rounded-2xl">
                            <h3 class="font-semibold text-sm flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-secondary text-[20px]">{{ $cat['icon'] }}</span>
                                {{ $cat['title'] }}
                            </h3>
                            <div class="space-y-3">
                                @foreach ($items as $item)
                                    @php
                                        $severity = $item['severity'] ?? 'medium';
                                        $isCritical = $severity === 'critical';
                                        $title = $item['title'] ?? $item['issue'] ?? 'Suggestion';
                                        $desc = $item['description'] ?? $item['suggestion'] ?? '';
                                    @endphp
                                    <div class="suggestion-card p-4 rounded-xl border border-outline-variant/50 {{ $isCritical ? 'suggestion-critical' : '' }}">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="text-sm font-semibold">{{ $title }}</p>
                                            @if ($isCritical)
                                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-red-500/20 text-red-400">Critical</span>
                                            @endif
                                        </div>
                                        @if ($desc)
                                            <p class="text-xs text-on-surface-variant mt-2 leading-relaxed">{{ $desc }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                @if (!empty($analysis['final_recommendations']))
                    <div class="glass-card p-5 rounded-2xl border border-secondary/30">
                        <h3 class="font-semibold text-sm flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-secondary text-[20px]">tips_and_updates</span>
                            Final Recommendations
                        </h3>
                        <div class="space-y-3">
                            @foreach ($analysis['final_recommendations'] as $item)
                                <div class="p-4 rounded-xl bg-secondary/5 border border-secondary/20">
                                    <p class="text-sm font-semibold">{{ $item['title'] ?? 'Recommendation' }}</p>
                                    <p class="text-xs text-on-surface-variant mt-1">{{ $item['description'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
@endsection

@push('page-scripts')
<script>
window.resumeOptimizerConfig = {
    pageUrl: @json(route('user.resume.ai-optimizer')),
    generateUrl: @json(route('user.resume.ai-optimizer.generate')),
    statusUrlTemplate: @json(route('user.resume.ai-optimizer.status', ['run' => '__RUN__'])),
    pollRunId: @json($pollRunId),
    processingMode: @json($processingMode ?? null),
    pollIntervalMs: 4000,
    maxPollSeconds: @json(($processingMode ?? '') === 'generating' ? 90 : 90),
};
</script>
<script src="{{ asset('js/resume-ai-optimizer.js') }}"></script>
@endpush
