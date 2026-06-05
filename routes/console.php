<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\RecordatorioCitasJob;
use App\Jobs\RevisarInventarioJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recordatorio 1 día antes — corre a las 8am todos los días
Schedule::job(new RecordatorioCitasJob)->dailyAt('08:00');

// Alertas de inventario: stock bajo y productos por caducar — corre a las 8:05am
Schedule::job(new RevisarInventarioJob)->dailyAt('08:05');
