<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;

class NotificacionWebController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        if ($usuario->rol !== 'paciente') {
            abort(403, 'Solo los pacientes pueden ver sus notificaciones.');
        }

        $notificaciones = Notificacion::where('paciente_id', $usuario->paciente_id)
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('notificaciones.index', [
            'notificaciones' => $notificaciones
        ]);
    }
}