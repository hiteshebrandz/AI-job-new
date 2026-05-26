<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\Conversation;
use App\Services\ConversationService;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HrMessageController extends Controller
{
    public function __construct(
        private ConversationService $conversationService,
        private MessageService $messageService,
    ) {}

    public function index(): View
    {
        $conversations = Conversation::query()
            ->where('hr_id', auth()->id())
            ->with(['candidate.candidate', 'jobDescription'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        return view('pages.hr.messages.index', [
            'activeNav' => 'messages',
            'conversations' => $conversations,
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorizeConversation($conversation);
        $this->messageService->markRead($conversation, auth()->user());

        $conversation->load(['candidate.candidate', 'jobDescription', 'messages.sender']);

        return view('pages.hr.messages.show', [
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
        abort_unless($conversation->hr_id === auth()->id(), 403);
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
