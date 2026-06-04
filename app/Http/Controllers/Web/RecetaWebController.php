<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Dentista;
use App\Models\Inventario;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\Tratamiento;
use Illuminate\Http\Request;

class RecetaWebController extends Controller
{
    public function index()
{
    $usuario = auth()->user();

    $query = \App\Models\Receta::with(['paciente', 'dentista', 'tratamiento']);

    if ($usuario->rol === 'paciente') {
        $query->where('paciente_id', $usuario->paciente_id);
    }

    if ($usuario->rol === 'dentista') {
        $query->where('dentista_id', $usuario->dentista_id);
    }

    $recetas = $query
        ->orderBy('id', 'desc')
        ->paginate(20);

    return view('recetas.index', [
        'recetas' => $recetas
    ]);
}

    public function create()
    {
        return view('recetas.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $datos = $this->validarReceta($request);
        $datos = $this->completarMedicamento($datos);

        $receta = Receta::create($datos);

        if ($receta->inventario_id && $receta->cantidad) {
            $this->descontarInventario($receta);
        }

        return redirect()->route('recetas.vista')
            ->with('success', 'Receta registrada y stock actualizado correctamente');
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
        $datos = $this->completarMedicamento($datos);

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
            'pacientes'   => Paciente::orderBy('nombre')->get(),
            'dentistas'   => Dentista::orderBy('nombre')->get(),
            'tratamientos' => Tratamiento::with('paciente')->orderBy('id', 'desc')->get(),
            'medicamentos' => Inventario::where('categoria', 'Medicamentos')->orderBy('nombre')->get(),
        ];
    }

    private function validarReceta(Request $request): array
    {
        return $request->validate([
            'paciente_id'    => 'required|exists:pacientes,id',
            'dentista_id'    => 'required|exists:dentistas,id',
            'tratamiento_id' => 'nullable|exists:tratamientos,id',
            'inventario_id'  => 'nullable|exists:inventarios,id',
            'cantidad'       => 'nullable|integer|min:1',
            // medicamento requerido solo si NO se seleccionó del inventario
            'medicamento'    => 'nullable|string|max:150',
            'dosis'          => 'required|string|max:100',
            'frecuencia'     => 'required|string|max:100',
            'duracion'       => 'required|string|max:100',
            'indicaciones'   => 'nullable|string',
            'fecha_emision'  => 'required|date',
        ]);
    }

    private function completarMedicamento(array $datos): array
    {
        // Si viene del inventario, tomar el nombre de ahí (más confiable que el JS)
        if (!empty($datos['inventario_id'])) {
            $inv = Inventario::find($datos['inventario_id']);
            if ($inv) {
                $datos['medicamento'] = $datos['medicamento'] ?: $inv->nombre;
            }
        }

        // Si aún está vacío (sin inventario y sin texto), lanzar error
        if (empty($datos['medicamento'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'medicamento' => 'El nombre del medicamento es requerido.',
            ]);
        }

        return $datos;
    }

    private function descontarInventario(Receta $receta): void
    {
        $inventario = Inventario::find($receta->inventario_id);
        if (!$inventario) return;

        $dentista = Dentista::find($receta->dentista_id);
        $cantidad = (int) $receta->cantidad;

        $updates = [
            'cantidad' => max(0, $inventario->cantidad - $cantidad),
        ];

        if ($dentista && $dentista->consultorio) {
            $num = preg_replace('/[^0-9]/', '', $dentista->consultorio);
            if (in_array($num, ['1', '2', '3', '4'])) {
                $col = "stock_c{$num}";
                $updates[$col] = max(0, $inventario->$col - $cantidad);
            }
        }

        $inventario->update($updates);
    }
}
