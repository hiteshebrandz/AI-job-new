<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Job;
use App\Support\CandidateJobProfile;
use App\Support\MatchableJobSpec;

class JobMatchService
{
    /**
     * Match percentage (52–99) using skills, projects/tech, experience, keywords, education.
     */
    public function percentage(Job $job, ?Candidate $candidate = null, ?CandidateJobProfile $profile = null): int
    {
        if ($profile === null && $candidate !== null) {
            $profile = app(CandidateProfileBuilder::class)->fromCandidate($candidate);
        }

        $jobSkills = $this->normalizeSkills($job->skills_required);

        if ($jobSkills === [] && ($profile === null || $profile->isEmpty())) {
            return 70 + ($job->id % 29);
        }

        if ($profile === null || $profile->isEmpty()) {
            return max(55, min(88, 50 + count($jobSkills) * 3));
        }

        $skillsScore      = $this->skillsScore($jobSkills, $profile);
        $projectsScore    = $this->projectsScore($job, $profile);
        $experienceScore  = $this->experienceScore($job, $profile);
        $keywordsScore    = $this->keywordsScore($job, $profile);
        $educationScore   = $this->educationScore($job, $profile);

        $raw = ($skillsScore * 0.35)
             + ($projectsScore * 0.25)
             + ($experienceScore * 0.15)
             + ($keywordsScore * 0.15)
             + ($educationScore * 0.10);

        $score = (int) round(52 + ($raw * 47));

        return min(99, max(52, $score));
    }

    public function percentageForSpec(MatchableJobSpec $spec, ?CandidateJobProfile $profile): int
    {
        return $this->percentage($this->jobFromSpec($spec), null, $profile);
    }

    public function matchReasonForSpec(MatchableJobSpec $spec, CandidateJobProfile $profile): string
    {
        return $this->matchReason($this->jobFromSpec($spec), $profile);
    }

    private function jobFromSpec(MatchableJobSpec $spec): Job
    {
        $job = new Job([
            'title' => $spec->title,
            'description' => $spec->description,
            'requirements' => $spec->requirements,
            'experience_required' => $spec->experienceRequired,
            'skills_required' => $spec->skillsRequired,
        ]);
        $job->id = 1;

        return $job;
    }

    /**
     * Short explanation for UI (why this job matched).
     */
    public function matchReason(Job $job, CandidateJobProfile $profile): string
    {
        $jobSkills = array_map('mb_strtolower', $this->normalizeSkills($job->skills_required));
        $candidateSkills = array_map('mb_strtolower', $profile->skills);

        $matched = array_values(array_intersect($candidateSkills, $jobSkills));
        if ($matched !== []) {
            $list = implode(', ', array_slice($matched, 0, 3));

            return "Strong skill overlap: {$list}.";
        }

        $techMatched = array_values(array_intersect(
            array_map('mb_strtolower', $profile->technologies),
            $jobSkills
        ));
        if ($techMatched !== []) {
            return 'Your project tech stack aligns with this role.';
        }

        if ($profile->currentTitle !== '' && str_contains(
            mb_strtolower($job->title ?? ''),
            mb_strtolower($profile->currentTitle)
        )) {
            return 'Role title matches your current position.';
        }

        return 'Matched from your profile, experience, and project background.';
    }

    private function skillsScore(array $jobSkills, CandidateJobProfile $profile): float
    {
        if ($jobSkills === []) {
            return 0.75;
        }

        $candidateSkills = array_map('mb_strtolower', $profile->skills);
        $technologies    = array_map('mb_strtolower', $profile->technologies);
        $pool            = array_unique(array_merge($candidateSkills, $technologies));

        if ($pool === []) {
            return 0.30;
        }

        $jobLower = array_map('mb_strtolower', $jobSkills);
        $matched  = 0;
        foreach ($jobLower as $skill) {
            foreach ($pool as $candidate) {
                if ($candidate === $skill || str_contains($candidate, $skill) || str_contains($skill, $candidate)) {
                    $matched++;
                    break;
                }
            }
        }

        return $matched / max(count($jobLower), 1);
    }

    private function projectsScore(Job $job, CandidateJobProfile $profile): float
    {
        if ($profile->projects === []) {
            return 0.45;
        }

        $jobText = mb_strtolower(implode(' ', [
            $job->title ?? '',
            $job->description ?? '',
            $job->requirements ?? '',
            implode(' ', $this->normalizeSkills($job->skills_required)),
        ]));

        $scores = [];
        foreach ($profile->projects as $project) {
            $blob = mb_strtolower(implode(' ', [
                $project['title'] ?? '',
                $project['company'] ?? '',
                $project['description'] ?? '',
                $project['tag'] ?? '',
                implode(' ', $project['technologies'] ?? []),
            ]));
            if ($blob === '') {
                continue;
            }
            similar_text($jobText, $blob, $percent);
            $scores[] = $percent / 100;

            foreach ($project['technologies'] ?? [] as $tech) {
                if (str_contains($jobText, mb_strtolower($tech))) {
                    $scores[] = 0.85;
                }
            }
        }

        return $scores === [] ? 0.45 : min(1.0, max($scores));
    }

