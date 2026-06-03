<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Inventario;
use App\Models\Notificacion;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\Tratamiento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function redirectToHome()
    {
        $rol = Auth::user()->rol ?? null;

        return match ($rol) {
            'administrador', 'recepcionista' => redirect()->route('dashboard'),
            'dentista'                        => redirect()->route('dashboard.dentista'),
            'paciente'                        => redirect()->route('dashboard.paciente'),
            default                           => redirect()->route('dashboard'),
        };
    }

    // ── Administrador / Recepcionista ─────────────────────────────────
    public function index(Request $request)
    {
        $rol = Auth::user()->rol;
        if ($rol === 'dentista') return redirect()->route('dashboard.dentista');
        if ($rol === 'paciente') return redirect()->route('dashboard.paciente');

        $hoy = Carbon::today();
        $fechaCalendario = $hoy->copy()->startOfMonth();
        $mes = $request->query('mes');

        if (is_string($mes) && preg_match('/^\d{4}-\d{2}$/', $mes)) {
            try {
                $fechaCalendario = Carbon::createFromFormat('!Y-m-d', $mes . '-01')->startOfMonth();
            } catch (\Throwable) {
                $fechaCalendario = $hoy->copy()->startOfMonth();
            }
        }

        $inicioMes = $fechaCalendario->copy()->startOfMonth();
        $finMes    = $fechaCalendario->copy()->endOfMonth();

        return view('dashboard', [
            'totalPacientes'  => Paciente::count(),
            'totalDentistas'  => Dentista::count(),
            'totalCitas'      => Cita::count(),
            'totalTratamientos' => Tratamiento::count(),
            'totalRecetas'    => Receta::count(),
            'totalInventario' => Inventario::count(),
            'stockBajo'       => Inventario::whereColumn('cantidad', '<=', 'stock_minimo')->count(),
            'citasHoy'        => Cita::whereDate('fecha', $hoy)->count(),
            'citasMes'        => Cita::with(['paciente', 'dentista'])
                ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
                ->where('estado', '!=', 'cancelada')
                ->orderBy('fecha')->orderBy('hora_inicio')
                ->get()
                ->groupBy(fn($c) => Carbon::parse($c->fecha)->day),
            'mesActual'        => $fechaCalendario->translatedFormat('F Y'),
            'mesAnterior'      => $fechaCalendario->copy()->subMonth()->format('Y-m'),
            'mesSiguiente'     => $fechaCalendario->copy()->addMonth()->format('Y-m'),
            'inicioCalendario' => $inicioMes->copy()->startOfWeek(Carbon::MONDAY),
            'finCalendario'    => $finMes->copy()->endOfWeek(Carbon::SUNDAY),
            'hoy'              => $hoy,
            'fechaCalendario'  => $fechaCalendario,
        ]);
    }

    // ── Dentista ──────────────────────────────────────────────────────
    public function indexDentista(Request $request)
    {
        $rol = Auth::user()->rol;
        if ($rol !== 'dentista') return $this->redirectToHome();

        $user    = Auth::user();
        $dentista = $user->dentista;

        if (! $dentista) {
            return redirect()->route('dashboard')->with('error', 'No tiene perfil de dentista asignado.');
        }

        $hoy = Carbon::today();
        $fechaCalendario = $hoy->copy()->startOfMonth();
        $mes = $request->query('mes');

        if (is_string($mes) && preg_match('/^\d{4}-\d{2}$/', $mes)) {
            try {
                $fechaCalendario = Carbon::createFromFormat('!Y-m-d', $mes . '-01')->startOfMonth();
            } catch (\Throwable) {
                $fechaCalendario = $hoy->copy()->startOfMonth();
            }
        }

        $inicioMes = $fechaCalendario->copy()->startOfMonth();
        $finMes    = $fechaCalendario->copy()->endOfMonth();

        return view('dashboard-dentista', [
            'dentista'     => $dentista,
            'citasHoy'     => Cita::with('paciente')
                ->where('dentista_id', $dentista->id)
                ->whereDate('fecha', $hoy)
                ->orderBy('hora_inicio')
                ->get(),
            'citasProximas' => Cita::with('paciente')
                ->where('dentista_id', $dentista->id)
                ->whereDate('fecha', '>', $hoy)
                ->where('estado', '!=', 'cancelada')
                ->orderBy('fecha')->orderBy('hora_inicio')
                ->take(10)->get(),
            'tratamientosActivos' => Tratamiento::with('paciente')
                ->where('dentista_id', $dentista->id)
                ->where('estado', 'en_proceso')
                ->orderBy('updated_at', 'desc')
                ->take(8)->get(),
            'citasMes' => Cita::with('paciente')
                ->where('dentista_id', $dentista->id)
                ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
                ->where('estado', '!=', 'cancelada')
                ->orderBy('hora_inicio')
                ->get()
                ->groupBy(fn($c) => Carbon::parse($c->fecha)->day),
            'totalCitasMes'    => Cita::where('dentista_id', $dentista->id)
                ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
                ->count(),
            'mesActual'        => $fechaCalendario->translatedFormat('F Y'),
            'mesAnterior'      => $fechaCalendario->copy()->subMonth()->format('Y-m'),
            'mesSiguiente'     => $fechaCalendario->copy()->addMonth()->format('Y-m'),
            'inicioCalendario' => $inicioMes->copy()->startOfWeek(Carbon::MONDAY),
            'finCalendario'    => $finMes->copy()->endOfWeek(Carbon::SUNDAY),
            'hoy'              => $hoy,
            'fechaCalendario'  => $fechaCalendario,
        ]);
    }

    // ── Paciente ──────────────────────────────────────────────────────
    public function indexPaciente()
    {
        $rol = Auth::user()->rol;
        if ($rol !== 'paciente') return $this->redirectToHome();

        $user    = Auth::user();
        $paciente = $user->paciente;

        if (! $paciente) {
            return redirect()->route('login')->with('error', 'No tiene perfil de paciente asignado.');
        }

        $hoy = Carbon::today();

        $proximaCita = Cita::with('dentista')
            ->where('paciente_id', $paciente->id)
            ->whereDate('fecha', '>=', $hoy)
            ->where('estado', '!=', 'cancelada')
            ->orderBy('fecha')->orderBy('hora_inicio')
            ->first();

        $citasRecientes = Cita::with('dentista')
            ->where('paciente_id', $paciente->id)
            ->orderBy('fecha', 'desc')
            ->take(5)->get();

        $tratamientos = Tratamiento::with('dentista')
            ->where('paciente_id', $paciente->id)
            ->orderBy('updated_at', 'desc')
            ->take(5)->get();

        $notificaciones = Notificacion::where('paciente_id', $paciente->id)
            ->orderBy('created_at', 'desc')
            ->take(5)->get();

        $noLeidas = Notificacion::where('paciente_id', $paciente->id)
            ->where('leida', false)
            ->count();

        return view('dashboard-paciente', compact(
            'paciente', 'proximaCita', 'citasRecientes',
            'tratamientos', 'notificaciones', 'noLeidas', 'hoy'
        ));
    }
}
