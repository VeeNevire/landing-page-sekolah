<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $className,
        public string $programName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat! Anda Resmi Diterima - SMK MADYA DEPOK',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-accepted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
