<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeParsingLog extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'guest_session_id',
        'candidate_id',
        'file_name',
        'file_path',
        'parsing_status',
        'ai_score',
        'parsed_data',
        'error_message',
    ];

    protected $casts = [
        'parsed_data' => 'array',
        'ai_score' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function isFinished(): bool
    {
        return in_array($this->parsing_status, [self::STATUS_COMPLETED, self::STATUS_FAILED], true);
    }

    /**
     * Stable API payload for frontend auto-fill (resume-upload.js).
     */
    public function toRegistrationPayload(): array
    {
        $data = $this->parsed_data ?? [];
        unset($data['parser_source']);

        return array_merge($data, [
            'current_title' => $data['current_title'] ?? $data['title'] ?? '',
            'summary' => $data['summary'] ?? $data['ai_recommendation'] ?? '',
        ]);
    }
}
