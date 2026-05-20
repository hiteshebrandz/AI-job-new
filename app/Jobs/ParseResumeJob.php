<?php

namespace App\Jobs;

use App\Models\ResumeParsingLog;
use App\Services\ResumeParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ParseResumeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 130;

    public function __construct(
        public int $resumeParsingLogId
    ) {
        // Run on a dedicated queue so resume parsing doesn't block other jobs.
        $this->onQueue('resumes');
    }

    public function handle(ResumeParserService $parser): void
    {
        $log = ResumeParsingLog::query()->find($this->resumeParsingLogId);

        if (! $log) {
            return;
        }

        try {
            $parser->parse($log);
        } catch (Throwable $e) {
            $log->update([
                'parsing_status' => ResumeParsingLog::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(?Throwable $exception): void
    {
        ResumeParsingLog::query()
            ->where('id', $this->resumeParsingLogId)
            ->where('parsing_status', '!=', ResumeParsingLog::STATUS_COMPLETED)
            ->update([
                'parsing_status' => ResumeParsingLog::STATUS_FAILED,
                'error_message' => $exception?->getMessage() ?? 'Parsing failed.',
            ]);
    }
}
