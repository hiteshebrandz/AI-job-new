<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeAnalytics extends Model
{
    use HasFactory;

    protected $table = 'resume_analytics';

    protected $fillable = [
        'user_id',
        'resume_id',
        'candidate_name',
        'email',
        'phone',
        'current_role',
        'total_experience_years',
        'ai_score',
        'top_match_percentage',
        'application_count',
        'skill_count',
        'skills',
        'missing_skills',
        'skill_gap_analysis',
        'career_growth',
        'education',
        'nlp_analysis',
        'soft_skills',
        'ai_profile_summary',
        'resume_improvements',
        'job_recommendations',
        'strengths',
        'weaknesses',
        'raw_ai_response',
    ];

    protected $casts = [
        'total_experience_years' => 'float',
        'ai_score'               => 'integer',
        'top_match_percentage'   => 'integer',
        'application_count'      => 'integer',
        'skill_count'            => 'integer',
        'skills'                 => 'array',
        'missing_skills'         => 'array',
        'skill_gap_analysis'     => 'array',
        'career_growth'          => 'array',
        'education'              => 'array',
        'nlp_analysis'           => 'array',
        'soft_skills'            => 'array',
        'resume_improvements'    => 'array',
        'job_recommendations'    => 'array',
        'strengths'              => 'array',
        'weaknesses'             => 'array',
        'raw_ai_response'        => 'array',
    ];

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
