<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactInquiry extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

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
