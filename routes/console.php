<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\RecordatorioCitasJob;
use App\Jobs\RevisarInventarioJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new RecordatorioCitasJob)
    ->dailyAt('08:00');

Schedule::job(new RevisarInventarioJob)
    ->dailyAt('08:05');