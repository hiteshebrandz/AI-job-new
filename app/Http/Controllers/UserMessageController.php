<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Models\Conversation;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserMessageController extends Controller
{
    public function __construct(
        private MessageService $messageService,
    ) {}

    public function index(): View
    {
        $conversations = Conversation::query()
            ->where('candidate_id', auth()->id())
            ->with(['hr', 'jobDescription'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('pages.user.messages.index', [
            'activeNav' => 'messages',
            'conversations' => $conversations,
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorizeConversation($conversation);
        $this->messageService->markRead($conversation, auth()->user());

        $conversation->load(['hr', 'jobDescription', 'messages.sender']);

        return view('pages.user.messages.show', [
            'activeNav' => 'messages',
            'conversation' => $conversation,
            'messages' => $conversation->messages,
        ]);
    }

    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $items = $this->messageService
            ->listSince($conversation, $request->integer('after_id') ?: null)
            ->map(fn ($m) => $this->formatMessage($m));

        return response()->json(['messages' => $items]);
    }

    public function store(SendMessageRequest $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $message = $this->messageService->send(
            $conversation,
            $request->user(),
            $request->validated('body'),
        );

        return response()->json(['message' => $this->formatMessage($message)], 201);
    }

    public function markRead(Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);
        $count = $this->messageService->markRead($conversation, auth()->user());

        return response()->json(['marked' => $count]);
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => $this->messageService->unreadCountFor(auth()->user()),
        ]);
    }

    private function authorizeConversation(Conversation $conversation): void
    {
        abort_unless($conversation->candidate_id === auth()->id(), 403);
    }

    private function formatMessage(\App\Models\Message $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'sender_id' => $message->sender_id,
            'is_mine' => $message->sender_id === auth()->id(),
            'read_at' => $message->read_at?->toIso8601String(),
            'created_at' => $message->created_at?->toIso8601String(),
            'sender_name' => $message->sender?->name,
        ];
    }
}
