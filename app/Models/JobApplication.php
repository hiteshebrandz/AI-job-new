<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    public const STATUS_APPLIED = 'applied';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_SHORTLISTED = 'shortlisted';

    public const STATUS_INTERVIEW = 'interview_scheduled';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_HIRED = 'hired';

    public static function statuses(): array
    {
        return [
            self::STATUS_APPLIED => 'Applied',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_INTERVIEW => 'Interview Scheduled',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_HIRED => 'Hired',
        ];
    }

    public static function statusLabel(string $status): string
    {
        return self::statuses()[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    protected $fillable = [
        'user_id',
        'job_id',
        'status',
        'match_score',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'match_score' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ApplicationNotification::class);
    }
}
