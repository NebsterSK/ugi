<?php

namespace App\Console\Commands;

use App\Actions\SniffFilter;
use App\Models\Filter;
use Illuminate\Console\Command;

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

        $filters->each(fn (Filter $filter) => $sniffFilter->handle($filter, $this->output));

        return self::SUCCESS;
    }
}
