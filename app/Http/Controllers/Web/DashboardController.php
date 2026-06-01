<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Inventario;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\Tratamiento;

class DashboardController extends Controller
{
    public function redirectToHome()
    {
        return redirect()->route('dashboard');
    }

    public function index()
    {
        $hoy = now()->toDateString();

        return view('dashboard', [
            'totalPacientes' => Paciente::count(),
            'totalDentistas' => Dentista::count(),
            'totalCitas' => Cita::count(),
            'totalTratamientos' => Tratamiento::count(),
            'totalRecetas' => Receta::count(),
            'totalInventario' => Inventario::count(),
            'stockBajo' => Inventario::whereColumn('cantidad', '<=', 'stock_minimo')->count(),
            'citasHoy' => Cita::whereDate('fecha', $hoy)->count(),
        ]);
    }
}
