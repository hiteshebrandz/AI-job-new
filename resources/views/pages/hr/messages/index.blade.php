@extends('layouts.employer', ['activeNav' => 'messages'])

@section('title', 'Messages')

@section('page-css', 'ai-hiring.css')

@section('employer-main')
<div class="mb-8">
    <h2 class="text-[28px] font-extrabold text-on-surface">Messages</h2>
    <p class="text-sm text-on-surface-variant mt-1">Conversations with candidates</p>
</div>

@if ($conversations->isEmpty())
    <div class="glass-card p-12 rounded-2xl text-center">
        <span class="material-symbols-outlined text-[48px] text-on-surface-variant">forum</span>
        <p class="text-on-surface-variant mt-4">No conversations yet. Use <strong>Connect for Job</strong> from AI Hiring matches.</p>
    </div>
@else
    <div class="space-y-2">
        @foreach ($conversations as $conv)
            @php
                $other = $conv->candidate;
                $c = $other?->candidate;
                $name = $c?->full_name ?? $other?->name;
            @endphp
            <a href="{{ route('hr.messages.show', $conv) }}" class="glass-card p-4 rounded-2xl flex items-center gap-4 hover:border-secondary/40 block transition-all">
                <div class="w-12 h-12 rounded-xl gradient-violet flex items-center justify-center text-white font-bold">{{ $other?->initials() }}</div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-on-surface truncate">{{ $name }}</p>
                    @if ($conv->jobDescription)
                        <p class="text-xs text-on-surface-variant truncate">Re: {{ $conv->jobDescription->title }}</p>
                    @endif
                </div>
                @if ($conv->last_message_at)
                    <span class="text-[10px] text-on-surface-variant">{{ $conv->last_message_at->diffForHumans() }}</span>
                @endif
            </a>
        @endforeach
    </div>
@endif
@endsection

@push('employer-scripts')
<script src="{{ asset('js/hr-messages-badge.js') }}"></script>
@endpush
