<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParentAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $parentName,
        public string $parentEmail,
        public string $password,
        public string $studentName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun Portal Orang Tua - InvestaSchool',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.parent-account',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

