<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public JobApplication $application,
    ) {}

    public function envelope(): Envelope
    {
        $jobTitle = $this->application->job->title ?? 'your application';

        return new Envelope(
            subject: "Application Update: {$jobTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-status',
        );
    }
}
