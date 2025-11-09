<?php

use App\Console\Commands\Sniff;
use Illuminate\Support\Facades\Schedule;

Schedule::command(Sniff::class)->dailyAt('05:03')->environments(['production']);
