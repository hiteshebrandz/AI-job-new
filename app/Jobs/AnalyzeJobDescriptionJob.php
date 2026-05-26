<?php

namespace App\Jobs;

use App\Models\JobDescription;
use App\Services\JobDescriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class AnalyzeJobDescriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 180;

    public function __construct(
        public int $jobDescriptionId
    ) {
        $this->onQueue('default');
    }

    public function handle(JobDescriptionService $service): void
    {
        $jd = JobDescription::query()->find($this->jobDescriptionId);
        if (! $jd) {
            return;
        }

        try {
            $service->analyze($jd);
            MatchCandidatesForJdJob::dispatch($jd->id);
        } catch (Throwable $e) {
            $jd->update([
                'status' => JobDescription::STATUS_FAILED,
                'analysis_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(?Throwable $exception): void
    {
        JobDescription::query()
            ->where('id', $this->jobDescriptionId)
            ->whereNotIn('status', [JobDescription::STATUS_COMPLETED])
            ->update([
                'status' => JobDescription::STATUS_FAILED,
                'analysis_error' => $exception?->getMessage() ?? 'Analysis failed.',
            ]);
    }
}
