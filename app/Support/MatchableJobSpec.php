<?php

namespace App\Support;

/**
 * Job-like requirements used for candidate matching (from Job or extracted JD).
 */
final class MatchableJobSpec
{
    /**
     * @param  array<int, string>  $skillsRequired
     */
    public function __construct(
        public readonly string $title = '',
        public readonly string $description = '',
        public readonly string $requirements = '',
        public readonly string $experienceRequired = '',
        public readonly array $skillsRequired = [],
    ) {}

    public static function fromJob(\App\Models\Job $job): self
    {
        $skills = app(\App\Services\JobMatchService::class)->normalizeSkills($job->skills_required);

        return new self(
            title: (string) ($job->title ?? ''),
            description: (string) ($job->description ?? ''),
            requirements: (string) ($job->requirements ?? ''),
            experienceRequired: (string) ($job->experience_required ?? ''),
            skillsRequired: $skills,
        );
    }

    /**
     * @param  array<string, mixed>  $extracted
     */
    public static function fromExtractedRequirements(array $extracted, ?string $title = null): self
    {
        $skills = [];
        foreach (['skills', 'technologies'] as $key) {
            $items = $extracted[$key] ?? [];
            if (is_array($items)) {
                foreach ($items as $item) {
                    $skills[] = is_string($item) ? $item : (string) ($item['name'] ?? '');
                }
            }
        }

        $skills = array_values(array_filter(array_map('trim', $skills)));

        $responsibilities = is_array($extracted['responsibilities'] ?? null)
            ? implode("\n", $extracted['responsibilities'])
            : (string) ($extracted['responsibilities'] ?? '');

        $preferred = is_array($extracted['preferred_qualifications'] ?? null)
            ? implode("\n", $extracted['preferred_qualifications'])
            : (string) ($extracted['preferred_qualifications'] ?? '');

        $education = (string) ($extracted['education'] ?? '');
        $experience = (string) ($extracted['experience'] ?? '');

        $requirements = trim(implode("\n", array_filter([
            $education !== '' ? "Education: {$education}" : '',
            $preferred,
        ])));

        $description = trim(implode("\n", array_filter([
            $responsibilities,
            is_string($extracted['keywords'] ?? null)
                ? $extracted['keywords']
                : (is_array($extracted['keywords'] ?? null) ? implode(' ', $extracted['keywords']) : ''),
        ])));

        return new self(
            title: $title ?? 'Role',
            description: $description,
            requirements: $requirements,
            experienceRequired: $experience,
            skillsRequired: $skills,
        );
    }
}
