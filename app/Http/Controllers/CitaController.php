<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Dentista;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index()
    {
        return response()->json([
            'mensaje' => 'Lista de citas',
            'data' => Cita::with(['paciente', 'dentista'])
                ->orderBy('fecha', 'desc')
                ->orderBy('hora_inicio', 'desc')
                ->paginate(10)
        ]);
    }

    public function store(Request $request)
    {

   if (auth()->user()->rol === 'paciente') {
    $request->merge([
        'paciente_id' => auth()->user()->paciente_id
    ]);
}
        $datos = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'dentista_id' => 'required|exists:dentistas,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required',
            'duracion_minutos' => 'required|integer|min:15|max:240',
            'motivo' => 'required|string|max:255'
        ]);

        $inicio = Carbon::parse($datos['fecha'] . ' ' . $datos['hora_inicio']);
        $fin = $inicio->copy()->addMinutes((int) $datos['duracion_minutos']);

        if ($inicio->isPast()) {
            return response()->json([
                'mensaje' => 'No se pueden agendar citas en fechas u horas pasadas'
            ], 422);
        }

        $dentista = Dentista::findOrFail($datos['dentista_id']);

        $horarioInicioDentista = Carbon::parse($datos['fecha'] . ' ' . $dentista->horario_inicio);
        $horarioFinDentista = Carbon::parse($datos['fecha'] . ' ' . $dentista->horario_fin);

        if ($inicio->lt($horarioInicioDentista) || $fin->gt($horarioFinDentista)) {
            return response()->json([
                'mensaje' => 'La cita está fuera del horario laboral del dentista'
            ], 422);
        }

        $empalme = Cita::where('dentista_id', $datos['dentista_id'])
            ->where('fecha', $datos['fecha'])
            ->where('estado', '!=', 'cancelada')
            ->where(function ($query) use ($inicio, $fin) {
                $query->where(function ($q) use ($inicio, $fin) {
                    $q->whereTime('hora_inicio', '<', $fin->format('H:i:s'))
                      ->whereTime('hora_fin', '>', $inicio->format('H:i:s'));
                });
            })
            ->exists();

        if ($empalme) {
            return response()->json([
                'mensaje' => 'El dentista ya tiene una cita en ese horario'
            ], 409);
        }

        $cita = Cita::create([
            'paciente_id' => $datos['paciente_id'],
            'dentista_id' => $datos['dentista_id'],
            'fecha' => $datos['fecha'],
            'hora_inicio' => $inicio->format('H:i:s'),
            'hora_fin' => $fin->format('H:i:s'),
            'duracion_minutos' => $datos['duracion_minutos'],
            'motivo' => $datos['motivo'],
            'estado' => 'pendiente'
        ]);

        return response()->json([
            'mensaje' => 'Cita registrada correctamente',
            'data' => $cita->load(['paciente', 'dentista'])
        ], 201);
    }

    public function show(Cita $cita)
    {
        return response()->json([
            'mensaje' => 'Cita encontrada',
            'data' => $cita->load(['paciente', 'dentista'])
        ]);
    }

    public function update(Request $request, Cita $cita)
    {
        $datos = $request->validate([
            'paciente_id' => 'sometimes|exists:pacientes,id',
            'dentista_id' => 'sometimes|exists:dentistas,id',
            'fecha' => 'sometimes|date',
            'hora_inicio' => 'sometimes',
            'duracion_minutos' => 'sometimes|integer|min:15|max:240',
            'motivo' => 'sometimes|string|max:255',
            'estado' => 'sometimes|in:pendiente,confirmada,cancelada,finalizada'
        ]);

        $pacienteId = $datos['paciente_id'] ?? $cita->paciente_id;
        $dentistaId = $datos['dentista_id'] ?? $cita->dentista_id;
        $fecha = $datos['fecha'] ?? $cita->fecha;
        $horaInicio = $datos['hora_inicio'] ?? $cita->hora_inicio;
        $duracion = $datos['duracion_minutos'] ?? $cita->duracion_minutos;

        $inicio = Carbon::parse($fecha . ' ' . $horaInicio);
        $fin = $inicio->copy()->addMinutes((int) $duracion);

        if ($inicio->isPast() && ($datos['estado'] ?? null) !== 'cancelada') {
            return response()->json([
                'mensaje' => 'No se puede reprogramar una cita a una fecha u hora pasada'
            ], 422);
        }

        $dentista = Dentista::findOrFail($dentistaId);

        $horarioInicioDentista = Carbon::parse($fecha . ' ' . $dentista->horario_inicio);
        $horarioFinDentista = Carbon::parse($fecha . ' ' . $dentista->horario_fin);

        if ($inicio->lt($horarioInicioDentista) || $fin->gt($horarioFinDentista)) {
            return response()->json([
                'mensaje' => 'La cita está fuera del horario laboral del dentista'
            ], 422);
        }

        $empalme = Cita::where('dentista_id', $dentistaId)
            ->where('fecha', $fecha)
            ->where('id', '!=', $cita->id)
            ->where('estado', '!=', 'cancelada')
            ->where(function ($query) use ($inicio, $fin) {
                $query->whereTime('hora_inicio', '<', $fin->format('H:i:s'))
                    ->whereTime('hora_fin', '>', $inicio->format('H:i:s'));
            })
            ->exists();

        if ($empalme) {
            return response()->json([
                'mensaje' => 'El dentista ya tiene una cita en ese horario'
            ], 409);
        }

        $cita->update([
            'paciente_id' => $pacienteId,
            'dentista_id' => $dentistaId,
            'fecha' => $fecha,
            'hora_inicio' => $inicio->format('H:i:s'),
            'hora_fin' => $fin->format('H:i:s'),
            'duracion_minutos' => $duracion,
            'motivo' => $datos['motivo'] ?? $cita->motivo,
            'estado' => $datos['estado'] ?? $cita->estado
        ]);

        return response()->json([
            'mensaje' => 'Cita actualizada correctamente',
            'data' => $cita->load(['paciente', 'dentista'])
        ]);
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return response()->json([
            'mensaje' => 'Cita eliminada correctamente'
        ]);
    }

    public function storeWeb(Request $request)
{
    $response = $this->store($request);

    if ($response->getStatusCode() !== 201) {
        $data = $response->getData(true);

        return back()
            ->withInput()
            ->withErrors([
                'error' => $data['mensaje'] ?? 'No se pudo registrar la cita'
            ]);
    }

    return redirect()
        ->route('citas.vista')
        ->with('success', 'Cita registrada correctamente');
}

public function updateWeb(Request $request, Cita $cita)
{
    $response = $this->update($request, $cita);

    if ($response->getStatusCode() !== 200) {
        $data = $response->getData(true);

        return back()
            ->withInput()
            ->withErrors([
                'error' => $data['mensaje'] ?? 'No se pudo actualizar la cita'
            ]);
    }

    return redirect()
        ->route('citas.vista')
        ->with('success', 'Cita actualizada correctamente');
}

}