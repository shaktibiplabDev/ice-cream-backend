<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminInquiryReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address($this->inquiry->email, $this->inquiry->name),
            ],
            subject: 'New Celesty inquiry ' . $this->inquiry->displayNumber(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiries.admin-received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
