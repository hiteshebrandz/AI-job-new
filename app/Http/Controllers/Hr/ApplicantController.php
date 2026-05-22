<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\ApplicationNotificationService;
use App\Services\JobMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicantController extends Controller
{
    public function __construct(
        private ApplicationNotificationService $notifications,
        private JobMatchService $jobMatchService,
    ) {}

    /**
     * List all job seekers (users with role candidate).
     */
    public function index(Request $request): View
    {
        $hrJobIds = Job::where('hr_id', auth()->id())->pluck('id');

        $query = User::query()
            ->where('role', User::ROLE_USER)
            ->with('candidate')
            ->withCount([
                'jobApplications as applications_count',
                'jobApplications as hr_applications_count' => function ($q) use ($hrJobIds) {
                    $q->whereIn('job_id', $hrJobIds);
                },
            ]);

        if ($search = trim((string) $request->input('search'))) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhereHas('candidate', function ($c) use ($like) {
                        $c->where('full_name', 'like', $like)
                            ->orWhere('current_title', 'like', $like)
                            ->orWhere('location', 'like', $like);
                    });
            });
        }

        if ($request->boolean('applied_only')) {
            $query->whereHas('jobApplications', fn ($q) => $q->whereIn('job_id', $hrJobIds));
        }

        $jobSeekers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('pages.hr.job_seekers_index', [
            'activeNav'  => 'candidates',
            'jobSeekers' => $jobSeekers,
            'filters'    => $request->only(['search', 'applied_only']),
            'totalCount' => User::where('role', User::ROLE_USER)->count(),
        ]);
    }

    /**
     * Full profile page for a job seeker.
     */
    public function showJobSeeker(Request $request, User $user): View
    {
        abort_unless($user->role === User::ROLE_USER, 404);

        $hrJobIds = Job::where('hr_id', auth()->id())->pluck('id');

        $user->load('candidate');

        $applications = JobApplication::query()
            ->where('user_id', $user->id)
            ->whereIn('job_id', $hrJobIds)
            ->with('job')
            ->latest('applied_at')
            ->get();

        $candidate = $user->candidate;

        return view('pages.hr.job_seeker_show', [
            'activeNav'     => 'candidates',
            'jobSeeker'     => $user,
            'candidate'     => $candidate,
            'applications'  => $applications,
            'statuses'      => JobApplication::statuses(),
        ]);
    }

    public function downloadResume(User $user): StreamedResponse
    {
        abort_unless($user->role === User::ROLE_USER, 404);

        $candidate = $user->candidate;
        abort_unless($candidate?->resume_path && Storage::disk('local')->exists($candidate->resume_path), 404);

        return Storage::disk('local')->download(
            $candidate->resume_path,
            basename($candidate->resume_path)
        );
    }

    /**
     * Update application status (AJAX).
     */
    public function updateStatus(Request $request, JobApplication $application): JsonResponse
    {
        abort_unless(
            Job::where('id', $application->job_id)->where('hr_id', auth()->id())->exists(),
            403
        );

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(JobApplication::statuses()))],
        ]);

        $application->update(['status' => $validated['status']]);
        $this->notifications->notifyStatusChange($application->fresh(['job', 'user']));

        return response()->json([
            'success'      => true,
            'message'      => 'Application status updated.',
            'status'       => $application->status,
            'status_label' => JobApplication::statusLabel($application->status),
        ]);
    }

    /**
     * Return JSON details of a single application (for modal/drawer).
     */
    public function showApplication(JobApplication $application): JsonResponse
    {
        abort_unless(
            Job::where('id', $application->job_id)->where('hr_id', auth()->id())->exists(),
            403
        );

        $application->load(['user.candidate', 'job']);
        $candidate = $application->user->candidate;

        return response()->json([
            'success'     => true,
            'application' => [
                'id'           => $application->id,
                'status'       => $application->status,
                'status_label' => JobApplication::statusLabel($application->status),
                'match_score'  => $application->match_score ?? $this->jobMatchService->percentage($application->job, $candidate),
                'applied_at'   => $application->applied_at?->format('M j, Y'),
                'candidate'    => [
                    'name'       => $candidate?->full_name ?? $application->user->name,
                    'email'      => $application->user->email,
                    'phone'      => $candidate?->phone,
                    'title'      => $candidate?->current_title,
                    'experience' => $candidate?->experience_years,
                    'skills'     => $candidate?->skills ?? [],
                    'education'  => $candidate?->education,
                    'university' => $candidate?->university,
                    'ai_score'   => $candidate?->ai_score,
                ],
                'job' => [
                    'title'        => $application->job->title,
                    'company_name' => $application->job->company_name,
                ],
            ],
        ]);
    }
}
