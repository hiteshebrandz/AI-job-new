<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationSettingsController extends Controller
{
    private const ALLOWED_KEYS = [
        'job_alerts',
        'application_updates',
        'status_changes',
        'interview_invites',
        'weekly_digest',
        'new_applicants',     // HR only
        'job_performance',    // HR only
    ];

    public function showUser(): View
    {
        return view('pages.email_notification_settings', [
            'activeNav'           => 'settings',
            'notificationSettings'=> auth()->user()->notification_settings ?? [],
        ]);
    }

    public function saveUser(Request $request): RedirectResponse
    {
        return $this->saveSettings($request, self::ALLOWED_KEYS);
    }

    public function showHr(): View
    {
        return view('pages.email_notification_settings', [
            'activeNav'           => 'settings',
            'notificationSettings'=> auth()->user()->notification_settings ?? [],
        ]);
    }

    public function saveHr(Request $request): RedirectResponse
    {
        return $this->saveSettings($request, self::ALLOWED_KEYS);
    }

    private function saveSettings(Request $request, array $allowedKeys): RedirectResponse
    {
        $settings = [];
        foreach ($allowedKeys as $key) {
            $settings[$key] = $request->boolean($key);
        }

        auth()->user()->update(['notification_settings' => $settings]);

        return back()->with('status', 'Notification preferences saved.');
    }
}
