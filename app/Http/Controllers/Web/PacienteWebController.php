<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteWebController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->query('buscar');

        $pacientes = Paciente::query()
            ->when($buscar, function ($query, $buscar) {
                $buscar = strtolower(trim($buscar));

                $query->where(function ($q) use ($buscar) {
                    $q->whereRaw('LOWER(nombre) LIKE ?', ["%{$buscar}%"])
                        ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
                        ->orWhereRaw('LOWER(COALESCE(apellido_materno, \'\')) LIKE ?', ["%{$buscar}%"])
                        ->orWhereRaw("LOWER(CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno, ''))) LIKE ?", ["%{$buscar}%"]);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pacientes.partials.tabla', [
                'pacientes' => $pacientes,
            ]);
        }

        return view('pacientes.index', [
            'pacientes' => $pacientes,
            'buscar' => $buscar,
        ]);
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $datos = $this->validarPaciente($request);

        Paciente::create($datos);

        return redirect()->route('pacientes.vista')
            ->with('success', 'Paciente registrado correctamente');
    }

    public function show(Paciente $paciente)
    {
        $paciente->load(['citas' => function ($q) {
            $q->with('dentista')->orderBy('fecha', 'desc')->orderBy('hora_inicio', 'desc')->limit(10);
        }, 'expediente']);

        $tratamientos = \App\Models\Tratamiento::where('paciente_id', $paciente->id)
            ->with('dentista')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('pacientes.show', compact('paciente', 'tratamientos'));
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', [
            'paciente' => $paciente
        ]);
    }

    public function update(Request $request, Paciente $paciente)
    {
        $datos = $this->validarPaciente($request, $paciente);

        $paciente->update($datos);

        return redirect()->route('pacientes.vista')
            ->with('success', 'Paciente actualizado correctamente');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();

        return redirect()->route('pacientes.vista')
            ->with('success', 'Paciente eliminado correctamente');
    }

    private function validarPaciente(Request $request, ?Paciente $paciente = null): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|unique:pacientes,correo' . ($paciente ? ',' . $paciente->id : ''),
            'fecha_nacimiento' => 'required|date',
            'curp' => 'nullable|string|max:18',
            'tipo_sangre' => 'nullable|string|max:5',
            'alergias' => 'nullable|string|max:255',
            'antecedentes_medicos' => 'nullable|string'
        ]);
    }
}
