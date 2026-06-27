<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ampi:sync-properties')
    ->everyFourHours()
    ->timezone('America/Mexico_City')
    ->withoutOverlapping(240)
    ->name('ampi-properties-sync');
