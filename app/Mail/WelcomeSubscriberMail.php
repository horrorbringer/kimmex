<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeSubscriberMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Welcome to Kimmex Newsletter'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-subscriber',
            with: [
                'subscriberName' => $this->subscriber->name,
                'subscriberEmail' => $this->subscriber->email,
                'unsubscribeUrl' => url('/unsubscribe/' . $this->subscriber->unsubscribe_token),
                'websiteUrl' => url('/'),
                'projectsUrl' => url('/projects'),
                'newsUrl' => url('/news'),
            ],
        );
    }
}
