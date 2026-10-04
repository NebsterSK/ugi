<?php

namespace App\Mail;

use App\Models\Entry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Str;

class NewEntriesFound extends Mailable
{
    /**
     * @param  Collection<int, Entry>  $entries
     */
    public function __construct(public Collection $entries)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->entries->count().' new '.Str::plural('apartment', $this->entries->count()).' found',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-entries-found',
        );
    }
}
