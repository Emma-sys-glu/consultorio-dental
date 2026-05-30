<?php

namespace App\Jobs;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RecordatorioCitasJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $mañana = Carbon::tomorrow()->toDateString();

        $citas = Cita::with(['paciente', 'dentista'])
            ->where('fecha', $mañana)
            ->where('estado', '!=', 'cancelada')
            ->get();

        foreach ($citas as $cita) {

            Log::info(
                'RECORDATORIO CITA | Paciente: ' .
                $cita->paciente->nombre .
                ' | Dentista: ' .
                $cita->dentista->nombre .
                ' | Fecha: ' .
                $cita->fecha .
                ' ' .
                $cita->hora_inicio
            );
        }
    }
}