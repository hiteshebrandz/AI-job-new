<?php

namespace App\Support;

/**
 * Aggregated candidate signals used for job matching (skills, projects, tech, keywords).
 */
final class CandidateJobProfile
{
    /**
     * @param  array<int, string>  $skills
     * @param  array<int, string>  $technologies
     * @param  array<int, string>  $keywords
     * @param  array<int, string>  $domains
     * @param  array<int, array{title: string, company: string, description: string, tag: string, technologies: array<int, string>}>  $projects
     */
    public function __construct(
        public readonly array $skills = [],
        public readonly array $technologies = [],
        public readonly array $keywords = [],
        public readonly array $domains = [],
        public readonly array $projects = [],
        public readonly int $experienceYears = 0,
        public readonly string $education = '',
        public readonly string $currentTitle = '',
        public readonly string $summary = '',
    ) {}

    public function isEmpty(): bool
    {
        return $this->skills === []
            && $this->technologies === []
            && $this->keywords === []
            && $this->projects === []
            && $this->experienceYears === 0
            && $this->education === ''
            && $this->currentTitle === ''
            && $this->summary === '';
    }

    /**
     * @return array<int, string>
     */
    public function allMatchTokens(): array
    {
        $merged = array_merge(
            $this->skills,
            $this->technologies,
            $this->keywords,
            $this->domains,
        );

        foreach ($this->projects as $project) {
            $merged[] = $project['title'] ?? '';
            $merged[] = $project['tag'] ?? '';
            $merged = array_merge($merged, $project['technologies'] ?? []);
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($v) => mb_strtolower(trim((string) $v)),
            $merged
        ))));
    }
}
