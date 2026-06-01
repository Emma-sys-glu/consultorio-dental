<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CitaController;
use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Paciente;
use Illuminate\Http\Request;

class CitaWebController extends Controller
{
    public function index()
    {
        return view('citas.index', [
            'citas' => Cita::with(['paciente', 'dentista'])
                ->orderBy('fecha', 'desc')
                ->paginate(20),
        ]);
    }

    public function create()
    {
        return view('citas.create', [
            'pacientes' => Paciente::orderBy('nombre')->get(),
            'dentistas' => Dentista::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request, CitaController $citas)
    {
        return $citas->storeWeb($request);
    }

    public function edit(Cita $cita)
    {
        return view('citas.edit', [
            'cita' => $cita,
            'pacientes' => Paciente::orderBy('nombre')->get(),
            'dentistas' => Dentista::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Cita $cita, CitaController $citas)
    {
        return $citas->updateWeb($request, $cita);
    }

    public function cancel(Cita $cita)
    {
        $cita->update([
            'estado' => 'cancelada'
        ]);

        return redirect()->route('citas.vista')
            ->with('success', 'Cita cancelada correctamente');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.vista')
            ->with('success', 'Cita eliminada correctamente');
    }
}
