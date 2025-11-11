<?php

use App\Console\Commands\Bury;
use App\Console\Commands\Sniff;
use Illuminate\Support\Facades\Schedule;

Schedule::command(Sniff::class)->dailyAt('05:03')->environments(['production']);

Schedule::command(Bury::class)->dailyAt('05:30')->environments(['production']);
