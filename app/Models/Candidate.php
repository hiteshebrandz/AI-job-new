<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'candidate_code',
        'full_name',
        'email',
        'phone',
        'location',
        'current_title',
        'experience_years',
        'seniority_level',
        'previous_companies',
        'education',
        'university',
        'graduation_year',
        'skills',
        'resume_path',
        'ai_recommendation',
        'ai_score',
    ];

    protected $casts = [
        'skills' => 'array',
        'experience_years' => 'integer',
        'graduation_year' => 'integer',
        'ai_score' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parsingLogs(): HasMany
    {
        return $this->hasMany(ResumeParsingLog::class);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'EH-'.strtoupper(substr(uniqid(), -8));
        } while (self::query()->where('candidate_code', $code)->exists());

        return $code;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->full_name)) ?: [];

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr($parts[1], 0, 1));
        }

        return strtoupper(substr($this->full_name, 0, 2));
    }
}
