<?php

namespace App\Console\Commands;

use App\Actions\SniffFilter;
use App\Enums\SniffRunStatus;
use App\Mail\NewEntriesFound;
use App\Mail\SniffFailed;
use App\Models\Entry;
use App\Models\Filter;
use App\Models\SniffRun;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Throwable;

class Sniff extends Command
{
    protected $signature = 'sniff';

    protected $description = 'Sniff us a new home.';

    public function handle(SniffFilter $sniffFilter): int
    {
        $startedAt = now()->startOfSecond();
        $sniffRun = null;

        try {
            $sniffRun = SniffRun::create([
                'status' => SniffRunStatus::Running,
                'started_at' => $startedAt,
            ]);

            $filters = Filter::active()->get();

            if ($filters->isEmpty()) {
                $this->warn('No active filters, nothing to sniff.');

                $sniffRun->update([
                    'status' => SniffRunStatus::Warning,
                    'error' => 'No active filters, nothing to sniff.',
                    'finished_at' => now(),
                ]);

                return self::SUCCESS;
            }

            $counts = ['saved' => 0, 'skipped' => 0, 'failed' => 0];

            foreach ($filters as $filter) {
                foreach ($sniffFilter->handle($filter, $this->output) as $key => $count) {
                    $counts[$key] += $count;
                }
            }

            $newEntriesCount = $this->notifyAboutNewEntries($startedAt);

            [$status, $error] = $this->evaluate($counts);

            $sniffRun->update([
                'status' => $status,
                'filters_count' => $filters->count(),
                'saved_count' => $counts['saved'],
                'skipped_count' => $counts['skipped'],
                'failed_count' => $counts['failed'],
                'new_entries_count' => $newEntriesCount,
                'error' => $error,
                'finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            report($e);

            $error = $e::class.': '.$e->getMessage();

            rescue(fn () => $sniffRun?->update([
                'status' => SniffRunStatus::Failed,
                'error' => $error,
                'finished_at' => now(),
            ]), report: false);

            $this->error('Sniff failed: '.$error);

            $this->notifyAboutFailure($error, $startedAt);

            return self::FAILURE;
        }

        if ($status === SniffRunStatus::Failed) {
            $this->error('Sniff failed: '.$error);

            $this->notifyAboutFailure($error, $startedAt);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Status and problem description of a run that finished with the given listing counts.
     *
     * @param  array{saved: int, skipped: int, failed: int}  $counts
     * @return array{0: SniffRunStatus, 1: string|null}
     */
    protected function evaluate(array $counts): array
    {
        if ($counts['failed'] > 0 && $counts['saved'] === 0) {
            return [SniffRunStatus::Failed, 'None of the '.$counts['failed'].' listings could be parsed or saved, the listing markup has probably changed.'];
        }

        if ($counts['failed'] > 0) {
            return [SniffRunStatus::Warning, $counts['failed'].' listings could not be parsed or saved.'];
        }

        if ($counts['saved'] === 0 && $counts['skipped'] === 0) {
            return [SniffRunStatus::Warning, 'No listings found, the listing markup may have changed.'];
        }

        return [SniffRunStatus::Succeeded, null];
    }

    /**
     * Email the entries created since the given time and return how many there were.
     */
    protected function notifyAboutNewEntries(Carbon $since): int
    {
        $newEntries = Entry::query()->where('created_at', '>=', $since)->orderBy('id')->get();

        if ($newEntries->isEmpty()) {
            $this->info('No new entries found.');

            return 0;
        }

        $recipient = config('mail.notification_recipient');

        if (! $recipient) {
            $this->warn('Found '.$newEntries->count().' new entries, but MAIL_NOTIFICATION_RECIPIENT is not set.');

            return $newEntries->count();
        }

        Mail::to($recipient)->send(new NewEntriesFound($newEntries));

        $this->info('Sent notification about '.$newEntries->count().' new entries to '.$recipient.'.');

        return $newEntries->count();
    }

    protected function notifyAboutFailure(string $error, Carbon $startedAt): void
    {
        $recipient = config('mail.notification_recipient');

        if (! $recipient) {
            $this->warn('Sniff failed, but MAIL_NOTIFICATION_RECIPIENT is not set.');

            return;
        }

        rescue(fn () => Mail::to($recipient)->send(new SniffFailed($error, $startedAt)));
    }
}
