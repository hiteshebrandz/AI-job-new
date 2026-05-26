<?php

namespace App\Jobs;

use App\Models\JobDescription;
use App\Services\JdCandidateMatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class MatchCandidatesForJdJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 300;

    public function __construct(
        public int $jobDescriptionId
    ) {
        $this->onQueue('default');
    }

    public function handle(JdCandidateMatchService $matcher): void
    {
        $jd = JobDescription::query()->find($this->jobDescriptionId);
        if (! $jd || $jd->status === JobDescription::STATUS_FAILED) {
            return;
        }

        try {
            $matcher->matchAll($jd);
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
            ->where('status', '!=', JobDescription::STATUS_COMPLETED)
            ->update([
                'status' => JobDescription::STATUS_FAILED,
                'analysis_error' => $exception?->getMessage() ?? 'Matching failed.',
            ]);
    }
}
