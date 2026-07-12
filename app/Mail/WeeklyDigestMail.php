<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $articles,
        public Collection $projects,
        public Subscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kimmex Weekly Digest — ' . now()->format('M d, Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-digest',
            with: [
                'articles' => $this->articles,
                'projects' => $this->projects,
                'subscriberName' => $this->subscriber->name,
                'unsubscribeUrl' => url('/unsubscribe/' . $this->subscriber->unsubscribe_token),
            ],
        );
    }
}
