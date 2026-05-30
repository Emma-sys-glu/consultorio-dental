<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index()
    {
        return response()->json([
            'mensaje' => 'Lista de pacientes',
            'data' => Paciente::orderBy('id', 'desc')->paginate(20)
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|unique:pacientes,correo',
            'fecha_nacimiento' => 'required|date',
            'curp' => 'nullable|string|max:18',
            'tipo_sangre' => 'nullable|string|max:5',
            'alergias' => 'nullable|string|max:255',
            'antecedentes_medicos' => 'nullable|string'
        ]);

        $paciente = Paciente::create($datos);

        return response()->json([
            'mensaje' => 'Paciente registrado correctamente',
            'data' => $paciente
        ], 201);
    }

    public function show(Paciente $paciente)
    {
        return response()->json([
            'mensaje' => 'Paciente encontrado',
            'data' => $paciente->load('citas')
        ]);
    }

    public function update(Request $request, Paciente $paciente)
    {
        $datos = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'apellido_paterno' => 'sometimes|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'sometimes|string|max:20',
            'correo' => 'sometimes|email|unique:pacientes,correo,' . $paciente->id,
            'fecha_nacimiento' => 'sometimes|date',
            'curp' => 'nullable|string|max:18',
            'tipo_sangre' => 'nullable|string|max:5',
            'alergias' => 'nullable|string|max:255',
            'antecedentes_medicos' => 'nullable|string'
        ]);

        $paciente->update($datos);

        return response()->json([
            'mensaje' => 'Paciente actualizado correctamente',
            'data' => $paciente
        ]);
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();

        return response()->json([
            'mensaje' => 'Paciente eliminado correctamente'
        ]);
    }
}