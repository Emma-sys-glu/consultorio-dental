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
    $usuario = auth()->user();

    $query = Cita::with(['paciente', 'dentista']);

    if ($usuario->rol === 'paciente') {
        $query->where('paciente_id', $usuario->paciente_id);
    }

    if ($usuario->rol === 'dentista') {
        $query->where('dentista_id', $usuario->dentista_id);
    }

    $citas = $query->orderBy('fecha', 'desc')
        ->orderBy('hora_inicio', 'desc')
        ->paginate(20);

    return view('citas.index', compact('citas'));
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
    $usuario = auth()->user();

    if ($usuario->rol === 'paciente' && $cita->paciente_id !== $usuario->paciente_id) {
        abort(403, 'No puedes editar una cita que no te pertenece.');
    }

    if ($usuario->rol === 'dentista' && $cita->dentista_id !== $usuario->dentista_id) {
        abort(403, 'No puedes editar una cita que no te corresponde.');
    }

    return view('citas.edit', [
        'cita' => $cita,
        'pacientes' => Paciente::orderBy('nombre')->get(),
        'dentistas' => Dentista::orderBy('nombre')->get(),
    ]);
}

    public function update(Request $request, Cita $cita, CitaController $citas)
    {
        $usuario = auth()->user();

if ($usuario->rol === 'paciente' && $cita->paciente_id !== $usuario->paciente_id) {
    abort(403, 'No puedes actualizar una cita que no te pertenece.');
}

if ($usuario->rol === 'paciente') {
    $request->merge([
        'paciente_id' => $usuario->paciente_id
    ]);
}
        return $citas->updateWeb($request, $cita);
    }

    public function cancelar(Cita $cita)
{
    $usuario = auth()->user();

    if ($usuario->rol === 'paciente' && $cita->paciente_id !== $usuario->paciente_id) {
        abort(403, 'No puedes cancelar una cita que no te pertenece.');
    }

    if ($usuario->rol === 'dentista' && $cita->dentista_id !== $usuario->dentista_id) {
        abort(403, 'No puedes cancelar una cita que no te corresponde.');
    }

    $cita->update([
        'estado' => 'cancelada'
    ]);

    return redirect()->route('citas.vista')
        ->with('success', 'Cita cancelada correctamente');
}

    public function destroy(Cita $cita)
{
    $usuario = auth()->user();

    if ($usuario->rol === 'paciente' && $cita->paciente_id !== $usuario->paciente_id) {
        abort(403, 'No puedes eliminar una cita que no te pertenece.');
    }

    if ($usuario->rol === 'dentista' && $cita->dentista_id !== $usuario->dentista_id) {
        abort(403, 'No puedes eliminar una cita que no te corresponde.');
    }

    $cita->delete();

    return redirect()->route('citas.vista')
        ->with('success', 'Cita eliminada correctamente');
}
}
