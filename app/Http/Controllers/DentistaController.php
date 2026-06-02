<?php

namespace App\Http\Controllers;

use App\Models\Dentista;
use Illuminate\Http\Request;

class DentistaController extends Controller
{
    public function index()
    {
        return response()->json([
            'mensaje' => 'Lista de dentistas',
            'data' => Dentista::orderBy('id', 'desc')->paginate(5)
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'especialidad' => 'required|string|max:100',
            'cedula_profesional' => 'required|string|max:30|unique:dentistas,cedula_profesional',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|unique:dentistas,correo',
            'horario_inicio' => 'required',
            'horario_fin' => 'required',
            'consultorio' => 'required|string|max:50'
        ]);

        $dentista = Dentista::create($datos);

        return response()->json([
            'mensaje' => 'Dentista registrado correctamente',
            'data' => $dentista
        ], 201);
    }

    public function show(Dentista $dentista)
    {
        return response()->json([
            'mensaje' => 'Dentista encontrado',
            'data' => $dentista->load('citas')
        ]);
    }

    public function update(Request $request, Dentista $dentista)
    {
        $datos = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'apellido_paterno' => 'sometimes|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'especialidad' => 'sometimes|string|max:100',
            'cedula_profesional' => 'sometimes|string|max:30|unique:dentistas,cedula_profesional,' . $dentista->id,
            'telefono' => 'sometimes|string|max:20',
            'correo' => 'sometimes|email|unique:dentistas,correo,' . $dentista->id,
            'horario_inicio' => 'sometimes',
            'horario_fin' => 'sometimes',
            'consultorio' => 'sometimes|string|max:50'
        ]);

        $dentista->update($datos);

        return response()->json([
            'mensaje' => 'Dentista actualizado correctamente',
            'data' => $dentista
        ]);
    }

    public function destroy(Dentista $dentista)
    {
        $dentista->delete();

        return response()->json([
            'mensaje' => 'Dentista eliminado correctamente'
        ]);
    }
}