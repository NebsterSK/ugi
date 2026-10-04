<?php

namespace App\Console\Commands;

use App\Actions\SniffFilter;
use App\Mail\NewEntriesFound;
use App\Models\Entry;
use App\Models\Filter;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class Sniff extends Command
{
    protected $signature = 'sniff';

    protected $description = 'Sniff us a new home.';

    public function handle(SniffFilter $sniffFilter): int
    {
        $filters = Filter::active()->get();

        if ($filters->isEmpty()) {
            $this->warn('No active filters, nothing to sniff.');

            return self::SUCCESS;
        }

        $startedAt = now()->startOfSecond();

        $filters->each(fn (Filter $filter) => $sniffFilter->handle($filter, $this->output));

        $this->notifyAboutNewEntries($startedAt);

        return self::SUCCESS;
    }

    protected function notifyAboutNewEntries(Carbon $since): void
    {
        $newEntries = Entry::query()->where('created_at', '>=', $since)->orderBy('id')->get();

        if ($newEntries->isEmpty()) {
            $this->info('No new entries found.');

            return;
        }

        $recipient = config('mail.notification_recipient');

        if (! $recipient) {
            $this->warn('Found '.$newEntries->count().' new entries, but MAIL_NOTIFICATION_RECIPIENT is not set.');

            return;
        }

        Mail::to($recipient)->send(new NewEntriesFound($newEntries));

        $this->info('Sent notification about '.$newEntries->count().' new entries to '.$recipient.'.');
    }
}
