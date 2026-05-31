@extends('layouts.app')

@section('title', 'Editar expediente - DentalCare')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Editar expediente clinico</h2>
        <p class="text-muted mb-0">
            {{ $expediente->paciente->nombre ?? '' }}
            {{ $expediente->paciente->apellido_paterno ?? '' }}
            {{ $expediente->paciente->apellido_materno ?? '' }}
        </p>
    </div>

    <a href="{{ route('expedientes.vista') }}" class="btn btn-outline-secondary">Volver</a>
</div>

<form action="{{ route('expedientes.actualizar', $expediente) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <strong>Resumen clinico</strong>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <input type="text" class="form-control" value="{{ $expediente->paciente->nombre ?? '' }} {{ $expediente->paciente->apellido_paterno ?? '' }} {{ $expediente->paciente->apellido_materno ?? '' }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Diagnostico</label>
                        <textarea name="diagnostico" class="form-control" rows="3">{{ old('diagnostico', $expediente->diagnostico) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones clinicas</label>
                        <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $expediente->observaciones) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Procedimientos realizados</label>
                        <textarea name="procedimientos_realizados" class="form-control" rows="3">{{ old('procedimientos_realizados', $expediente->procedimientos_realizados) }}</textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Evolucion del tratamiento</label>
                        <textarea name="evolucion_tratamiento" class="form-control" rows="3">{{ old('evolucion_tratamiento', $expediente->evolucion_tratamiento) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <strong>Agregar documentos</strong>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tipo de documento</label>
                        <select name="tipo_documento" class="form-select">
                            <option value="Radiografia">Radiografia</option>
                            <option value="Consentimiento informado">Consentimiento informado</option>
                            <option value="Estudio de laboratorio">Estudio de laboratorio</option>
                            <option value="Receta externa">Receta externa</option>
                            <option value="Documento clinico" selected>Documento clinico</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Subir archivos</label>
                        <input type="file" name="documentos[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        <div class="form-text">PDF, JPG o PNG. Maximo 5 MB por archivo.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('expedientes.vista') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary">Actualizar expediente</button>
    </div>
</form>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between">
        <strong>Documentos adjuntos</strong>
        <span class="badge text-bg-light">{{ $expediente->documentos->count() }}</span>
    </div>

    <div class="list-group list-group-flush">
        @forelse($expediente->documentos as $documento)
            <div class="list-group-item">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                    <div class="text-truncate">
                        <strong class="d-block text-truncate">{{ $documento->nombre_original }}</strong>
                        <span class="small text-muted">
                            {{ $documento->tipo }} · {{ number_format($documento->tamano / 1024, 1) }} KB
                        </span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ asset('storage/' . $documento->ruta) }}" target="_blank" class="btn btn-outline-primary btn-sm">Abrir</a>
                        <form action="{{ route('expedientes.documentos.eliminar', $documento) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar este documento?')">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="list-group-item text-muted">
                Este expediente no tiene documentos adjuntos.
            </div>
        @endforelse
    </div>
</div>

@endsection
