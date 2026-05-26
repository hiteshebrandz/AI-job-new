<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\JobDescription;
use App\Models\User;

class ConversationService
{
    public function findOrCreate(
        User $hr,
        User $candidate,
        ?JobDescription $jobDescription = null,
    ): Conversation {
        abort_unless($hr->isHr(), 403);
        abort_unless($candidate->isUser(), 404);

        $query = Conversation::query()
            ->where('hr_id', $hr->id)
            ->where('candidate_id', $candidate->id);

        if ($jobDescription) {
            $query->where('job_description_id', $jobDescription->id);
        } else {
            $query->whereNull('job_description_id');
        }

        $existing = $query->first();
        if ($existing) {
            return $existing;
        }

        return Conversation::create([
            'hr_id' => $hr->id,
            'candidate_id' => $candidate->id,
            'job_description_id' => $jobDescription?->id,
        ]);
    }

    public function authorizeParticipant(Conversation $conversation, User $user): void
    {
        abort_unless($conversation->involvesUser($user), 403);
    }
}
