<?php

use App\Console\Commands\Crawl;
use Illuminate\Support\Facades\Schedule;

Schedule::command(Crawl::class)->dailyAt('05:01')->environments(['production']);
