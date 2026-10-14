<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * "You have a new message" email that lands in your Gmail.
 * Hitting Reply in Gmail answers the visitor directly (Reply-To is set).
 */
class ContactFormMail extends Mailable
{
    // Property names avoid the ones Laravel's Mailable already uses (subject, from, to…).
    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $topic,
        public ?string $body,
        public ?string $dashboardUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Portfolio Contact: {$this->topic}",
            replyTo: [new Address($this->senderEmail, $this->senderName)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.contact-form');
    }
}
