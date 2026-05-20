<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Support\CandidateJobProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorInstance;
use Illuminate\Support\Collection;

class JobRecommendationService
{
    public function __construct(
        private CandidateProfileBuilder $profileBuilder,
        private JobMatchService $jobMatchService,
    ) {}

    /**
     * @return array{
     *     jobs: LengthAwarePaginator,
     *     profile: CandidateJobProfile,
     *     applied_job_ids: array<int, int>
     * }
     */
    public function recommendForUser(User $user, Request $request, int $perPage = 6): array
    {
        $profile = $this->profileBuilder->forUser($user);
        $appliedJobIds = JobApplication::query()
            ->where('user_id', $user->id)
            ->pluck('job_id')
            ->all();

        $query = $this->filteredJobsQuery($request);
        $sort = (string) $request->input('sort', 'match');

        if ($sort === 'match') {
            $jobs = $this->paginateByMatchScore($query, $profile, $request, $perPage);
        } else {
            match ($sort) {
                'salary' => $query->orderByDesc('max_salary')->orderByDesc('min_salary')->latest('id'),
                default  => $query->latest(),
            };
            $paginator = $query->paginate($perPage)->withQueryString();
            $paginator->getCollection()->transform(function (Job $job) use ($profile) {
                $job->match_score = $this->jobMatchService->percentage($job, null, $profile);
                $job->match_reason = $this->jobMatchService->matchReason($job, $profile);

                return $job;
            });
            $jobs = $paginator;
        }

        return [
            'jobs' => $jobs,
            'profile' => $profile,
            'applied_job_ids' => $appliedJobIds,
        ];
    }

    /**
     * Top N jobs for dashboard widgets (no pagination).
     *
     * @return Collection<int, Job>
     */
    public function topMatchesForUser(User $user, int $limit = 5): Collection
    {
        $profile = $this->profileBuilder->forUser($user);

        $jobs = Job::query()
            ->where('status', Job::STATUS_ACTIVE)
            ->latest('id')
            ->limit(100)
            ->get();

        return $jobs
            ->map(function (Job $job) use ($profile) {
                $job->match_score = $this->jobMatchService->percentage($job, null, $profile);
                $job->match_reason = $this->jobMatchService->matchReason($job, $profile);

                return $job;
            })
            ->sortByDesc('match_score')
            ->take($limit)
            ->values();
    }

    private function filteredJobsQuery(Request $request): Builder
    {
        $search = trim((string) $request->input('search', ''));
        $salaryBands = array_values(array_filter((array) $request->input('salary', [])));
        $jobTypes = array_values(array_filter((array) $request->input('job_types', [])));
        $distance = max(5, min(50, (int) $request->input('distance', 50)));

        $query = Job::query()->where('status', Job::STATUS_ACTIVE);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where('title', 'like', $like)
                    ->orWhere('company_name', 'like', $like)
                    ->orWhere('location', 'like', $like)
                    ->orWhere('job_type', 'like', $like)
                    ->orWhere('work_mode', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('skills_required', 'like', $like);
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

        return $query;
    }

    /**
     * @param  array<int, int>  $appliedJobIds
     */
    private function paginateByMatchScore(
        Builder $query,
        CandidateJobProfile $profile,
        Request $request,
        int $perPage,
    ): LengthAwarePaginator {
        $allJobs = $query->latest('id')->limit(250)->get();

        $scored = $allJobs->map(function (Job $job) use ($profile) {
            $job->match_score = $this->jobMatchService->percentage($job, null, $profile);
            $job->match_reason = $this->jobMatchService->matchReason($job, $profile);

            return $job;
        })->sortByDesc('match_score')->values();

        $page = max(1, (int) $request->input('page', 1));
        $slice = $scored->slice(($page - 1) * $perPage, $perPage)->values();

        return new PaginatorInstance(
            $slice,
            $scored->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}
