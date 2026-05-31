<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\ExpedienteDocumento;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpedienteController extends Controller
{
    public function index()
    {
        return response()->json([
            'mensaje' => 'Lista de expedientes',
            'data' => Expediente::with(['paciente', 'documentos'])
                ->orderBy('id', 'desc')
                ->paginate(20)
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id|unique:expedientes,paciente_id',
            'diagnostico' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'procedimientos_realizados' => 'nullable|string',
            'evolucion_tratamiento' => 'nullable|string',
            'tipo_documento' => 'nullable|string|max:100',
            'documentos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $expediente = Expediente::create($this->datosExpediente($datos));
        $this->guardarDocumentos($request, $expediente);

        return response()->json([
            'mensaje' => 'Expediente creado correctamente',
            'data' => $expediente->load(['paciente', 'documentos'])
        ], 201);
    }

    public function show(Expediente $expediente)
    {
        return response()->json([
            'mensaje' => 'Expediente encontrado',
            'data' => $expediente->load(['paciente', 'documentos'])
        ]);
    }

    public function update(Request $request, Expediente $expediente)
    {
        $datos = $request->validate([
            'diagnostico' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'procedimientos_realizados' => 'nullable|string',
            'evolucion_tratamiento' => 'nullable|string',
            'tipo_documento' => 'nullable|string|max:100',
            'documentos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $expediente->update($this->datosExpediente($datos));
        $this->guardarDocumentos($request, $expediente);

        return response()->json([
            'mensaje' => 'Expediente actualizado correctamente',
            'data' => $expediente->load(['paciente', 'documentos'])
        ]);
    }

    public function destroy(Expediente $expediente)
    {
        $this->eliminarArchivosDelExpediente($expediente);
        $expediente->delete();

        return response()->json([
            'mensaje' => 'Expediente eliminado correctamente'
        ]);
    }

    public function indexWeb(Request $request)
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

    public function createWeb()
    {
        return view('expedientes.create', [
            'pacientes' => Paciente::doesntHave('expediente')
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    public function storeWeb(Request $request)
    {
        $datos = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id|unique:expedientes,paciente_id',
            'diagnostico' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'procedimientos_realizados' => 'nullable|string',
            'evolucion_tratamiento' => 'nullable|string',
            'tipo_documento' => 'nullable|string|max:100',
            'documentos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $expediente = Expediente::create($this->datosExpediente($datos));
        $this->guardarDocumentos($request, $expediente);

        return redirect()->route('expedientes.vista')
            ->with('success', 'Expediente creado correctamente');
    }

    public function editWeb(Expediente $expediente)
    {
        return view('expedientes.edit', [
            'expediente' => $expediente->load(['paciente', 'documentos']),
        ]);
    }

    public function updateWeb(Request $request, Expediente $expediente)
    {
        $datos = $request->validate([
            'diagnostico' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'procedimientos_realizados' => 'nullable|string',
            'evolucion_tratamiento' => 'nullable|string',
            'tipo_documento' => 'nullable|string|max:100',
            'documentos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $expediente->update($this->datosExpediente($datos));
        $this->guardarDocumentos($request, $expediente);

        return redirect()->route('expedientes.vista')
            ->with('success', 'Expediente actualizado correctamente');
    }

    public function destroyWeb(Expediente $expediente)
    {
        $this->eliminarArchivosDelExpediente($expediente);
        $expediente->delete();

        return redirect()->route('expedientes.vista')
            ->with('success', 'Expediente eliminado correctamente');
    }

    public function destroyDocumentoWeb(ExpedienteDocumento $documento)
    {
        Storage::disk('public')->delete($documento->ruta);
        $documento->delete();

        return back()->with('success', 'Documento eliminado correctamente');
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
