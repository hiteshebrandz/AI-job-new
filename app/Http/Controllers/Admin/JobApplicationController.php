<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Services\ApplicationNotificationService;
use App\Services\JobMatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    public function __construct(
        private ApplicationNotificationService $notifications,
        private JobMatchService $jobMatchService
    ) {}

    public function index(Request $request): View
    {
        $query = JobApplication::query()
            ->with(['user.candidate', 'job']);

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', $like)->orWhere('email', 'like', $like))
                    ->orWhereHas('job', fn ($j) => $j->where('title', 'like', $like)->orWhere('company_name', 'like', $like))
                    ->orWhereHas('user.candidate', fn ($c) => $c->where('skills', 'like', $like));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($jobId = $request->input('job_id')) {
            $query->where('job_id', $jobId);
        }

        if ($company = $request->input('company')) {
            $query->whereHas('job', fn ($j) => $j->where('company_name', 'like', '%'.$company.'%'));
        }

        if ($minScore = $request->input('min_score')) {
            $query->where('match_score', '>=', (int) $minScore);
        }

        if ($from = $request->input('from_date')) {
            $query->whereDate('applied_at', '>=', $from);
        }

        if ($to = $request->input('to_date')) {
            $query->whereDate('applied_at', '<=', $to);
        }

        $applications = $query->latest('applied_at')->paginate(15)->withQueryString();

        return view('pages.admin.job_applications.index', [
            'activeNav' => 'applications',
            'applications' => $applications,
            'statuses' => JobApplication::statuses(),
            'jobs' => Job::query()->orderBy('title')->get(['id', 'title', 'company_name']),
            'filters' => $request->only(['search', 'status', 'job_id', 'company', 'min_score', 'from_date', 'to_date']),
        ]);
    }

    public function show(JobApplication $application): View
    {
        $application->load(['user.candidate', 'job.hr']);

        $user = $application->user;
        $candidate = $user->candidate;

        return view('pages.admin.job_applications.show', [
            'activeNav' => 'applications',
            'application' => $application,
            'candidate' => $candidate,
            'statuses' => JobApplication::statuses(),
            'savedJobsCount' => $user->savedJobRecords()->count(),
            'appliedJobsCount' => $user->jobApplications()->count(),
            'matchScore' => $application->match_score ?? ($candidate && $application->job
                ? $this->jobMatchService->percentage($application->job, $candidate)
                : null),
        ]);
    }

    public function updateStatus(Request $request, JobApplication $application): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(JobApplication::statuses()))],
        ]);

        $application->update(['status' => $validated['status']]);
        $this->notifications->notifyStatusChange($application->fresh(['job', 'user']));

        return response()->json([
            'success' => true,
            'message' => 'Application status updated.',
            'status' => $application->status,
            'status_label' => JobApplication::statusLabel($application->status),
        ]);
    }

    public function downloadResume(JobApplication $application)
    {
        $candidate = $application->user->candidate;

        abort_unless($candidate?->resume_path && Storage::disk('local')->exists($candidate->resume_path), 404);

        return Storage::disk('local')->download(
            $candidate->resume_path,
            basename($candidate->resume_path)
        );
    }
}
