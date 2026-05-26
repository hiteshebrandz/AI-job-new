@extends('layouts.candidate', ['activeNav' => 'messages'])

@section('title', 'Messages')

@section('page-css', 'ai-hiring.css')

@section('page-main')
<div class="mb-8">
    <h2 class="text-[28px] font-extrabold" style="color: var(--text-primary);">Messages</h2>
    <p class="text-sm mt-1" style="color: var(--text-muted);">Messages from employers</p>
</div>

@if ($conversations->isEmpty())
    <div class="glass-card p-12 rounded-2xl text-center">
        <span class="material-symbols-outlined text-[48px]" style="color: var(--text-muted);">forum</span>
        <p class="mt-4" style="color: var(--text-secondary);">No messages yet. When an employer reaches out, it will appear here.</p>
    </div>
@else
    <div class="space-y-2">
        @foreach ($conversations as $conv)
            <a href="{{ route('user.messages.show', $conv) }}" class="glass-card p-4 rounded-2xl flex items-center gap-4 block hover:opacity-90 transition-opacity">
                <div class="w-12 h-12 rounded-xl gradient-violet flex items-center justify-center text-white font-bold">{{ $conv->hr?->initials() }}</div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold truncate" style="color: var(--text-primary);">{{ $conv->hr?->name }}</p>
                    @if ($conv->jobDescription)
                        <p class="text-xs truncate" style="color: var(--text-muted);">Re: {{ $conv->jobDescription->title }}</p>
                    @endif
                </div>
                @if ($conv->last_message_at)
                    <span class="text-[10px]" style="color: var(--text-muted);">{{ $conv->last_message_at->diffForHumans() }}</span>
                @endif
            </a>
        @endforeach
    </div>
@endif
@endsection

@push('page-scripts')
<script src="{{ asset('js/user-messages-badge.js') }}"></script>
@endpush
