<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Resume;
use App\Models\ResumeAnalytics;
use App\Models\User;
use App\Support\CandidateJobProfile;

class CandidateProfileBuilder
{
    public function forUser(User $user): CandidateJobProfile
    {
        $user->loadMissing(['candidate', 'skills']);

        $candidate = $user->candidate;
        $analytics = $this->latestCompletedAnalytics($user->id);

        return $this->build($candidate, $analytics, $user);
    }

    public function fromCandidate(?Candidate $candidate): CandidateJobProfile
    {
        if (! $candidate) {
            return new CandidateJobProfile;
        }

        $candidate->loadMissing('user');
        $analytics = $this->latestCompletedAnalytics((int) $candidate->user_id);

        return $this->build($candidate, $analytics, $candidate->user);
    }

    /** @var array<int, string> */
    private const TECH_VOCABULARY = [
        'php', 'laravel', 'javascript', 'typescript', 'react', 'angular', 'vue', 'node', 'nodejs',
        'python', 'django', 'flask', 'java', 'spring', 'kotlin', 'swift', 'go', 'golang', 'rust',
        'c#', 'csharp', '.net', 'asp.net', 'mysql', 'postgresql', 'mongodb', 'redis', 'docker',
        'kubernetes', 'aws', 'azure', 'gcp', 'git', 'ci/cd', 'devops', 'agile', 'scrum', 'html',
        'css', 'tailwind', 'bootstrap', 'rest', 'api', 'graphql', 'microservices', 'machine learning',
        'ai', 'data science', 'sql', 'nosql', 'linux', 'figma', 'ui', 'ux', 'seo', 'wordpress',
    ];

    /** @var array<int, string> */
    private const STOPWORDS = [
        'the', 'and', 'for', 'with', 'from', 'this', 'that', 'have', 'has', 'was', 'were', 'are',
        'our', 'your', 'you', 'will', 'can', 'all', 'any', 'per', 'via', 'into', 'about', 'over',
    ];

    private function build(?Candidate $candidate, ?ResumeAnalytics $analytics, ?User $user): CandidateJobProfile
    {
        $skills = [];
        $technologies = [];
        $keywords = [];
        $domains = [];
        $projects = [];
        $experienceYears = 0;
        $education = '';
        $currentTitle = '';
        $summary = '';

        if ($candidate) {
            $skills = array_merge($skills, $this->normalizeSkills($candidate->skills ?? []));
            $experienceYears = (int) ($candidate->experience_years ?? 0);
            $education = (string) ($candidate->education ?? '');
            $currentTitle = (string) ($candidate->current_title ?? '');
            $summary = trim((string) ($candidate->summary ?? '') . ' ' . (string) ($candidate->previous_companies ?? ''));
            $projects = array_merge($projects, $this->normalizeProjects($candidate->projects ?? []));
        }

        if ($user) {
            foreach ($user->skills as $skill) {
                $skills[] = $skill->name;
            }
        }

        if ($analytics) {
            $skills = array_merge($skills, $this->normalizeSkills($analytics->skills ?? []));
            if ($experienceYears === 0 && $analytics->total_experience_years) {
                $experienceYears = (int) round((float) $analytics->total_experience_years);
            }
            if ($currentTitle === '' && $analytics->current_role) {
                $currentTitle = (string) $analytics->current_role;
            }
            if ($summary === '' && $analytics->ai_profile_summary) {
                $summary = (string) $analytics->ai_profile_summary;
            }
            $projects = array_merge($projects, $this->projectsFromCareerGrowth($analytics->career_growth ?? []));
        }

        $skills = $this->uniqueLower($skills);
        $textBlob = implode(' ', array_filter([
            $currentTitle,
            $summary,
            $education,
            implode(' ', array_map(fn ($p) => ($p['title'] ?? '') . ' ' . ($p['description'] ?? '') . ' ' . ($p['tag'] ?? ''), $projects)),
        ]));

        $technologies = $this->uniqueLower(array_merge(
            $technologies,
            $this->extractTechnologies($textBlob),
            $skills,
            ...array_map(fn ($p) => $p['technologies'] ?? [], $projects)
        ));

        $keywords = $this->uniqueLower(array_merge(
            $this->tokenize($textBlob),
            $skills,
            $technologies
        ));

        foreach ($projects as $project) {
            if (! empty($project['tag'])) {
                $domains[] = $project['tag'];
            }
            if (! empty($project['title'])) {
                $domains[] = $project['title'];
            }
        }
        if ($currentTitle !== '') {
            $domains[] = $currentTitle;
        }

        $domains = $this->uniqueLower($domains);

        return new CandidateJobProfile(
            skills: $skills,
            technologies: $technologies,
            keywords: $keywords,
            domains: $domains,
            projects: $projects,
            experienceYears: $experienceYears,
            education: $education,
            currentTitle: $currentTitle,
            summary: $summary,
        );
    }

