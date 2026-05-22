<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeOptimizerRun extends Model
{
    public const STATUS_UPLOADED   = 'uploaded';
    public const STATUS_ANALYZING  = 'analyzing';
    public const STATUS_ANALYZED   = 'analyzed';
    public const STATUS_GENERATING = 'generating';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_FAILED     = 'failed';

    protected $fillable = [
        'user_id',
        'original_file_name',
        'original_file_path',
        'file_type',
        'extracted_text',
        'status',
        'processing_started_at',
        'analysis_result',
        'generated_file_path',
        'error_message',
    ];

    protected $casts = [
        'analysis_result'         => 'array',
        'processing_started_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isAnalyzing(): bool
    {
        return in_array($this->status, [self::STATUS_UPLOADED, self::STATUS_ANALYZING], true);
    }

    public function isAnalyzed(): bool
    {
        return $this->status === self::STATUS_ANALYZED;
    }

    public function isGenerating(): bool
    {
        return $this->status === self::STATUS_GENERATING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function score(): ?int
    {
        $result = $this->analysis_result;

        return isset($result['score']) ? (int) $result['score'] : null;
    }

    /**
     * @return array{
     *     phase: string,
     *     phase_label: string,
     *     elapsed_seconds: int,
     *     estimated_total_seconds: int,
     *     estimated_remaining_seconds: int,
     *     progress_percent: int
     * }
     */
    public function processingProgress(): array
    {
        $estimatedTotal = match ($this->status) {
            self::STATUS_ANALYZING, self::STATUS_UPLOADED => 60,
            self::STATUS_GENERATING => 30,
            default => 0,
        };

        if ($estimatedTotal === 0) {
            return [
                'phase'                       => 'idle',
                'phase_label'                 => '',
                'elapsed_seconds'             => 0,
                'estimated_total_seconds'     => 0,
                'estimated_remaining_seconds' => 0,
                'progress_percent'            => 0,
            ];
        }

        $startedAt = $this->processing_started_at ?? $this->updated_at;
        $elapsed = max(0, (int) $startedAt?->diffInSeconds(now()));
        $remaining = max(0, $estimatedTotal - $elapsed);
        $percent = min(95, (int) round(($elapsed / $estimatedTotal) * 100));

        $phaseLabel = match ($this->status) {
            self::STATUS_UPLOADED, self::STATUS_ANALYZING => $elapsed < 15
                ? 'Extracting text from your resume…'
                : 'Running ATS analysis with AI…',
            self::STATUS_GENERATING => $elapsed < 20
                ? 'Preparing your resume content…'
                : ($elapsed < 90
                    ? 'AI is rewriting and optimizing sections…'
                    : 'Building your PDF document…'),
            default => 'Processing…',
        };

        return [
            'phase'                       => $this->status,
            'phase_label'                 => $phaseLabel,
            'elapsed_seconds'             => $elapsed,
            'estimated_total_seconds'     => $estimatedTotal,
            'estimated_remaining_seconds' => $remaining,
            'progress_percent'            => $percent,
        ];
    }
}
