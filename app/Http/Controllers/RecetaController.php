<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RecetaController extends Controller
{
    public function index()
    {
        return response()->json([
            'mensaje' => 'Lista de recetas',
            'data' => Receta::with(['paciente', 'dentista', 'tratamiento'])
                ->orderBy('id', 'desc')
                ->paginate(20)
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'dentista_id' => 'required|exists:dentistas,id',
            'tratamiento_id' => 'nullable|exists:tratamientos,id',
            'medicamento' => 'required|string|max:150',
            'dosis' => 'required|string|max:100',
            'frecuencia' => 'required|string|max:100',
            'duracion' => 'required|string|max:100',
            'indicaciones' => 'nullable|string',
            'fecha_emision' => 'nullable|date'
        ]);

        $datos['fecha_emision'] = $datos['fecha_emision'] ?? Carbon::now()->format('Y-m-d');

        $receta = Receta::create($datos);

        return response()->json([
            'mensaje' => 'Receta registrada correctamente',
            'data' => $receta->load(['paciente', 'dentista', 'tratamiento'])
        ], 201);
    }

    public function show(Receta $receta)
    {
        return response()->json([
            'mensaje' => 'Receta encontrada',
            'data' => $receta->load(['paciente', 'dentista', 'tratamiento'])
        ]);
    }

    public function update(Request $request, Receta $receta)
    {
        $datos = $request->validate([
            'medicamento' => 'sometimes|string|max:150',
            'dosis' => 'sometimes|string|max:100',
            'frecuencia' => 'sometimes|string|max:100',
            'duracion' => 'sometimes|string|max:100',
            'indicaciones' => 'nullable|string',
            'fecha_emision' => 'sometimes|date'
        ]);

        $receta->update($datos);

        return response()->json([
            'mensaje' => 'Receta actualizada correctamente',
            'data' => $receta->load(['paciente', 'dentista', 'tratamiento'])
        ]);
    }

    public function destroy(Receta $receta)
    {
        $receta->delete();

        return response()->json([
            'mensaje' => 'Receta eliminada correctamente'
        ]);
    }
}