    private function latestCompletedAnalytics(int $userId): ?ResumeAnalytics
    {
        $resume = Resume::query()
            ->where('user_id', $userId)
            ->where('status', Resume::STATUS_COMPLETED)
            ->latest()
            ->first();

        return $resume?->analytics;
    }

    /**
     * @param  array<int, mixed>  $raw
     * @return array<int, array{title: string, company: string, description: string, tag: string, technologies: array<int, string>}>
     */
    private function normalizeProjects(array $raw): array
    {
        $out = [];
        foreach ($raw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $title = trim((string) ($item['title'] ?? ''));
            $description = trim((string) ($item['description'] ?? ''));
            $tag = trim((string) ($item['tag'] ?? $item['domain'] ?? ''));
            $techs = $this->normalizeSkills($item['technologies'] ?? $item['tech_stack'] ?? []);
            if ($title === '' && $description === '') {
                continue;
            }
            $out[] = [
                'title' => $title,
                'company' => trim((string) ($item['company'] ?? '')),
                'description' => $description,
                'tag' => $tag,
                'technologies' => array_merge($techs, $this->extractTechnologies($description . ' ' . $tag)),
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, mixed>  $careerGrowth
     * @return array<int, array{title: string, company: string, description: string, tag: string, technologies: array<int, string>}>
     */
    private function projectsFromCareerGrowth(array $careerGrowth): array
    {
        $out = [];
        foreach ($careerGrowth as $role) {
            if (! is_array($role)) {
                continue;
            }
            $title = trim((string) ($role['title'] ?? ''));
            $description = trim((string) ($role['description'] ?? ''));
            $tag = trim((string) ($role['tag'] ?? ''));
            if ($title === '' && $description === '') {
                continue;
            }
            $out[] = [
                'title' => $title,
                'company' => trim((string) ($role['company'] ?? '')),
                'description' => $description,
                'tag' => $tag,
                'technologies' => $this->extractTechnologies($description . ' ' . $tag . ' ' . $title),
            ];
        }

        return $out;
    }

    /**
     * @return array<int, string>
     */
    private function extractTechnologies(string $text): array
    {
        $lower = mb_strtolower($text);
        $found = [];
        foreach (self::TECH_VOCABULARY as $tech) {
            if (str_contains($lower, $tech)) {
                $found[] = $tech;
            }
        }

        return $this->uniqueLower($found);
    }

    /**
     * @return array<int, string>
     */
    private function tokenize(string $text): array
    {
        $parts = preg_split('/[^a-z0-9+#.]+/i', mb_strtolower($text)) ?: [];
        $tokens = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if (strlen($part) < 3 || in_array($part, self::STOPWORDS, true)) {
                continue;
            }
            $tokens[] = $part;
        }

        return $this->uniqueLower($tokens);
    }

    /**
     * @param  array<int, string>  $items
     * @return array<int, string>
     */
    private function uniqueLower(array $items): array
    {
        $normalized = [];
        foreach ($items as $item) {
            $item = mb_strtolower(trim((string) $item));
            if ($item !== '') {
                $normalized[$item] = $item;
            }
        }

        return array_values($normalized);
    }

    /**
     * @param  array<int, string>|string|null  $skills
     * @return array<int, string>
     */
    private function normalizeSkills(array|string|null $skills): array
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
