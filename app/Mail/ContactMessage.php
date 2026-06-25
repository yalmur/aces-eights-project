<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $senderName,
        public readonly string $senderEmail,
        public readonly string $messageSubject,
        public readonly string $body,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->senderEmail],
            subject: '[Contact] ' . $this->messageSubject . ' — ' . $this->senderName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: ['subject' => $this->messageSubject],
        );
    }
}
