<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SavedJob;
use App\Services\JobMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedJobsController extends Controller
{
    public function __construct(
        private JobMatchService $jobMatchService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $candidate = $user->candidate;

        $saved = SavedJob::query()
            ->where('user_id', $user->id)
            ->with(['job' => fn ($q) => $q->where('status', Job::STATUS_ACTIVE)])
            ->latest()
            ->paginate(10);

        $saved->getCollection()->transform(function (SavedJob $record) use ($candidate, $user) {
            $job = $record->job;
            if ($job) {
                $job->match_score = $this->jobMatchService->percentage($job, $candidate);
                $job->has_applied = $user->jobApplications()->where('job_id', $job->id)->exists();
            }

            return $record;
        });

        return view('pages.user_saved_jobs', [
            'activeNav' => 'jobs',
            'savedJobs' => $saved,
        ]);
    }

    public function destroy(Request $request, Job $job): JsonResponse
    {
        $deleted = SavedJob::query()
            ->where('user_id', $request->user()->id)
            ->where('job_id', $job->id)
            ->delete();

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Job was not in your saved list.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Job removed from saved jobs.',
        ]);
    }
}
