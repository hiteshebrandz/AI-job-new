<?php

namespace App\Http\Controllers;

use App\Models\ApplicationNotification;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\SavedJob;
use App\Services\JobMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserJobController extends Controller
{
    public function __construct(
        private JobMatchService $jobMatchService
    ) {}

    public function show(Request $request, Job $job): View
    {
        abort_unless($job->status === Job::STATUS_ACTIVE, 404);

        $job->load('hr');
        $job->ensureSlug();
        $company = $job->resolveCompany();
        $user = $request->user();
        $candidate = $user->candidate;

        $hasApplied = JobApplication::query()
            ->where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->exists();

        $hasSaved = SavedJob::query()
            ->where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->exists();

        $matchScore = $this->jobMatchService->percentage($job, $candidate);

        $similarJobs = Job::query()
            ->where('status', Job::STATUS_ACTIVE)
            ->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                $q->where('job_type', $job->job_type)
                    ->orWhere('company_name', $job->company_name);
            })
            ->latest()
            ->limit(3)
            ->get();

        return view('pages.job_details', [
            'activeNav' => 'jobs',
            'job' => $job,
            'company' => $company,
            'matchScore' => $matchScore,
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
            'match_score' => $this->jobMatchService->percentage($job, $user->candidate),
            'applied_at'  => now(),
        ]);

        // Notify candidate that their application was submitted
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
        $search = trim((string) $request->input('search', ''));
        $salaryBands = array_values(array_filter((array) $request->input('salary', [])));
        $jobTypes = array_values(array_filter((array) $request->input('job_types', [])));
        $distance = max(5, min(50, (int) $request->input('distance', 50)));
        $sort = (string) $request->input('sort', 'match');

        $query = Job::query()
            ->where('status', Job::STATUS_ACTIVE);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where('title', 'like', $like)
                    ->orWhere('company_name', 'like', $like)
                    ->orWhere('location', 'like', $like)
                    ->orWhere('job_type', 'like', $like)
                    ->orWhere('work_mode', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        if ($salaryBands !== []) {
            $query->where(function ($q) use ($salaryBands) {
                foreach ($salaryBands as $band) {
                    $q->orWhere(function ($sub) use ($band) {
                        match ($band) {
                            '100-150' => $sub->where(function ($inner) {
                                $inner->whereBetween('min_salary', [100000, 150000])
                                    ->orWhereBetween('max_salary', [100000, 150000])
                                    ->orWhere(function ($overlap) {
                                        $overlap->where('min_salary', '<=', 150000)
                                            ->where('max_salary', '>=', 100000);
                                    });
                            }),
                            '150-200' => $sub->where(function ($inner) {
                                $inner->whereBetween('min_salary', [150000, 200000])
                                    ->orWhereBetween('max_salary', [150000, 200000])
                                    ->orWhere(function ($overlap) {
                                        $overlap->where('min_salary', '<=', 200000)
                                            ->where('max_salary', '>=', 150000);
                                    });
                            }),
                            '200-plus' => $sub->where(function ($inner) {
                                $inner->where('min_salary', '>=', 200000)
                                    ->orWhere('max_salary', '>=', 200000);
                            }),
                            default => null,
                        };
                    });
                }
            });
        }

        if ($jobTypes !== []) {
            $standardTypes = array_values(array_diff($jobTypes, ['Remote']));
            $wantsRemote = in_array('Remote', $jobTypes, true);

            $query->where(function ($q) use ($standardTypes, $wantsRemote) {
                if ($standardTypes !== []) {
                    $q->whereIn('job_type', $standardTypes);
                }
                if ($wantsRemote) {
                    $method = $standardTypes !== [] ? 'orWhere' : 'where';
                    $q->{$method}(function ($remote) {
                        $remote->where('work_mode', 'Remote')
                            ->orWhere('location', 'like', '%Remote%');
                    });
                }
            });
        }

        if ($distance < 50 && $distance <= 25) {
            $query->where(function ($q) {
                $q->whereNull('work_mode')
                    ->orWhereIn('work_mode', ['On-site', 'Hybrid']);
            });
        }

        $candidate = $request->user()->candidate;

        if ($sort === 'match') {
            // Fetch all matching jobs (without pagination first) to score+sort them
            $allJobs = $query->latest('id')->get();
            $allJobs = $allJobs->map(function ($job) use ($candidate) {
                $job->match_score = $this->jobMatchService->percentage($job, $candidate);
                return $job;
            })->sortByDesc('match_score')->values();

            // Manual pagination from the sorted collection
            $page      = (int) $request->input('page', 1);
            $perPage   = 6;
            $slice     = $allJobs->slice(($page - 1) * $perPage, $perPage)->values();
            $jobs      = new \Illuminate\Pagination\LengthAwarePaginator(
                $slice,
                $allJobs->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            match ($sort) {
                'salary' => $query->orderByDesc('max_salary')->orderByDesc('min_salary')->latest('id'),
                default  => $query->latest(),
            };

            $jobs = $query->paginate(6)->withQueryString();
            $jobs->getCollection()->transform(function ($job) use ($candidate) {
                $job->match_score = $this->jobMatchService->percentage($job, $candidate);
                return $job;
            });
        }

        return view('pages.job_recommendations', [
            'activeNav'  => 'jobs',
            'jobs'       => $jobs,
            'search'     => $search,
            'salaryBands'=> $salaryBands,
            'jobTypes'   => $jobTypes,
            'distance'   => $distance,
            'sort'       => $sort,
        ]);
    }
}
