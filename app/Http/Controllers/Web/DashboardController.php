<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Inventario;
use App\Models\Paciente;
use App\Models\Tratamiento;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function redirectToHome()
    {
        return redirect()->route('dashboard');
    }

    public function index(Request $request)
    {
        $hoy = Carbon::today();
        $mes = $request->query('mes');
        $fechaCalendario = $hoy->copy()->startOfMonth();

        if (is_string($mes) && preg_match('/^\d{4}-\d{2}$/', $mes)) {
            try {
                $fechaCalendario = Carbon::createFromFormat('!Y-m-d', $mes . '-01')->startOfMonth();
            } catch (\Throwable) {
                $fechaCalendario = $hoy->copy()->startOfMonth();
            }
        }

        $inicioMes = $fechaCalendario->copy()->startOfMonth();
        $finMes = $fechaCalendario->copy()->endOfMonth();

        return view('dashboard', [
            'totalPacientes' => Paciente::count(),
            'totalDentistas' => Dentista::count(),
            'totalCitas' => Cita::count(),
            'totalTratamientos' => Tratamiento::count(),
            'stockBajo' => Inventario::whereColumn('cantidad', '<=', 'stock_minimo')->count(),
            'citasHoy' => Cita::whereDate('fecha', $hoy)->count(),
            'proximasCitas' => Cita::with(['paciente', 'dentista'])
                ->whereDate('fecha', '>=', $hoy)
                ->where('estado', '!=', 'cancelada')
                ->orderBy('fecha')
                ->orderBy('hora_inicio')
                ->limit(5)
                ->get(),
            'citasMes' => Cita::with(['paciente', 'dentista'])
                ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
                ->where('estado', '!=', 'cancelada')
                ->orderBy('fecha')
                ->orderBy('hora_inicio')
                ->get()
                ->groupBy(fn ($cita) => Carbon::parse($cita->fecha)->day),
            'mesActual' => $fechaCalendario->translatedFormat('F Y'),
            'mesAnterior' => $fechaCalendario->copy()->subMonth()->format('Y-m'),
            'mesSiguiente' => $fechaCalendario->copy()->addMonth()->format('Y-m'),
            'mesSeleccionado' => $fechaCalendario->format('Y-m'),
            'inicioCalendario' => $inicioMes->copy()->startOfWeek(Carbon::MONDAY),
            'finCalendario' => $finMes->copy()->endOfWeek(Carbon::SUNDAY),
            'hoy' => $hoy,
            'fechaCalendario' => $fechaCalendario,
            'productosBajos' => Inventario::whereColumn('cantidad', '<=', 'stock_minimo')
                ->orderBy('cantidad')
                ->limit(5)
                ->get(),
        ]);
    }
}
