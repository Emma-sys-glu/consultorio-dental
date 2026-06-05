<?php

namespace App\Console\Commands;

use App\Models\Cita;
use App\Models\Notificacion;
use App\Models\PushSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotificacionCitaTest extends Command
{
    protected $signature   = 'notif:cita {email : Email del paciente}';
    protected $description = 'Prueba notificacion de recordatorio de cita (1 dia antes) para un paciente';

    public function handle(): void
    {
        $email = $this->argument('email');

        $usuario = User::where('email', $email)->first();

        if (!$usuario) {
            $this->error("Usuario no encontrado: {$email}");
            return;
        }

        if ($usuario->rol !== 'paciente' || !$usuario->paciente_id) {
            $this->error("El usuario {$email} no es paciente o no tiene paciente vinculado.");
            return;
        }

        $this->line("Paciente: {$usuario->name} ({$usuario->email})");

        $tienePush = PushSubscription::where('user_id', $usuario->id)->exists();
        $this->line('Push activo: ' . ($tienePush ? 'si' : 'no — la notificacion se guarda pero no llega al navegador'));

        $cita = Cita::with('dentista')
            ->where('paciente_id', $usuario->paciente_id)
            ->whereNotIn('estado', ['cancelada'])
            ->orderBy('fecha')
            ->first();

        if ($cita) {
            $this->line("Cita encontrada: {$cita->fecha} {$cita->hora_inicio} — {$cita->motivo}");
        } else {
            $this->line('Sin citas registradas, se usara hora de ejemplo.');
        }

        $hora    = $cita ? Carbon::parse($cita->hora_inicio)->format('H:i') : '10:00';
        $drNombre = $cita?->dentista
            ? 'Dr. ' . $cita->dentista->nombre . ' ' . $cita->dentista->apellido_paterno
            : 'tu dentista';

        $titulo  = 'Recordatorio: cita manana';
        $mensaje = "Manana tienes cita a las {$hora} con {$drNombre}. Llega 10 minutos antes.";
        $tipoDb  = 'recordatorio_cita';

        $notif = Notificacion::create([
            'paciente_id' => $usuario->paciente_id,
            'tipo'        => $tipoDb,
            'titulo'      => $titulo,
            'mensaje'     => $mensaje,
        ]);

        $this->line("Notificacion creada: ID #{$notif->id}");
        $this->line("Titulo: {$titulo}");
        $this->line("Mensaje: {$mensaje}");
        $this->info($tienePush ? 'Push enviado.' : 'Notificacion guardada (sin push activo).');
    }
}
