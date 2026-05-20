<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Admin analytics JSON — consumed by Chart.js on the admin analytics dashboard.
     */
    public function adminData(): JsonResponse
    {
        $months = 6;

        // Monthly registrations (users + hr)
        $registrations = User::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        // Monthly applications
        $applications = JobApplication::query()
            ->select(DB::raw("DATE_FORMAT(applied_at, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->where('applied_at', '>=', now()->subMonths($months)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        // Monthly job posts
        $jobPosts = Job::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        // Build month labels
        $labels = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $labels[] = now()->subMonths($i)->format('Y-m');
        }

        return response()->json([
            'success' => true,
            'labels'  => array_map(fn ($m) => date('M Y', strtotime($m . '-01')), $labels),
            'registrations_by_month' => array_map(fn ($m) => (int) ($registrations[$m] ?? 0), $labels),
            'applications_by_month'  => array_map(fn ($m) => (int) ($applications[$m] ?? 0), $labels),
            'jobs_by_month'          => array_map(fn ($m) => (int) ($jobPosts[$m] ?? 0), $labels),
            'totals' => [
                'users'        => User::where('role', User::ROLE_USER)->count(),
                'hr'           => User::where('role', User::ROLE_HR)->count(),
                'jobs'         => Job::count(),
                'active_jobs'  => Job::where('status', Job::STATUS_ACTIVE)->count(),
                'applications' => JobApplication::count(),
            ],
            'status_breakdown' => JobApplication::query()
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
        ]);
    }

    /**
     * Resume analytics JSON — consumed by Chart.js on the candidate analytics dashboard.
     */
    public function resumeData(): JsonResponse
    {
        $user      = auth()->user();
        $candidate = $user->candidate;

        $applications = $user->jobApplications()->with('job')->latest('applied_at')->get();

        $statusBreakdown = $applications->groupBy('status')->map->count();

        $topMatches = $applications
            ->sortByDesc('match_score')
            ->take(5)
            ->map(fn ($a) => [
                'job_title'   => $a->job->title ?? '—',
                'company'     => $a->job->company_name ?? '—',
                'match_score' => $a->match_score ?? 0,
                'status'      => $a->status,
            ])
            ->values();

        return response()->json([
            'success'         => true,
            'ai_score'        => $candidate?->ai_score ?? 0,
            'skill_count'     => count($candidate?->skills ?? []),
            'skills'          => $candidate?->skills ?? [],
            'application_count' => $applications->count(),
            'top_match_score' => $applications->max('match_score') ?? 0,
            'avg_match_score' => $applications->count()
                ? (int) round($applications->avg('match_score'))
                : 0,
            'status_breakdown' => $statusBreakdown,
            'top_matches'      => $topMatches,
        ]);
    }
}
