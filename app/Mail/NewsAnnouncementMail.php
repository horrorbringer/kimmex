<?php

namespace App\Mail;

use App\Models\NewsArticle;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsArticle $article,
        public Subscriber $subscriber,
        public string $customIntro = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->article->getTranslation('title', 'en') . ' — Kimmex News',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.news-announcement',
            with: [
                'title' => $this->article->getTranslation('title', 'en'),
                'excerpt' => $this->article->getTranslation('excerpt', 'en'),
                'coverImage' => $this->article->coverImage ? url($this->article->coverImage) : null,
                'articleUrl' => url('/news/' . $this->article->slug),
                'subscriberName' => $this->subscriber->name,
                'unsubscribeUrl' => url('/unsubscribe/' . $this->subscriber->unsubscribe_token),
                'customIntro' => $this->customIntro,
            ],
        );
    }
}
