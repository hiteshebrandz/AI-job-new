<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\CandidateMatch;
use App\Models\JobDescription;
use App\Models\User;
use App\Support\CandidateJobProfile;
use App\Support\MatchableJobSpec;
use Illuminate\Support\Facades\Log;

class JdCandidateMatchService
{
    private const TOP_AI_EXPLAIN = 25;

    public function __construct(
        private CandidateProfileBuilder $profileBuilder,
        private JobMatchService $jobMatchService,
        private PythonJdAnalyzerService $jdAnalyzer,
    ) {}

    public function matchAll(JobDescription $jd): void
    {
        $jd->update(['status' => JobDescription::STATUS_MATCHING]);

        CandidateMatch::query()->where('job_description_id', $jd->id)->delete();

        $spec = MatchableJobSpec::fromExtractedRequirements(
            $jd->extracted_requirements ?? [],
            $jd->title
        );

        $users = User::query()
            ->where('role', User::ROLE_USER)
            ->with('candidate')
            ->get();

        $rows = [];

        foreach ($users as $user) {
            $candidate = $user->candidate;
            if (! $candidate) {
                continue;
            }

            $profile = $this->profileBuilder->forUser($user);
            $score = $this->jobMatchService->percentageForSpec($spec, $profile);
            $reason = $this->jobMatchService->matchReasonForSpec($spec, $profile);

            $rows[] = [
                'job_description_id' => $jd->id,
                'candidate_id' => $candidate->id,
                'user_id' => $user->id,
                'match_score' => $score,
                'ai_reason' => $reason,
                'match_breakdown' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        usort($rows, fn ($a, $b) => $b['match_score'] <=> $a['match_score']);

        foreach (array_chunk($rows, 100) as $chunk) {
            CandidateMatch::insert($chunk);
        }

        $this->enrichTopMatchesWithAi($jd, $spec, array_slice($rows, 0, self::TOP_AI_EXPLAIN));

        $jd->update(['status' => JobDescription::STATUS_COMPLETED]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $topRows
     */
    private function enrichTopMatchesWithAi(JobDescription $jd, MatchableJobSpec $spec, array $topRows): void
    {
        $jdSummary = json_encode([
            'title' => $jd->title,
            'requirements' => $jd->extracted_requirements,
        ], JSON_UNESCAPED_UNICODE);

        foreach ($topRows as $row) {
            $match = CandidateMatch::query()
                ->where('job_description_id', $jd->id)
                ->where('user_id', $row['user_id'])
                ->first();

            if (! $match) {
                continue;
            }

            $user = User::query()->with('candidate')->find($row['user_id']);
            if (! $user) {
                continue;
            }

            $profile = $this->profileBuilder->forUser($user);
            $candidateSummary = $this->profileSummary($user, $profile);

            try {
                $reason = $this->jdAnalyzer->explainMatch($jdSummary, $candidateSummary);
                if ($reason !== '') {
                    $match->update(['ai_reason' => $reason]);
                }
            } catch (\Throwable $e) {
                Log::debug('AI match explanation skipped', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }
    }

    private function profileSummary(User $user, CandidateJobProfile $profile): string
    {
        $candidate = $user->candidate;
        $parts = [
            'name' => $candidate?->full_name ?? $user->name,
            'title' => $candidate?->current_title ?? '',
            'experience_years' => $candidate?->experience_years ?? $profile->experienceYears,
            'skills' => array_slice($profile->skills, 0, 15),
            'technologies' => array_slice($profile->technologies, 0, 10),
            'education' => $candidate?->education ?? $profile->education,
            'summary' => $candidate?->summary ?? $profile->summary,
        ];

        return json_encode($parts, JSON_UNESCAPED_UNICODE);
    }
}
