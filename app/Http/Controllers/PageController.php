<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function landing(): View
    {
        return view('pages.landing_page');
    }

    public function applicantManagement(): View
    {
        return view('pages.applicant_management', ['activeNav' => 'candidates']);
    }

    public function resumeAnalytics(): View
    {
        return view('pages.resume_analytics_dashboard', ['activeNav' => 'analytics']);
    }

    public function adminAnalytics(): View
    {
        return view('pages.admin_analytics_dashboard', ['activeNav' => 'analytics']);
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
