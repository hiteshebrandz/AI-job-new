<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function user(): View
    {
        $user      = auth()->user();
        $candidate = $user->candidate;

        $appliedCount  = $user->jobApplications()->count();
        $savedCount    = $user->savedJobRecords()->count();
        $unreadNotifs  = $user->applicationNotifications()->where('is_read', false)->count();
        $matchScore    = $candidate?->ai_score ?? 0;

        // Active applications (not rejected/hired)
        $activeApplications = $user->jobApplications()
            ->whereNotIn('status', [JobApplication::STATUS_REJECTED, JobApplication::STATUS_HIRED])
            ->count();

        return view('dashboards.user', [
            'user'               => $user,
            'candidate'          => $candidate,
            'appliedCount'       => $appliedCount,
            'savedCount'         => $savedCount,
            'unreadNotifs'       => $unreadNotifs,
            'matchScore'         => $matchScore,
            'activeApplications' => $activeApplications,
        ]);
    }

    public function hr(): View
    {
        $user = auth()->user();

        $jobs = Job::query()
            ->where('hr_id', auth()->id())
            ->withCount('applications')
            ->latest()
            ->get();

        $activeJobs      = $jobs->where('status', Job::STATUS_ACTIVE)->count();
        $totalApplicants = $jobs->sum('applications_count');
        $draftJobs       = $jobs->where('status', Job::STATUS_INACTIVE)->count();

        // Recent applicants for HR's jobs
        $recentApplications = JobApplication::query()
            ->whereIn('job_id', $jobs->pluck('id'))
            ->with(['user.candidate', 'job'])
            ->latest('applied_at')
            ->take(5)
            ->get();

        return view('dashboards.hr', [
            'user'               => $user,
            'jobs'               => $jobs,
            'activeJobs'         => $activeJobs,
            'totalApplicants'    => $totalApplicants,
            'draftJobs'          => $draftJobs,
            'recentApplications' => $recentApplications,
        ]);
    }

    public function admin(): View
    {
        $totalUsers        = User::where('role', User::ROLE_USER)->count();
        $totalHr           = User::where('role', User::ROLE_HR)->count();
        $totalJobs         = Job::count();
        $activeJobs        = Job::where('status', Job::STATUS_ACTIVE)->count();
        $totalApplications = JobApplication::count();

        // Monthly registrations (last 6 months)
        $users = User::where('role', User::ROLE_USER)->orderBy('name')->get();
        $hrs   = User::where('role', User::ROLE_HR)->orderBy('name')->get();

        $recentApplications = JobApplication::query()
            ->with(['user.candidate', 'job'])
            ->latest('applied_at')
            ->take(10)
            ->get();

        return view('dashboards.admin', [
            'totalUsers'         => $totalUsers,
            'totalHr'            => $totalHr,
            'totalJobs'          => $totalJobs,
            'activeJobs'         => $activeJobs,
            'totalApplications'  => $totalApplications,
            'users'              => $users,
            'hrs'                => $hrs,
            'recentApplications' => $recentApplications,
        ]);
    }
}
