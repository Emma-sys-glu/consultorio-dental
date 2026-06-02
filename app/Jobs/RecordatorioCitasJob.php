<?php

namespace App\Jobs;

use App\Models\Cita;
use App\Models\Notificacion;
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
        $manana = Carbon::tomorrow()->toDateString();

        $citas = Cita::with(['paciente', 'dentista'])
            ->whereDate('fecha', $manana)
            ->where('estado', '!=', 'cancelada')
            ->get();

        foreach ($citas as $cita) {
            if (!$cita->paciente || !$cita->dentista) {
                continue;
            }

            $mensaje = 'Tienes una cita mañana a las ' .
                $cita->hora_inicio .
                ' con el dentista ' .
                $cita->dentista->nombre . ' ' .
                $cita->dentista->apellido_paterno . '.';

            $existe = Notificacion::where('paciente_id', $cita->paciente_id)
                ->where('tipo', 'cita')
                ->where('titulo', 'Recordatorio de cita')
                ->where('mensaje', $mensaje)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if (!$existe) {
                Notificacion::create([
                    'paciente_id' => $cita->paciente_id,
                    'tipo' => 'cita',
                    'titulo' => 'Recordatorio de cita',
                    'mensaje' => $mensaje,
                ]);

                Log::info('RECORDATORIO CITA creado para cita ID: ' . $cita->id);
            }
        }
    }
}