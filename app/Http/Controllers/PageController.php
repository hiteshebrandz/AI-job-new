<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\View\View;

class PageController extends Controller
{
    public function landing(): View
    {
        return view('pages.landing_page');
    }

    public function resumeAnalytics(): View
    {
        $user      = auth()->user();
        $candidate = $user->candidate;

        $applications = $user->jobApplications()->with('job')->latest('applied_at')->get();
        $statusBreakdown = $applications->groupBy('status')->map->count();

        return view('pages.resume_analytics_dashboard', [
            'activeNav'        => 'analytics',
            'aiScore'          => $candidate?->ai_score ?? 0,
            'skillCount'       => count($candidate?->skills ?? []),
            'skills'           => $candidate?->skills ?? [],
            'applicationCount' => $applications->count(),
            'topMatchScore'    => $applications->max('match_score') ?? 0,
            'avgMatchScore'    => $applications->count()
                ? (int) round($applications->avg('match_score'))
                : 0,
            'recentApps'       => $applications->take(5),
            'statusBreakdown'  => $statusBreakdown,
            'candidate'        => $candidate,
        ]);
    }

    public function adminAnalytics(): View
    {
        return view('pages.admin_analytics_dashboard', [
            'activeNav'        => 'analytics',
            'totalUsers'       => User::where('role', User::ROLE_USER)->count(),
            'totalHr'          => User::where('role', User::ROLE_HR)->count(),
            'totalJobs'        => Job::count(),
            'activeJobs'       => Job::where('status', Job::STATUS_ACTIVE)->count(),
            'totalApplications'=> JobApplication::count(),
        ]);
    }

    public function applicantManagement(): View
    {
        // Legacy stub — route now goes to Hr\ApplicantController
        return view('pages.applicant_management', ['activeNav' => 'candidates']);
    }

    public function emailNotificationSettings(): View
    {
        return view('pages.email_notification_settings', ['activeNav' => 'settings']);
    }

    public function executiveSuiteOne(): View
    {
        return view('pages.elements_hr_executive_suite_1');
    }

    public function executiveSuiteTwo(): View
    {
        return view('pages.elements_hr_executive_suite_2');
    }

    public function sitemap(): View
    {
        return view('pages.sitemap');
    }
}
