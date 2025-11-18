<?php

namespace App\Console\Commands;

use App\Models\Entry;
use Illuminate\Console\Command;

class Bury extends Command
{
    protected $signature = 'bury';
    protected $description = 'Bury old entries.';

    public function handle(): int
    {
        Entry::ignored()->delete();

        Entry::notIgnored()->where('updated_at', '<', now()->subHours(25))->delete();

        return self::SUCCESS;
    }
}
