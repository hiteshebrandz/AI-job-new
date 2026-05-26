<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;

class MessageService
{
    public function send(Conversation $conversation, User $sender, string $body): Message
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => trim($body),
        ]);

        $conversation->update(['last_message_at' => $message->created_at]);

        return $message->load('sender');
    }

    public function markRead(Conversation $conversation, User $reader): int
    {
        return Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $reader->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * @return Collection<int, Message>
     */
    public function listSince(Conversation $conversation, ?int $afterId = null): Collection
    {
        $query = $conversation->messages()->with('sender')->orderBy('id');

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        return $query->get();
    }

    public function unreadCountFor(User $user): int
    {
        if ($user->isHr()) {
            $conversationIds = Conversation::query()->where('hr_id', $user->id)->pluck('id');
        } else {
            $conversationIds = Conversation::query()->where('candidate_id', $user->id)->pluck('id');
        }

        if ($conversationIds->isEmpty()) {
            return 0;
        }

        return Message::query()
            ->whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}
