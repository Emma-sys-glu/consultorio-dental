<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Dentista;
use App\Models\Cita;
use App\Models\Tratamiento;
use App\Models\Inventario;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalPacientes' => Paciente::count(),
            'totalDentistas' => Dentista::count(),
            'totalCitas' => Cita::count(),
            'totalTratamientos' => Tratamiento::count(),
            'stockBajo' => Inventario::whereColumn('cantidad', '<=', 'stock_minimo')->count(),
        ]);
    }
}
