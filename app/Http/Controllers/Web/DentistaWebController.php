<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Dentista;
use Illuminate\Http\Request;

class DentistaWebController extends Controller
{
    public function index()
    {
        return view('dentistas.index', [
            'dentistas' => Dentista::orderBy('id', 'desc')->paginate(20)
        ]);
    }

    public function create()
    {
        return view('dentistas.create');
    }

    public function store(Request $request)
    {
        $datos = $this->validarDentista($request);

        Dentista::create($datos);

        return redirect()->route('dentistas.vista')
            ->with('success', 'Dentista registrado correctamente');
    }

    public function edit(Dentista $dentista)
    {
        return view('dentistas.edit', [
            'dentista' => $dentista
        ]);
    }

    public function update(Request $request, Dentista $dentista)
    {
        $datos = $this->validarDentista($request, $dentista);

        $dentista->update($datos);

        return redirect()->route('dentistas.vista')
            ->with('success', 'Dentista actualizado correctamente');
    }

    public function destroy(Dentista $dentista)
    {
        $dentista->delete();

        return redirect()->route('dentistas.vista')
            ->with('success', 'Dentista eliminado correctamente');
    }

    private function validarDentista(Request $request, ?Dentista $dentista = null): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'especialidad' => 'required|string|max:100',
            'cedula_profesional' => 'required|string|max:30|unique:dentistas,cedula_profesional' . ($dentista ? ',' . $dentista->id : ''),
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|unique:dentistas,correo' . ($dentista ? ',' . $dentista->id : ''),
            'horario_inicio' => 'required',
            'horario_fin' => 'required',
            'consultorio' => 'required|string|max:50'
        ]);
    }
}
