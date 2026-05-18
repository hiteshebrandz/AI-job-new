<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Job;

class JobMatchService
{
    public function percentage(Job $job, ?Candidate $candidate): int
    {
        $jobSkills = $this->normalizeSkills($job->skills_required);
        if ($jobSkills === []) {
            return 70 + ($job->id % 29);
        }

        if (! $candidate) {
            return max(55, min(88, 50 + count($jobSkills) * 3));
        }

        $candidateSkills = $this->normalizeSkills($candidate->skills ?? []);
        if ($candidateSkills === []) {
            return 65 + ($job->id % 25);
        }

        $matched = count(array_intersect(
            array_map('strtolower', $candidateSkills),
            array_map('strtolower', $jobSkills)
        ));

        $ratio = $matched / max(count($jobSkills), 1);
        $score = (int) round(60 + ($ratio * 38));

        return min(99, max(52, $score));
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
