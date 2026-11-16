<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Carbon;

class SniffFailed extends Mailable
{
    public function __construct(public string $error, public Carbon $startedAt)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sniff failed',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.sniff-failed',
        );
    }
}
