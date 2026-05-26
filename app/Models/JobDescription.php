<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobDescription extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ANALYZING = 'analyzing';

    public const STATUS_READY = 'ready';

    public const STATUS_MATCHING = 'matching';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const SOURCE_TEXT = 'text';

    public const SOURCE_FILE = 'file';

    protected $fillable = [
        'hr_id',
        'title',
        'jd_content',
        'source_type',
        'file_path',
        'file_name',
        'extracted_requirements',
        'status',
        'analysis_error',
    ];

    protected $casts = [
        'extracted_requirements' => 'array',
    ];

    public function hr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    public function candidateMatches(): HasMany
    {
        return $this->hasMany(CandidateMatch::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_ANALYZING,
            self::STATUS_READY,
            self::STATUS_MATCHING,
        ], true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ANALYZING => 'Analyzing JD',
            self::STATUS_READY => 'Ready to match',
            self::STATUS_MATCHING => 'Matching candidates',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            default => 'Pending',
        };
    }
}
