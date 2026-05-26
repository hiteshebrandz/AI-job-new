<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Resume;
use App\Models\User;
use App\Services\JobRecommendationService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function landing(): View
    {
        $stats = [
            'candidates'   => Candidate::count(),
            'activeJobs'   => Job::where('status', Job::STATUS_ACTIVE)->count(),
            'companies'    => Company::count(),
            'applications' => JobApplication::count(),
        ];

        $featuredJobs = Job::where('status', Job::STATUS_ACTIVE)
            ->select(['id', 'title', 'company_name', 'location', 'job_type', 'work_mode', 'experience_required', 'created_at'])
            ->latest()
            ->limit(6)
            ->get();

        return view('pages.landing_page', compact('stats', 'featuredJobs'));
    }

    public function resumeAnalytics(JobRecommendationService $jobRecommendations): View
    {
        $user      = auth()->user();
        $candidate = $user->candidate;

        // Load latest resume from the AI analytics flow
        $latestResume = Resume::where('user_id', $user->id)->latest()->first();
        $analytics    = null;
        $resumeStatus = 'none';

        if ($latestResume) {
            if ($latestResume->isCompleted()) {
                $analytics    = $latestResume->analytics;
                $resumeStatus = $analytics ? 'completed' : 'failed';
            } elseif ($latestResume->isFailed()) {
                $resumeStatus = 'failed';
            } else {
                $resumeStatus = 'processing';
            }
        }

        // Legacy application data (still used for application_count fallback)
        $applications = $user->jobApplications()->with('job')->latest('applied_at')->get();

        $recommendedJobs = $resumeStatus === 'completed'
            ? $jobRecommendations->topMatchesForUser($user, 6)
            : collect();

        $appliedJobIds = $user->jobApplications()->pluck('job_id')->all();

        return view('pages.resume_analytics_dashboard', [
            'activeNav'        => 'analytics',
            'candidate'        => $candidate,
            'resumeStatus'     => $resumeStatus,
            'latestResume'     => $latestResume,
            'analytics'        => $analytics,
            'recommendedJobs'  => $recommendedJobs,
            'appliedJobIds'    => $appliedJobIds,
            'aiScore'          => $analytics?->ai_score ?? ($candidate?->ai_score ?? 0),
            'topMatchScore'    => $recommendedJobs->max('match_score')
                ?? $analytics?->top_match_percentage
                ?? ($applications->max('match_score') ?? 0),
            'skillCount'       => $analytics?->skill_count ?? count($candidate?->skills ?? []),
            'applicationCount' => $analytics?->application_count ?? $applications->count(),
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
