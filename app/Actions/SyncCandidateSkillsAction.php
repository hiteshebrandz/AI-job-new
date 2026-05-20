<?php

namespace App\Actions;

use App\Models\Candidate;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Str;

class SyncCandidateSkillsAction
{
    /**
     * @param  array<int, string>  $skillNames
     * @return array<int, string>
     */
    public function execute(User $user, array $skillNames): array
    {
        $normalized = collect($skillNames)
            ->map(fn ($s) => trim((string) $s))
            ->filter()
            ->unique(fn ($s) => Str::lower($s))
            ->values();

        $skillIds = $normalized->map(function (string $name) {
            return Skill::findOrCreateByName($name)->id;
        })->all();

        $user->skills()->sync($skillIds);

        $names = $normalized->all();

        $candidate = Candidate::query()->where('user_id', $user->id)->first();
        if ($candidate) {
            $candidate->update(['skills' => $names]);
        }

        return $names;
    }
}
