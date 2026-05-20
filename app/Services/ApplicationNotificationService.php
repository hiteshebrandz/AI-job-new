<?php

namespace App\Services;

use App\Models\ApplicationNotification;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusNotification;

class ApplicationNotificationService
{
    public function notifyStatusChange(JobApplication $application): ApplicationNotification
    {
        $application->loadMissing('job', 'user');

        $jobTitle    = $application->job->title;
        $statusLabel = JobApplication::statusLabel($application->status);

        $message = match ($application->status) {
            JobApplication::STATUS_SHORTLISTED => "Your application for {$jobTitle} has been shortlisted.",
            JobApplication::STATUS_INTERVIEW   => "Your interview for {$jobTitle} has been scheduled.",
            JobApplication::STATUS_REJECTED    => "Your application for {$jobTitle} was not selected at this time.",
            JobApplication::STATUS_HIRED       => "Congratulations! You have been hired for {$jobTitle}.",
            default                            => "Your application for {$jobTitle} is now: {$statusLabel}.",
        };

        $notification = ApplicationNotification::create([
            'user_id'            => $application->user_id,
            'job_application_id' => $application->id,
            'message'            => $message,
        ]);

        // Also dispatch a queued Laravel notification (email + database)
        try {
            $application->user->notify(new ApplicationStatusNotification($application));
        } catch (\Throwable) {
            // Queue may be unavailable in local dev.
        }

        return $notification;
    }
}
