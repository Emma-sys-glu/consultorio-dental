<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Expediente;
use App\Models\Paciente;
use App\Models\Tratamiento;
use Illuminate\Http\Request;

class TratamientoWebController extends Controller
{
    public function index()
    {
        return view('tratamientos.index', [
            'tratamientos' => Tratamiento::with(['paciente', 'dentista'])
                ->orderBy('id', 'desc')
                ->paginate(20)
        ]);
    }

    public function create()
    {
        return view('tratamientos.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $datos = $this->validarTratamiento($request);

        Tratamiento::create($datos);

        return redirect()->route('tratamientos.vista')
            ->with('success', 'Tratamiento registrado correctamente');
    }

    public function edit(Tratamiento $tratamiento)
    {
        return view('tratamientos.edit', [
            'tratamiento' => $tratamiento,
        ] + $this->catalogos());
    }

    public function update(Request $request, Tratamiento $tratamiento)
    {
        $datos = $this->validarTratamiento($request);

        $tratamiento->update($datos);

        return redirect()->route('tratamientos.vista')
            ->with('success', 'Tratamiento actualizado correctamente');
    }

    public function destroy(Tratamiento $tratamiento)
    {
        $tratamiento->delete();

        return redirect()->route('tratamientos.vista')
            ->with('success', 'Tratamiento eliminado correctamente');
    }

    private function catalogos(): array
    {
        return [
            'pacientes' => Paciente::orderBy('nombre')->get(),
            'dentistas' => Dentista::orderBy('nombre')->get(),
            'expedientes' => Expediente::with('paciente')->orderBy('id')->get(),
            'citas' => Cita::with(['paciente', 'dentista'])->orderBy('fecha', 'desc')->get(),
        ];
    }

    private function validarTratamiento(Request $request): array
    {
        return $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'dentista_id' => 'required|exists:dentistas,id',
            'expediente_id' => 'required|exists:expedientes,id',
            'cita_id' => 'nullable|exists:citas,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'costo' => 'required|numeric|min:0',
            'estado' => 'required|in:pendiente,en_proceso,finalizado,cancelado',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
        ]);
    }
}
