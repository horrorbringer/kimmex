<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAutoReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Inquiry $inquiry,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('We received your message — Kimmex'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-auto-reply',
            with: [
                'name' => $this->inquiry->name,
                'subject' => $this->inquiry->subject,
                'message' => $this->inquiry->message,
            ],
        );
    }
}
