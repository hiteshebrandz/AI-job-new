<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Job;

class JobMatchService
{
    /**
     * Return a match percentage (52–99) for a job/candidate pair.
     *
     * Weights:
     *   Skills      60 %
     *   Experience  25 %
     *   Education   15 %
     */
    public function percentage(Job $job, ?Candidate $candidate): int
    {
        $jobSkills = $this->normalizeSkills($job->skills_required);

        // No job skills defined → use id-based pseudo score
        if ($jobSkills === []) {
            return 70 + ($job->id % 29);
        }

        if (! $candidate) {
            return max(55, min(88, 50 + count($jobSkills) * 3));
        }

        // ── Skills match (60 %) ────────────────────────────────────────────────
        $candidateSkills = $this->normalizeSkills($candidate->skills ?? []);
        $skillsScore     = 0;

        if ($candidateSkills !== []) {
            $matched     = count(array_intersect(
                array_map('strtolower', $candidateSkills),
                array_map('strtolower', $jobSkills)
            ));
            $skillsScore = $matched / max(count($jobSkills), 1); // 0.0 – 1.0
        } else {
            $skillsScore = 0.30; // partial score when no skills on file
        }

        // ── Experience match (25 %) ────────────────────────────────────────────
        $experienceScore = $this->experienceScore($job, $candidate);

        // ── Education match (15 %) ────────────────────────────────────────────
        $educationScore = $this->educationScore($job, $candidate);

        // ── Weighted total ────────────────────────────────────────────────────
        $raw = ($skillsScore * 0.60)
             + ($experienceScore * 0.25)
             + ($educationScore * 0.15);

        $score = (int) round(52 + ($raw * 47)); // map 0.0–1.0 → 52–99

        return min(99, max(52, $score));
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function experienceScore(Job $job, Candidate $candidate): float
    {
        $requiredYears  = $this->parseExperienceYears($job->experience_required ?? '');
        $candidateYears = (int) ($candidate->experience_years ?? 0);

        if ($requiredYears === 0) {
            return 1.0; // no requirement set — full points
        }

        if ($candidateYears >= $requiredYears) {
            return 1.0;
        }

        // Partial credit: ratio capped at 0.95 if close
        return min(0.95, $candidateYears / $requiredYears);
    }

    private function educationScore(Job $job, Candidate $candidate): float
    {
        $jobDesc       = strtolower($job->requirements ?? '') . ' ' . strtolower($job->description ?? '');
        $candidateEdu  = strtolower($candidate->education ?? '');

        if ($candidateEdu === '') {
            return 0.5; // neutral
        }

        $degreeWeights = [
            'phd' => 4, 'doctorate' => 4,
            'master' => 3, 'mba' => 3, 'm.s' => 3, 'msc' => 3,
            'bachelor' => 2, 'b.s' => 2, 'b.e' => 2, 'bsc' => 2, 'b.tech' => 2,
            'associate' => 1, 'diploma' => 1,
        ];

        $jobLevel       = 0;
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
            return 1.0; // job doesn't specify education — full points
        }

        if ($candidateLevel >= $jobLevel) {
            return 1.0;
        }

        return max(0.4, $candidateLevel / $jobLevel);
    }

    private function parseExperienceYears(string $text): int
    {
        if (preg_match('/(\d+)\+?\s*(?:years?|yrs?)/i', $text, $m)) {
            return (int) $m[1];
        }

        // e.g. "Senior" → assume 5+ years
        $levelMap = [
            'entry'    => 0,
            'junior'   => 1,
            'mid'      => 3,
            'senior'   => 5,
            'lead'     => 7,
            'principal'=> 8,
            'director' => 10,
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
