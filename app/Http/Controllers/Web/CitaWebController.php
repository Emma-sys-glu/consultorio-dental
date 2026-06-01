<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CitaController;
use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CitaWebController extends Controller
{
    public function index(Request $request)
    {
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
        $finMes = $fechaCalendario->copy()->endOfMonth();

        return view('citas.index', [
            'citas' => Cita::with(['paciente', 'dentista'])
                ->orderBy('fecha', 'desc')
                ->paginate(20)
                ->withQueryString(),
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
            'inicioCalendario' => $inicioMes->copy()->startOfWeek(Carbon::MONDAY),
            'finCalendario' => $finMes->copy()->endOfWeek(Carbon::SUNDAY),
            'hoy' => $hoy,
            'fechaCalendario' => $fechaCalendario,
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
