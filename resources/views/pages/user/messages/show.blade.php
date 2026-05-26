@extends('layouts.candidate', ['activeNav' => 'messages'])

@section('title', 'Chat')

@section('page-css', 'ai-hiring.css')

@section('page-main-full')
<div class="pt-[80px] flex flex-col h-[calc(100vh-80px)] max-w-4xl mx-auto px-4 lg:px-8">
    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('user.messages.index') }}" style="color: var(--text-muted);">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-lg font-bold" style="color: var(--text-primary);">{{ $conversation->hr?->name }}</h2>
            @if ($conversation->jobDescription)
                <p class="text-xs" style="color: var(--text-muted);">{{ $conversation->jobDescription->title }}</p>
            @endif
        </div>
    </div>

    <div id="chat-messages" class="flex-1 overflow-y-auto glass-card rounded-2xl p-4 space-y-3 mb-4"
         data-list-url="{{ route('user.messages.list', $conversation) }}"
         data-send-url="{{ route('user.messages.store', $conversation) }}"
         data-read-url="{{ route('user.messages.read', $conversation) }}"
         data-last-id="{{ $messages->last()?->id ?? 0 }}">
        @foreach ($messages as $msg)
            <div class="chat-bubble {{ $msg->sender_id === auth()->id() ? 'chat-bubble-mine' : 'chat-bubble-theirs' }}" data-id="{{ $msg->id }}">
                <p class="text-sm whitespace-pre-wrap">{{ $msg->body }}</p>
                <p class="text-[10px] mt-1 opacity-70">{{ $msg->created_at?->format('M j, g:i A') }}</p>
            </div>
        @endforeach
    </div>

    <form id="chat-form" class="flex gap-2 pb-6">
        @csrf
        <input type="text" id="chat-input" name="body" placeholder="Type a reply…" autocomplete="off"
            class="flex-1 px-4 py-3 rounded-xl border text-sm" style="border-color: var(--border-default); background: var(--bg-surface); color: var(--text-primary);">
        <button type="submit" class="btn-primary px-5 py-3 text-sm">Send</button>
    </form>
</div>
@endsection

@push('page-scripts')
<script src="{{ asset('js/user-messages.js') }}"></script>
<script src="{{ asset('js/user-messages-badge.js') }}"></script>
@endpush
