<?php

use App\Jobs\SyncAmpiPropertiesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SyncAmpiPropertiesJob)
    ->everyFourHours()
    ->timezone('America/Mexico_City')
    ->withoutOverlapping(240)
    ->name('ampi-properties-sync');
