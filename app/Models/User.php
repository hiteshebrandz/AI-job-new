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
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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
}
