<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ampi:sync-properties --delete-missing')
    ->dailyAt('02:00')
    ->timezone('America/Mexico_City')
    ->withoutOverlapping(360)
    ->appendOutputTo(storage_path('logs/ampi-sync.log'))
    ->name('ampi-properties-sync');
