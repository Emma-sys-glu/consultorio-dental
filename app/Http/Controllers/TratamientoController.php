<?php

namespace App\Http\Controllers;

use App\Models\Tratamiento;
use Illuminate\Http\Request;

class TratamientoController extends Controller
{
    public function index()
    {
        return response()->json([
            'mensaje' => 'Lista de tratamientos',
            'data' => Tratamiento::with(['paciente', 'dentista', 'expediente', 'cita'])
                ->orderBy('id', 'desc')
                ->paginate(20)
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'dentista_id' => 'required|exists:dentistas,id',
            'expediente_id' => 'required|exists:expedientes,id',
            'cita_id' => 'nullable|exists:citas,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'costo' => 'required|numeric|min:0',
            'estado' => 'nullable|in:pendiente,en_proceso,finalizado,cancelado',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
        ]);

        $tratamiento = Tratamiento::create($datos);

        return response()->json([
            'mensaje' => 'Tratamiento registrado correctamente',
            'data' => $tratamiento->load(['paciente', 'dentista', 'expediente', 'cita'])
        ], 201);
    }

    public function show(Tratamiento $tratamiento)
    {
        return response()->json([
            'mensaje' => 'Tratamiento encontrado',
            'data' => $tratamiento->load(['paciente', 'dentista', 'expediente', 'cita'])
        ]);
    }

    public function update(Request $request, Tratamiento $tratamiento)
    {
        $datos = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string',
            'costo' => 'sometimes|numeric|min:0',
            'estado' => 'sometimes|in:pendiente,en_proceso,finalizado,cancelado',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
        ]);

        $tratamiento->update($datos);

        return response()->json([
            'mensaje' => 'Tratamiento actualizado correctamente',
            'data' => $tratamiento
        ]);
    }

    public function destroy(Tratamiento $tratamiento)
    {
        $tratamiento->delete();

        return response()->json([
            'mensaje' => 'Tratamiento eliminado correctamente'
        ]);
    }
}