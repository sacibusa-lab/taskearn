<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Leaderboard rewards — run Sunday midnight for weekly, 1st of month for monthly
Schedule::command('leaderboard:reward week')->weekly()->sundays()->at('00:00');
Schedule::command('leaderboard:reward month')->monthlyOn(1, '00:00');
