<?php

namespace App\Mail;

use App\Enums\ApplicationStatus;
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
        public ApplicationStatus $status,
        public string $customMessage = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->status->emailSubject(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-status',
            with: [
                'applicantName' => $this->application->applicantName,
                'status' => $this->status,
                'jobTitle' => $this->application->job?->getTranslation('title', 'en') ?? 'General Application',
                'customMessage' => $this->customMessage,
            ],
        );
    }
}
