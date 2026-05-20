<?php

namespace App\Http\Controllers;

use App\Models\ApplicationNotification;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\SavedJob;
use App\Services\CandidateProfileBuilder;
use App\Services\JobMatchService;
use App\Services\JobRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserJobController extends Controller
{
    public function __construct(
        private JobMatchService $jobMatchService,
        private JobRecommendationService $jobRecommendationService,
        private CandidateProfileBuilder $profileBuilder,
    ) {}

    public function show(Request $request, Job $job): View
    {
        abort_unless($job->status === Job::STATUS_ACTIVE, 404);

        $job->load('hr', 'company');
        $job->ensureSlug();
        $company = $job->resolveCompany();
        $user = $request->user();
        $profile = $this->profileBuilder->forUser($user);

        $hasApplied = JobApplication::query()
            ->where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->exists();

        $hasSaved = SavedJob::query()
            ->where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->exists();

        $matchScore = $this->jobMatchService->percentage($job, $user->candidate, $profile);
        $matchReason = $this->jobMatchService->matchReason($job, $profile);

        $similarJobs = Job::query()
            ->where('status', Job::STATUS_ACTIVE)
            ->where('id', '!=', $job->id)
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(function (Job $similar) use ($profile) {
                $similar->match_score = $this->jobMatchService->percentage($similar, null, $profile);

                return $similar;
            })
            ->sortByDesc('match_score')
            ->take(3)
            ->values();

        return view('pages.job_details', [
            'activeNav' => 'jobs',
            'job' => $job,
            'company' => $company,
            'matchScore' => $matchScore,
            'matchReason' => $matchReason,
            'hasApplied' => $hasApplied,
            'hasSaved' => $hasSaved,
            'highlightApply' => $request->boolean('apply'),
            'similarJobs' => $similarJobs,
            'responsibilities' => $job->responsibilitiesList(),
            'requirements' => $job->requirementsList(),
            'skills' => $job->skillsList(),
            'benefits' => $job->benefitsList(),
        ]);
    }

    public function apply(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->status === Job::STATUS_ACTIVE, 404);

        $user = $request->user();
        $profile = $this->profileBuilder->forUser($user);

        $exists = JobApplication::query()
            ->where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'You already applied for this job.',
                'applied' => true,
            ], 422);
        }

        $application = JobApplication::create([
            'user_id'     => $user->id,
            'job_id'      => $job->id,
            'status'      => JobApplication::STATUS_APPLIED,
            'match_score' => $this->jobMatchService->percentage($job, $user->candidate, $profile),
            'applied_at'  => now(),
        ]);

        ApplicationNotification::create([
            'user_id'            => $user->id,
            'job_application_id' => $application->id,
            'message'            => "Your application for {$job->title} at {$job->company_name} has been submitted successfully.",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your application has been submitted successfully.',
            'applied' => true,
        ]);
    }

    public function saveJob(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->status === Job::STATUS_ACTIVE, 404);

        $user = $request->user();

        $exists = SavedJob::query()
            ->where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Already saved',
                'saved' => true,
            ], 422);
        }

        SavedJob::create([
            'user_id' => $user->id,
            'job_id' => $job->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job saved successfully',
            'saved' => true,
        ]);
    }

    public function recommendations(Request $request): View
    {
        $result = $this->jobRecommendationService->recommendForUser($request->user(), $request);

        return view('pages.job_recommendations', [
            'activeNav'       => 'jobs',
            'jobs'            => $result['jobs'],
            'profile'         => $result['profile'],
            'appliedJobIds'   => $result['applied_job_ids'],
            'search'          => trim((string) $request->input('search', '')),
            'salaryBands'     => array_values(array_filter((array) $request->input('salary', []))),
            'jobTypes'        => array_values(array_filter((array) $request->input('job_types', []))),
            'distance'        => max(5, min(50, (int) $request->input('distance', 50))),
            'sort'            => (string) $request->input('sort', 'match'),
        ]);
    }

    public function recommendationsApi(Request $request): JsonResponse
    {
        $jobs = $this->jobRecommendationService->topMatchesForUser($request->user(), 8);
        $appliedIds = JobApplication::query()
            ->where('user_id', $request->user()->id)
            ->pluck('job_id');

        return response()->json([
            'success' => true,
            'jobs' => $jobs->map(fn (Job $job) => [
                'id' => $job->id,
                'title' => $job->title,
                'company_name' => $job->company_name,
                'location' => $job->displayLocation(),
                'job_type' => $job->job_type,
                'work_mode' => $job->work_mode,
                'salary' => $job->displaySalary(),
                'skills' => array_slice($job->skillsList(), 0, 6),
                'match_score' => $job->match_score,
                'match_reason' => $job->match_reason ?? '',
                'url' => route('user.jobs.show', $job),
                'apply_url' => route('user.jobs.show', ['job' => $job, 'apply' => 1]),
                'has_applied' => $appliedIds->contains($job->id),
            ])->values(),
        ]);
    }
}
