<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Services\ApplicationNotificationService;
use App\Services\JobMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicantController extends Controller
{
    public function __construct(
        private ApplicationNotificationService $notifications,
        private JobMatchService $jobMatchService,
    ) {}

    /**
     * Show the Kanban/list view of applicants for all of the HR's jobs,
     * or a specific job when `job_id` is passed.
     */
    public function index(Request $request): View
    {
        $hrJobIds = Job::where('hr_id', auth()->id())->pluck('id');

        $query = JobApplication::query()
            ->whereIn('job_id', $hrJobIds)
            ->with(['user.candidate', 'job']);

        // Filter by specific job
        if ($jobId = $request->input('job_id')) {
            abort_unless($hrJobIds->contains($jobId), 403);
            $query->where('job_id', $jobId);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by search (name / email / skill)
        if ($search = trim((string) $request->input('search'))) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', $like)->orWhere('email', 'like', $like))
                  ->orWhereHas('user.candidate', fn ($c) => $c->where('current_title', 'like', $like));
            });
        }

        $applications = $query->latest('applied_at')->paginate(20)->withQueryString();

        // Group by status for Kanban view
        $grouped = $applications->getCollection()->groupBy('status');

        // All HR jobs for the filter dropdown
        $jobs = Job::where('hr_id', auth()->id())
            ->orderBy('title')
            ->get(['id', 'title', 'company_name']);

        return view('pages.applicant_management', [
            'activeNav'    => 'candidates',
            'applications' => $applications,
            'grouped'      => $grouped,
            'statuses'     => JobApplication::statuses(),
            'jobs'         => $jobs,
            'filters'      => $request->only(['job_id', 'status', 'search']),
        ]);
    }

    /**
     * Update application status (AJAX).
     */
    public function updateStatus(Request $request, JobApplication $application): JsonResponse
    {
        // Ensure this application belongs to an HR-owned job
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
     * Return JSON details of a single applicant (for modal/drawer).
     */
    public function show(JobApplication $application): JsonResponse
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
                    'name'          => $candidate?->full_name ?? $application->user->name,
                    'email'         => $application->user->email,
                    'phone'         => $candidate?->phone,
                    'title'         => $candidate?->current_title,
                    'experience'    => $candidate?->experience_years,
                    'skills'        => $candidate?->skills ?? [],
                    'education'     => $candidate?->education,
                    'university'    => $candidate?->university,
                    'ai_score'      => $candidate?->ai_score,
                ],
                'job' => [
                    'title'        => $application->job->title,
                    'company_name' => $application->job->company_name,
                ],
            ],
        ]);
    }
}
