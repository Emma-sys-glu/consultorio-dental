<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Dentista;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\Tratamiento;
use Illuminate\Http\Request;

class RecetaWebController extends Controller
{
    public function index()
    {
        return view('recetas.index', [
            'recetas' => Receta::with(['paciente', 'dentista', 'tratamiento'])
                ->orderBy('id', 'desc')
                ->paginate(20)
        ]);
    }

    public function create()
    {
        return view('recetas.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $datos = $this->validarReceta($request);

        Receta::create($datos);

        return redirect()->route('recetas.vista')
            ->with('success', 'Receta registrada correctamente');
    }

    public function edit(Receta $receta)
    {
        return view('recetas.edit', [
            'receta' => $receta,
        ] + $this->catalogos());
    }

    public function update(Request $request, Receta $receta)
    {
        $datos = $this->validarReceta($request);

        $receta->update($datos);

        return redirect()->route('recetas.vista')
            ->with('success', 'Receta actualizada correctamente');
    }

    public function destroy(Receta $receta)
    {
        $receta->delete();

        return redirect()->route('recetas.vista')
            ->with('success', 'Receta eliminada correctamente');
    }

    private function catalogos(): array
    {
        return [
            'pacientes' => Paciente::orderBy('nombre')->get(),
            'dentistas' => Dentista::orderBy('nombre')->get(),
            'tratamientos' => Tratamiento::with('paciente')->orderBy('id', 'desc')->get(),
        ];
    }

    private function validarReceta(Request $request): array
    {
        return $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'dentista_id' => 'required|exists:dentistas,id',
            'tratamiento_id' => 'nullable|exists:tratamientos,id',
            'medicamento' => 'required|string|max:150',
            'dosis' => 'required|string|max:100',
            'frecuencia' => 'required|string|max:100',
            'duracion' => 'required|string|max:100',
            'indicaciones' => 'nullable|string',
            'fecha_emision' => 'required|date'
        ]);
    }
}
