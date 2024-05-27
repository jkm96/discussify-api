<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:forum-statistics-command')
    ->everyMinute()
    ->runInBackground();

Schedule::command('app:clear-expired-tokens')
    ->dailyAt('01:00')
    ->runInBackground();
