<?php

namespace App\Jobs;

use App\Models\ResumeOptimizerRun;
use App\Services\PythonResumeOptimizerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class AnalyzeResumeOptimizerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 130;

    public function __construct(public readonly int $runId)
    {
        $this->onQueue('resumes');
    }

    public function handle(PythonResumeOptimizerService $optimizer): void
    {
        $run = ResumeOptimizerRun::find($this->runId);

        if (! $run) {
            return;
        }

        $run->update([
            'status'                => ResumeOptimizerRun::STATUS_ANALYZING,
            'processing_started_at' => now(),
        ]);

        try {
            $result = $optimizer->analyze($run);
        } catch (Throwable $e) {
            $this->markFailed($run, $e->getMessage());

            return;
        }

        $run->update([
            'extracted_text'          => $result['extracted_text'] ?? '',
            'analysis_result'         => $result['data'] ?? [],
            'status'                  => ResumeOptimizerRun::STATUS_ANALYZED,
            'processing_started_at'   => null,
            'error_message'           => null,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $run = ResumeOptimizerRun::find($this->runId);

        if ($run) {
            $this->markFailed($run, $exception?->getMessage() ?? 'Analysis failed.');
        }
    }

    private function markFailed(ResumeOptimizerRun $run, string $message): void
    {
        $run->update([
            'status'        => ResumeOptimizerRun::STATUS_FAILED,
            'error_message' => $message,
        ]);

        Log::error('AnalyzeResumeOptimizerJob failed', [
            'run_id'  => $run->id,
            'user_id' => $run->user_id,
            'error'   => $message,
        ]);
    }
}
