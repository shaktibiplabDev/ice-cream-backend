<?php

namespace App\Mail;

use App\Models\Inquiry;
use App\Models\InquiryMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Inquiry $inquiry,
        public InquiryMessage $replyMessage,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address($this->replyMessage->sender_email, $this->replyMessage->sender_name ?: config('mail.from.name')),
            ],
            subject: $this->replyMessage->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiries.reply',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
