<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Resume extends Model
{
    use HasFactory;

    protected $table = 'resume_files';

    public const STATUS_UPLOADED   = 'uploaded';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_FAILED     = 'failed';

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'extracted_text',
        'status',
        'error_message',
    ];

    protected $hidden = [
        'file_path',
        'extracted_text',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analytics(): HasOne
    {
        return $this->hasOne(ResumeAnalytics::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, [self::STATUS_UPLOADED, self::STATUS_PROCESSING], true);
    }
}