    private function experienceScore(Job $job, CandidateJobProfile $profile): float
    {
        $requiredYears = $this->parseExperienceYears($job->experience_required ?? '');
        $candidateYears = $profile->experienceYears;

        if ($requiredYears === 0) {
            return 1.0;
        }

        if ($candidateYears >= $requiredYears) {
            return 1.0;
        }

        return min(0.95, $candidateYears / $requiredYears);
    }

    private function keywordsScore(Job $job, CandidateJobProfile $profile): float
    {
        $jobTokens = $this->jobTokens($job);
        $profileTokens = array_unique(array_merge(
            $profile->keywords,
            array_map('mb_strtolower', $profile->domains)
        ));

        if ($jobTokens === [] || $profileTokens === []) {
            return 0.5;
        }

        $jobSet = array_flip($jobTokens);
        $matched = 0;
        foreach ($profileTokens as $token) {
            if (isset($jobSet[$token])) {
                $matched++;
                continue;
            }
            foreach ($jobTokens as $jobToken) {
                if (str_contains($jobToken, $token) || str_contains($token, $jobToken)) {
                    $matched++;
                    break;
                }
            }
        }

        return min(1.0, $matched / max(min(count($jobTokens), 12), 1));
    }

    private function educationScore(Job $job, CandidateJobProfile $profile): float
    {
        $candidateEdu = mb_strtolower($profile->education);
        if ($candidateEdu === '') {
            return 0.5;
        }

        $jobDesc = mb_strtolower(($job->requirements ?? '') . ' ' . ($job->description ?? ''));

        $degreeWeights = [
            'phd' => 4, 'doctorate' => 4,
            'master' => 3, 'mba' => 3, 'm.s' => 3, 'msc' => 3,
            'bachelor' => 2, 'b.s' => 2, 'b.e' => 2, 'bsc' => 2, 'b.tech' => 2,
            'associate' => 1, 'diploma' => 1,
        ];

        $jobLevel = 0;
        $candidateLevel = 0;

        foreach ($degreeWeights as $keyword => $weight) {
            if (str_contains($jobDesc, $keyword)) {
                $jobLevel = max($jobLevel, $weight);
            }
            if (str_contains($candidateEdu, $keyword)) {
                $candidateLevel = max($candidateLevel, $weight);
            }
        }

        if ($jobLevel === 0) {
            return 1.0;
        }

        if ($candidateLevel >= $jobLevel) {
            return 1.0;
        }

        return max(0.4, $candidateLevel / $jobLevel);
    }

    /**
     * @return array<int, string>
     */
    private function jobTokens(Job $job): array
    {
        $text = mb_strtolower(implode(' ', [
            $job->title ?? '',
            $job->description ?? '',
            $job->requirements ?? '',
            implode(' ', $this->normalizeSkills($job->skills_required)),
        ]));

        $parts = preg_split('/[^a-z0-9+#.]+/i', $text) ?: [];
        $tokens = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if (strlen($part) >= 3) {
                $tokens[$part] = $part;
            }
        }

        return array_values($tokens);
    }

    private function parseExperienceYears(string $text): int
    {
        if (preg_match('/(\d+)\+?\s*(?:years?|yrs?)/i', $text, $m)) {
            return (int) $m[1];
        }

        $levelMap = [
            'entry' => 0, 'junior' => 1, 'mid' => 3, 'senior' => 5,
            'lead' => 7, 'principal' => 8, 'director' => 10,
        ];

        $lower = strtolower($text);
        foreach ($levelMap as $keyword => $years) {
            if (str_contains($lower, $keyword)) {
                return $years;
            }
        }

        return 0;
    }

    /**
     * @param  array<int, string>|string|null  $skills
     * @return array<int, string>
     */
    public function normalizeSkills(array|string|null $skills): array
    {
        if (is_array($skills)) {
            return array_values(array_filter(array_map('trim', $skills)));
        }

        if ($skills === null || trim((string) $skills) === '') {
            return [];
        }

        $parts = preg_split('/[,;\n|]+/', (string) $skills) ?: [];

        return array_values(array_filter(array_map('trim', $parts)));
    }
}
