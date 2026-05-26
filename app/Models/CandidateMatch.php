<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_description_id',
        'candidate_id',
        'user_id',
        'match_score',
        'ai_reason',
        'match_breakdown',
    ];

    protected $casts = [
        'match_score' => 'integer',
        'match_breakdown' => 'array',
    ];

    public function jobDescription(): BelongsTo
    {
        return $this->belongsTo(JobDescription::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
