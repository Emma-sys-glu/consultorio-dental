<?php

namespace App\Jobs;

use App\Models\Cita;
use App\Models\Notificacion;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RecordatorioHoraCitaJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Ventana: citas que empiezan entre 1h50m y 2h10m desde ahora
        $desde = Carbon::now()->addMinutes(110);
        $hasta = Carbon::now()->addMinutes(130);

        $citas = Cita::with(['paciente', 'dentista'])
            ->whereDate('fecha', Carbon::today())
            ->whereTime('hora_inicio', '>=', $desde->format('H:i:s'))
            ->whereTime('hora_inicio', '<=', $hasta->format('H:i:s'))
            ->whereNotIn('estado', ['cancelada'])
            ->get();

        foreach ($citas as $cita) {
            if (!$cita->paciente || !$cita->dentista) {
                continue;
            }

            $hora    = Carbon::parse($cita->hora_inicio)->format('H:i');
            $mensaje = "Tu cita de hoy es en aproximadamente 2 horas, a las {$hora} con el Dr. "
                . $cita->dentista->nombre . ' ' . $cita->dentista->apellido_paterno . '.';

            $yaExiste = Notificacion::where('paciente_id', $cita->paciente_id)
                ->where('tipo', 'recordatorio_2h')
                ->whereDate('created_at', Carbon::today())
                ->where('mensaje', 'like', '%' . $hora . '%')
                ->exists();

            if (!$yaExiste) {
                Notificacion::create([
                    'paciente_id' => $cita->paciente_id,
                    'tipo'        => 'recordatorio_2h',
                    'titulo'      => '⏰ Tu cita es en 2 horas',
                    'mensaje'     => $mensaje,
                ]);

                Log::info('[Recordatorio 2h] Cita ID ' . $cita->id . ' — paciente ' . $cita->paciente_id);
            }
        }
    }
}
