<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactInquiry extends Mailable
{
    /** @param array{name: string, email: string, message: string, phone?: ?string, website?: ?string} $inquiry */
    public function __construct(public array $inquiry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->inquiry['email'])],
            subject: 'New WebWorksLab inquiry',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.contact-inquiry', text: 'mail.contact-inquiry-text');
    }
}
