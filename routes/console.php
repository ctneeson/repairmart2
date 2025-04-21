<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ExpireListings;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('listings:expire')
    ->description('Expire listings that have reached their expiration date')
    ->dailyAt('00:00')
    ->appendOutputTo(storage_path('logs/scheduler.log'));
