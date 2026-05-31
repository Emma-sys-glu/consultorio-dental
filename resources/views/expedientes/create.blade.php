@extends('layouts.app')

@section('title', 'Nuevo expediente - DentalCare')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Nuevo expediente clinico</h2>
        <p class="text-muted mb-0">Registra informacion medica y adjunta documentos del paciente.</p>
    </div>

    <a href="{{ route('expedientes.vista') }}" class="btn btn-outline-secondary">Volver</a>
</div>

@if($pacientes->isEmpty())
    <div class="alert alert-info">
        Todos los pacientes ya tienen expediente registrado.
    </div>
@endif

<form action="{{ route('expedientes.guardar') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <strong>Datos del expediente</strong>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <select name="paciente_id" class="form-select" required>
                            <option value="">Seleccionar paciente</option>
                            @foreach($pacientes as $paciente)
                                <option value="{{ $paciente->id }}" @selected(old('paciente_id') == $paciente->id)>
                                    {{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Solo aparecen pacientes sin expediente.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Diagnostico inicial</label>
                        <textarea name="diagnostico" class="form-control" rows="3" placeholder="Ej. Caries, gingivitis, sensibilidad dental...">{{ old('diagnostico') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones clinicas</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Notas generales de exploracion, sintomas o comentarios relevantes.">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Procedimientos realizados</label>
                        <textarea name="procedimientos_realizados" class="form-control" rows="3" placeholder="Limpieza, extraccion, resina, radiografia, etc.">{{ old('procedimientos_realizados') }}</textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Evolucion del tratamiento</label>
                        <textarea name="evolucion_tratamiento" class="form-control" rows="3" placeholder="Seguimiento, avances, recomendaciones o proxima revision.">{{ old('evolucion_tratamiento') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <strong>Documentos clinicos</strong>
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

                    <div class="mb-3">
                        <label class="form-label">Subir archivos</label>
                        <input type="file" name="documentos[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" multiple>
                        <div class="form-text">PDF, JPG o PNG. Maximo 5 MB por archivo.</div>
                    </div>

                    <div class="border rounded p-3 bg-light small">
                        <strong>Ejemplos de adjuntos:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Radiografias.</li>
                            <li>Consentimientos firmados.</li>
                            <li>Resultados de laboratorio.</li>
                            <li>Documentos enviados por otro especialista.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-primary" @disabled($pacientes->isEmpty())>Guardar expediente</button>
                <a href="{{ route('expedientes.vista') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</form>

@endsection
