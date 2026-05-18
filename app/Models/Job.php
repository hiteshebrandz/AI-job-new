<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Job extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'hr_id',
        'company_id',
        'title',
        'slug',
        'company_name',
        'location',
        'job_type',
        'experience_required',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'skills_required',
        'screening_question_1',
        'screening_question_2',
        'screening_question_3',
        'minimum_qualification',
        'preferred_qualification',
        'work_mode',
        'notice_period',
        'salary',
        'min_salary',
        'max_salary',
        'currency',
        'application_deadline',
        'number_of_openings',
        'status',
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'number_of_openings' => 'integer',
    ];

    public function hr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_applications')
            ->withPivot('status', 'match_score', 'applied_at')
            ->withTimestamps();
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_jobs')->withTimestamps();
    }

    public function savedJobRecords(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function resolveCompany(): Company
    {
        if ($this->company) {
            return $this->company;
        }

        $company = Company::resolveForJob($this);
        $this->updateQuietly(['company_id' => $company->id]);

        return $company;
    }

    /**
     * @return array<int, string>
     */
    public function parseListField(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];
        $items = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/^[\-\*\x{2022}\d\.\)]+[\s]*/u', '', trim($line)) ?? '');
            if ($line !== '') {
                $items[] = $line;
            }
        }

        return $items;
    }

    /**
     * @return array<int, string>
     */
    public function responsibilitiesList(): array
    {
        return $this->parseListField($this->responsibilities);
    }

    /**
     * @return array<int, string>
     */
    public function requirementsList(): array
    {
        return $this->parseListField($this->requirements);
    }

    /**
     * @return array<int, string>
     */
    public function skillsList(): array
    {
        return app(\App\Services\JobMatchService::class)->normalizeSkills($this->skills_required);
    }

    /**
     * @return array<int, array{icon: string, label: string}>
     */
    public function benefitsList(): array
    {
        $parsed = $this->parseListField($this->benefits);
        $icons = ['medical_services', 'flight', 'home_work', 'savings', 'school', 'fitness_center'];

        if ($parsed !== []) {
            return array_map(
                fn (string $label, int $i) => ['icon' => $icons[$i % count($icons)], 'label' => $label],
                array_slice($parsed, 0, 4),
                array_keys(array_slice($parsed, 0, 4))
            );
        }

        $defaults = [
            ['icon' => 'medical_services', 'label' => 'Premium Health'],
            ['icon' => 'flight', 'label' => 'Unlimited PTO'],
        ];

        if ($this->work_mode === 'Remote') {
            $defaults[] = ['icon' => 'home_work', 'label' => 'Remote Work'];
        } else {
            $defaults[] = ['icon' => 'home_work', 'label' => 'Hybrid Options'];
        }

        $defaults[] = ['icon' => 'savings', 'label' => '401k Matching'];

        return array_slice($defaults, 0, 4);
    }

    public function ensureSlug(): void
    {
        if ($this->slug) {
            return;
        }

        $base = Str::slug($this->title) ?: 'job';
        $slug = $base;
        $n = 1;

        while (self::query()->where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $base.'-'.(++$n);
        }

        $this->updateQuietly(['slug' => $slug]);
    }

    public function displaySalary(): string
    {
        if ($this->min_salary || $this->max_salary) {
            $format = fn (?float $value): string => '$'.number_format((float) $value / 1000, 0).'k';

            if ($this->min_salary && $this->max_salary) {
                return $format($this->min_salary).' - '.$format($this->max_salary);
            }

            if ($this->max_salary) {
                return 'Up to '.$format($this->max_salary);
            }

            return 'From '.$format($this->min_salary);
        }

        return $this->salary ?: 'Competitive';
    }

    public function displayLocation(): string
    {
        if ($this->work_mode === 'Remote') {
            return $this->location && ! str_contains(strtolower($this->location), 'remote')
                ? $this->location.' (Remote)'
                : 'Remote (Global)';
        }

        if ($this->work_mode && $this->work_mode !== 'On-site') {
            return $this->location.' ('.$this->work_mode.')';
        }

        return $this->location;
    }

    public function isNewPosting(): bool
    {
        return $this->created_at?->greaterThan(now()->subDays(2)) ?? false;
    }

    public function matchPercentage(?\App\Models\Candidate $candidate = null): int
    {
        return app(\App\Services\JobMatchService::class)->percentage($this, $candidate);
    }

    public function matchRingOffset(): float
    {
        $circumference = 251.2;

        return round($circumference * (1 - $this->matchPercentage() / 100), 1);
    }

    public function companyInitials(): string
    {
        $words = preg_split('/\s+/', trim($this->company_name)) ?: [];

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->company_name, 0, 2));
    }

    public function usesRemoteIcon(): bool
    {
        return $this->work_mode === 'Remote'
            || str_contains(strtolower($this->location), 'remote');
    }
}
