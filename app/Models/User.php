<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_USER = 'user';

    public const ROLE_HR = 'hr';

    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile_photo_path',
        'password',
        'role',
        'notification_settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'     => 'datetime',
        'notification_settings' => 'array',
    ];

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isHr(): bool
    {
        return $this->role === self::ROLE_HR;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_HR    => 'HR / Employer',
            self::ROLE_ADMIN => 'Administrator',
            default          => 'Candidate',
        };
    }

    public function initials(): string
    {
        $candidate = $this->relationLoaded('candidate') ? $this->candidate : $this->candidate()->first();
        if ($candidate && $candidate->full_name) {
            return $candidate->initials();
        }

        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    public function profilePhotoUrl(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        return asset('storage/' . ltrim($this->profile_photo_path, '/'));
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'hr_id');
    }

    public function candidate(): HasOne
    {
        return $this->hasOne(Candidate::class);
    }

    public function resumeParsingLogs(): HasMany
    {
        return $this->hasMany(ResumeParsingLog::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function savedJobRecords(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function savedJobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'saved_jobs')->withTimestamps();
    }

    public function appliedJobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_applications')
            ->withPivot('status', 'match_score', 'applied_at')
            ->withTimestamps();
    }

    public function applicationNotifications(): HasMany
    {
        return $this->hasMany(ApplicationNotification::class)->latest();
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills')->withTimestamps();
    }
}
