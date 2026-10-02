<?php

namespace App\Jobs;

use App\Actions\SniffFilter;
use App\Models\Filter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SniffFilterJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Filter $filter)
    {
    }

    public function handle(SniffFilter $sniffFilter): void
    {
        $sniffFilter->handle($this->filter);
    }
}
