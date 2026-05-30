<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    public function index()
    {
        return response()->json([
            'mensaje' => 'Lista de expedientes',
            'data' => Expediente::with('paciente')
                ->orderBy('id', 'desc')
                ->paginate(20)
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id|unique:expedientes,paciente_id',
            'diagnostico' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'procedimientos_realizados' => 'nullable|string',
            'evolucion_tratamiento' => 'nullable|string'
        ]);

        $expediente = Expediente::create($datos);

        return response()->json([
            'mensaje' => 'Expediente creado correctamente',
            'data' => $expediente->load('paciente')
        ], 201);
    }

    public function show(Expediente $expediente)
    {
        return response()->json([
            'mensaje' => 'Expediente encontrado',
            'data' => $expediente->load('paciente')
        ]);
    }

    public function update(Request $request, Expediente $expediente)
    {
        $datos = $request->validate([
            'diagnostico' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'procedimientos_realizados' => 'nullable|string',
            'evolucion_tratamiento' => 'nullable|string'
        ]);

        $expediente->update($datos);

        return response()->json([
            'mensaje' => 'Expediente actualizado correctamente',
            'data' => $expediente->load('paciente')
        ]);
    }

    public function destroy(Expediente $expediente)
    {
        $expediente->delete();

        return response()->json([
            'mensaje' => 'Expediente eliminado correctamente'
        ]);
    }
}