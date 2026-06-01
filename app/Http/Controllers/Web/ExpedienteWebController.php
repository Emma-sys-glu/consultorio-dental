<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\ExpedienteDocumento;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpedienteWebController extends Controller
{
    public function index(Request $request)
    {
        $buscar = strtolower(trim((string) $request->query('buscar', '')));

        $expedientes = Expediente::with(['paciente', 'documentos'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->whereHas('paciente', function ($q) use ($buscar) {
                    $q->whereRaw('LOWER(nombre) LIKE ?', ["%{$buscar}%"])
                        ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
                        ->orWhereRaw('LOWER(COALESCE(apellido_materno, \'\')) LIKE ?', ["%{$buscar}%"])
                        ->orWhereRaw("LOWER(CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno, ''))) LIKE ?", ["%{$buscar}%"]);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('expedientes.index', [
            'expedientes' => $expedientes,
            'buscar' => $buscar,
        ]);
    }

    public function create()
    {
        return view('expedientes.create', [
            'pacientes' => Paciente::doesntHave('expediente')
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validarExpediente($request, true);

        $expediente = Expediente::create($this->datosExpediente($datos));
        $this->guardarDocumentos($request, $expediente);

        return redirect()->route('expedientes.vista')
            ->with('success', 'Expediente creado correctamente');
    }

    public function edit(Expediente $expediente)
    {
        return view('expedientes.edit', [
            'expediente' => $expediente->load(['paciente', 'documentos']),
        ]);
    }

    public function update(Request $request, Expediente $expediente)
    {
        $datos = $this->validarExpediente($request);

        $expediente->update($this->datosExpediente($datos));
        $this->guardarDocumentos($request, $expediente);

        return redirect()->route('expedientes.vista')
            ->with('success', 'Expediente actualizado correctamente');
    }

    public function destroy(Expediente $expediente)
    {
        $this->eliminarArchivosDelExpediente($expediente);
        $expediente->delete();

        return redirect()->route('expedientes.vista')
            ->with('success', 'Expediente eliminado correctamente');
    }

    public function destroyDocumento(ExpedienteDocumento $documento)
    {
        Storage::disk('public')->delete($documento->ruta);
        $documento->delete();

        return back()->with('success', 'Documento eliminado correctamente');
    }

    private function validarExpediente(Request $request, bool $crear = false): array
    {
        $reglas = [
            'diagnostico' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'procedimientos_realizados' => 'nullable|string',
            'evolucion_tratamiento' => 'nullable|string',
            'tipo_documento' => 'nullable|string|max:100',
            'documentos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        if ($crear) {
            $reglas = ['paciente_id' => 'required|exists:pacientes,id|unique:expedientes,paciente_id'] + $reglas;
        }

        return $request->validate($reglas);
    }

    private function guardarDocumentos(Request $request, Expediente $expediente): void
    {
        if (!$request->hasFile('documentos')) {
            return;
        }

        $tipo = $request->input('tipo_documento') ?: 'Documento clinico';

        foreach ($request->file('documentos') as $archivo) {
            if (!$archivo || !$archivo->isValid()) {
                continue;
            }

            $ruta = $archivo->store('expedientes/' . $expediente->id, 'public');

            $expediente->documentos()->create([
                'tipo' => $tipo,
                'nombre_original' => $archivo->getClientOriginalName(),
                'ruta' => $ruta,
                'mime_type' => $archivo->getClientMimeType(),
                'tamano' => $archivo->getSize(),
            ]);
        }
    }

    private function datosExpediente(array $datos): array
    {
        unset($datos['tipo_documento'], $datos['documentos']);

        return $datos;
    }

    private function eliminarArchivosDelExpediente(Expediente $expediente): void
    {
        foreach ($expediente->documentos as $documento) {
            Storage::disk('public')->delete($documento->ruta);
        }
    }
}
