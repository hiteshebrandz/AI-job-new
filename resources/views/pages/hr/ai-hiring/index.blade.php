@extends('layouts.employer', ['activeNav' => 'ai-hiring'])

@section('title', 'AI Hiring')

@section('page-css', 'ai-hiring.css')

@section('employer-main')
<div class="mb-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <span class="badge-violet text-[11px]">AI Candidate Matching</span>
            <h2 class="text-[28px] font-extrabold text-on-surface mt-2">AI Hiring</h2>
            <p class="text-sm text-on-surface-variant mt-1">Upload a job description and find the best-matched candidates.</p>
        </div>
        <a href="{{ route('hr.ai-hiring.create') }}" class="btn-primary inline-flex items-center gap-2 py-2.5 px-5 text-sm">
            <span class="material-symbols-outlined text-[18px]">add</span>
            New JD Analysis
        </a>
    </div>

    @if (session('status'))
        <div class="mt-4 glass-card p-4 rounded-xl text-sm text-secondary">{{ session('status') }}</div>
    @endif

    @if ($descriptions->isEmpty())
        <div class="glass-card p-12 rounded-2xl text-center mt-8">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant">description</span>
            <p class="text-on-surface-variant mt-4">No job descriptions yet. Upload your first JD to start matching.</p>
            <a href="{{ route('hr.ai-hiring.create') }}" class="btn-primary inline-flex mt-6 py-2.5 px-5 text-sm">Upload Job Description</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
            @foreach ($descriptions as $jd)
                <a href="{{ route('hr.ai-hiring.matches', $jd) }}" class="glass-card p-5 rounded-2xl block hover:border-secondary/40 border border-transparent transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-on-surface truncate">{{ $jd->title }}</p>
                            <p class="text-xs text-on-surface-variant mt-1">{{ $jd->created_at?->format('M j, Y g:i A') }}</p>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded-md shrink-0
                            @if($jd->status === 'completed') bg-secondary-fixed text-secondary
                            @elseif($jd->status === 'failed') bg-[#1a1020] text-[#F87171]
                            @else bg-surface-container-high text-on-surface-variant @endif">
                            {{ $jd->statusLabel() }}
                        </span>
                    </div>
                    <p class="text-xs text-on-surface-variant mt-3 line-clamp-2">{{ Str::limit($jd->jd_content, 120) }}</p>
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $descriptions->links() }}</div>
    @endif
</div>
@endsection
