<?php

use App\Console\Commands\SyncBirthdayTodayCommand;
use App\Console\Commands\PruneExpiredSessionsCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SyncBirthdayTodayCommand::class)
    ->dailyAt('00:05')
    ->timezone(config('app.timezone'))
    ->withoutOverlapping();

Schedule::command(PruneExpiredSessionsCommand::class)
    ->hourly()
    ->timezone(config('app.timezone'))
    ->withoutOverlapping();
