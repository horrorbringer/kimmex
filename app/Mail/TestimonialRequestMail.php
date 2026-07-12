<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TestimonialRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $submitUrl;

    public function __construct(
        public Project $project,
        public string $clientName,
        public string $clientEmail,
    ) {
        $this->submitUrl = URL::signedRoute('testimonials.submit', [
            'project' => $this->project->slug,
            'email' => $this->clientEmail,
        ]);
    }

    public function envelope(): Envelope
    {
        $projectTitle = $this->project->getTranslation('title', 'en');

        return new Envelope(
            subject: "We'd Love Your Feedback — {$projectTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.testimonial-request',
            with: [
                'clientName' => $this->clientName,
                'projectTitle' => $this->project->getTranslation('title', 'en'),
                'submitUrl' => $this->submitUrl,
            ],
        );
    }
}
