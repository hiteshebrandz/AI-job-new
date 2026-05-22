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
use Illuminate\Support\Facades\Storage;
use Throwable;

class GenerateOptimizedResumeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 200;

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

        if (! in_array($run->status, [
            ResumeOptimizerRun::STATUS_ANALYZED,
            ResumeOptimizerRun::STATUS_GENERATING,
        ], true)) {
            return;
        }

        $run->update([
            'status'                  => ResumeOptimizerRun::STATUS_GENERATING,
            'processing_started_at'   => now(),
            'error_message'           => null,
        ]);

        $disk     = config('resume.optimizer_disk', 'public');
        $dir      = config('resume.optimizer_output_dir', 'resumes/optimized') . '/' . date('Y/m');
        $fileName = 'optimized_resume_' . $run->user_id . '_' . now()->format('Ymd_His') . '.pdf';
        $relative = $dir . '/' . $fileName;

        Storage::disk($disk)->makeDirectory($dir);
        $absoluteOutput = Storage::disk($disk)->path($relative);

        try {
            $optimizer->generate($run, $absoluteOutput);
        } catch (Throwable $e) {
            $this->markFailed($run, $e->getMessage());

            return;
        }

        if (! is_file($absoluteOutput)) {
            $this->markFailed($run, 'Generated PDF file was not created.');

            return;
        }

        $run->update([
            'generated_file_path'     => $relative,
            'status'                  => ResumeOptimizerRun::STATUS_COMPLETED,
            'processing_started_at'   => null,
            'error_message'           => null,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $run = ResumeOptimizerRun::find($this->runId);

        if ($run) {
            $this->markFailed($run, $exception?->getMessage() ?? 'Resume generation failed.');
        }
    }

    private function markFailed(ResumeOptimizerRun $run, string $message): void
    {
        $run->update([
            'status'        => ResumeOptimizerRun::STATUS_FAILED,
            'error_message' => $message,
        ]);

        Log::error('GenerateOptimizedResumeJob failed', [
            'run_id'  => $run->id,
            'user_id' => $run->user_id,
            'error'   => $message,
        ]);
    }
}
