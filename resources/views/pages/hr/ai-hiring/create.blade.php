@extends('layouts.employer', ['activeNav' => 'ai-hiring'])

@section('title', 'Upload Job Description')

@section('page-css', 'ai-hiring.css')

@section('employer-main')
<div class="mb-6">
    <a href="{{ route('hr.ai-hiring.index') }}" class="inline-flex items-center gap-2 text-sm text-on-surface-variant hover:text-secondary">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back to AI Hiring
    </a>
</div>

<div class="max-w-3xl">
    <span class="badge-violet text-[11px]">Step 1</span>
    <h2 class="text-[24px] font-extrabold text-on-surface mt-2">Upload or paste job description</h2>
    <p class="text-sm text-on-surface-variant mt-1 mb-6">Supported: plain text, PDF, DOCX, TXT (max {{ round($maxBytes / 1024 / 1024, 1) }} MB)</p>

    <form method="POST" action="{{ route('hr.ai-hiring.store') }}" enctype="multipart/form-data" class="glass-card p-6 rounded-2xl space-y-5" id="jd-upload-form">
        @csrf
        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Job title (optional)</label>
            <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Senior Full Stack Developer"
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-background text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Paste job description</label>
            <textarea name="jd_text" rows="12" placeholder="Paste the full job description here…"
                class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-background text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary">{{ old('jd_text') }}</textarea>
            @error('jd_text')<p class="text-xs text-[#F87171] mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="text-center text-xs text-on-surface-variant">— or upload a file —</div>

        <div id="jd-drop-zone" class="border-2 border-dashed border-outline-variant rounded-2xl p-8 text-center cursor-pointer hover:border-secondary/50 transition-colors">
            <span class="material-symbols-outlined text-[40px] text-on-surface-variant">upload_file</span>
            <p class="text-sm text-on-surface-variant mt-2">Drag & drop PDF, DOCX, or TXT</p>
            <p class="text-xs text-on-surface-variant mt-1" id="jd-file-label">No file selected</p>
            <input type="file" name="jd_file" id="jd-file-input" class="hidden" accept=".pdf,.docx,.txt">
        </div>
        @error('jd_file')<p class="text-xs text-[#F87171]">{{ $message }}</p>@enderror

        <button type="submit" class="btn-primary w-full py-3 text-sm font-semibold" id="jd-submit-btn">
            Analyze &amp; Match Candidates
        </button>
    </form>
</div>
@endsection

@push('employer-scripts')
<script src="{{ asset('js/hr-ai-hiring-create.js') }}"></script>
@endpush
