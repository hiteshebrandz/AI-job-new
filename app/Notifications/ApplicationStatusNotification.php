<?php

namespace App\Notifications;

use App\Mail\ApplicationStatusMail;
use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): ApplicationStatusMail
    {
        return new ApplicationStatusMail($this->application);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_title'      => $this->application->job->title ?? null,
            'status'         => $this->application->status,
            'status_label'   => JobApplication::statusLabel($this->application->status),
        ];
    }
}
