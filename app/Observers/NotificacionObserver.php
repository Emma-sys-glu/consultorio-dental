<?php

namespace App\Observers;

use App\Models\Notificacion;
use App\Models\User;
use App\Services\PushNotificationService;

class NotificacionObserver
{
    public function created(Notificacion $notificacion): void
    {
        if (! $notificacion->paciente_id) {
            return;
        }

        $usuario = User::where('paciente_id', $notificacion->paciente_id)->first();

        if (! $usuario) {
            return;
        }

        app(PushNotificationService::class)->enviarAlUsuario(
            $usuario->id,
            $notificacion->titulo,
            $notificacion->mensaje,
            '/notificaciones'
        );
    }
}
