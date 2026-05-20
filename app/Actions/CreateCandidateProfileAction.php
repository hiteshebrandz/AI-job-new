<?php

namespace App\Actions;

use App\Mail\CandidateWelcomeMail;
use App\Models\Candidate;
use App\Models\ResumeParsingLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateCandidateProfileAction
{
    public function __construct(
        private SyncCandidateSkillsAction $syncSkills
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     * @return array{user: User, candidate: Candidate, is_new_account: bool, plain_password: ?string}
     */
    public function execute(array $validated, User $authUser, bool $forceNewAccount = false): array
    {
        $log = ResumeParsingLog::findOrFail($validated['parsing_log_id']);

        if ($log->user_id !== $authUser->id) {
            abort(403);
        }

        $skills = array_values(array_filter($validated['skills'] ?? []));
        $authCandidate = Candidate::query()->where('user_id', $authUser->id)->first();
        $createNewAccount = $forceNewAccount || (
            ($validated['create_new_account'] ?? false)
            && $validated['email'] !== $authUser->email
        );

        $plainPassword = null;
        $isNewAccount = false;

        if ($createNewAccount) {
            if (User::query()->where('email', $validated['email'])->exists()) {
                throw ValidationException::withMessages([
                    'email' => ['This email is already registered.'],
                ]);
            }

            $plainPassword = Str::password(12);
            $user = User::create([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($plainPassword),
                'role' => User::ROLE_USER,
            ]);
            $isNewAccount = true;
        } else {
            $user = $authUser;

            if ($validated['email'] !== $user->email) {
                $exists = User::query()
                    ->where('email', $validated['email'])
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'email' => ['This email is already in use.'],
                    ]);
                }
            }

            $user->update([
                'name' => $validated['full_name'],
                'email' => $validated['email'],
            ]);
        }

        $profileCandidate = Candidate::query()->where('user_id', $user->id)->first();

        $summary = $validated['summary'] ?? $validated['ai_recommendation'] ?? null;

        $candidate = Candidate::updateOrCreate(
            ['user_id' => $user->id],
            [
                'candidate_code' => $profileCandidate?->candidate_code ?? Candidate::generateCode(),
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'location' => $validated['location'] ?? null,
                'current_title' => $validated['current_title'] ?? null,
                'experience_years' => $validated['experience_years'] ?? 0,
                'seniority_level' => $validated['seniority_level'] ?? null,
                'previous_companies' => $validated['previous_companies'] ?? null,
                'education' => $validated['education'] ?? null,
                'university' => $validated['university'] ?? null,
                'graduation_year' => $validated['graduation_year'] ?? null,
                'skills' => $skills,
                'resume_path' => $log->file_path,
                'ai_recommendation' => $validated['ai_recommendation'] ?? $summary,
                'summary' => $summary,
                'ai_score' => $validated['ai_score'] ?? $log->ai_score,
            ]
        );

        $this->syncSkills->execute($user, $skills);

        $log->update([
            'candidate_id' => $candidate->id,
            'parsing_status' => ResumeParsingLog::STATUS_COMPLETED,
        ]);

        try {
            Mail::to($candidate->email)->send(new CandidateWelcomeMail($candidate, $plainPassword, $isNewAccount));
        } catch (\Throwable) {
            // Mail may be unavailable in local dev.
        }

        return [
            'user' => $user,
            'candidate' => $candidate->fresh(),
            'is_new_account' => $isNewAccount,
            'plain_password' => $plainPassword,
        ];
    }
}
