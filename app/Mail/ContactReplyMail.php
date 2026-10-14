<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Your reply to a visitor, sent from your Gmail as a plain, personal email. */
class ContactReplyMail extends Mailable
{
    public function __construct(
        public Message $original,
        public string $replyBody,
    ) {}

    public function envelope(): Envelope
    {
        $ownAddress = config('portfolio.contact_email') ?: config('mail.from.address');

        return new Envelope(
            subject: 'Re: '.$this->original->subject,
            replyTo: [new Address($ownAddress, (string) config('mail.from.name'))],
        );
    }

    public function content(): Content
    {
        return new Content(text: 'mail.contact-reply');
    }
}
