@extends('layouts.app')

@section('title', 'Detalle Expediente - DentalTec')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="mb-1">Expediente clínico</h2>
        <p class="text-muted mb-0">
            {{ $expediente->paciente->nombre ?? '' }}
            {{ $expediente->paciente->apellido_paterno ?? '' }}
            {{ $expediente->paciente->apellido_materno ?? '' }}
            · Expediente #{{ $expediente->id }}
        </p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        @if(in_array(auth()->user()->rol, ['administrador', 'dentista']))
            <a href="{{ route('expedientes.editar', $expediente) }}" class="btn btn-primary btn-sm">Editar</a>
        @endif
        <a href="{{ route('expedientes.vista') }}" class="btn btn-secondary btn-sm">Volver</a>
    </div>
</div>

<div class="row g-4">

    {{-- Datos del paciente --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Paciente</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Nombre completo</div>
                        <div class="fw-semibold">
                            {{ $expediente->paciente->nombre ?? '—' }}
                            {{ $expediente->paciente->apellido_paterno ?? '' }}
                            {{ $expediente->paciente->apellido_materno ?? '' }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Teléfono</div>
                        <div>{{ $expediente->paciente->telefono ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Correo</div>
                        <div>{{ $expediente->paciente->correo ?? '—' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-muted small">Tipo de sangre</div>
                        <div>
                            @if($expediente->paciente->tipo_sangre ?? false)
                                <span class="badge text-bg-light border">{{ $expediente->paciente->tipo_sangre }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                    @if($expediente->paciente->alergias ?? false)
                    <div class="col-md-12">
                        <div class="text-muted small">Alergias</div>
                        <div class="text-danger fw-semibold">{{ $expediente->paciente->alergias }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen clínico --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Resumen clínico</div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="text-muted small mb-1">Diagnóstico</div>
                    <div>{{ $expediente->diagnostico ?: 'Sin diagnóstico registrado.' }}</div>
                </div>
                <div class="mb-4">
                    <div class="text-muted small mb-1">Observaciones clínicas</div>
                    <div>{{ $expediente->observaciones ?: 'Sin observaciones.' }}</div>
                </div>
                <div class="mb-4">
                    <div class="text-muted small mb-1">Procedimientos realizados</div>
                    <div>{{ $expediente->procedimientos_realizados ?: 'Sin procedimientos registrados.' }}</div>
                </div>
                <div class="mb-0">
                    <div class="text-muted small mb-1">Evolución del tratamiento</div>
                    <div>{{ $expediente->evolucion_tratamiento ?: 'Sin evolución registrada.' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Documentos --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Documentos adjuntos</span>
                <span class="badge text-bg-secondary">{{ $expediente->documentos->count() }}</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse($expediente->documentos as $documento)
                    <div class="list-group-item">
                        <div class="fw-semibold text-truncate">{{ $documento->nombre_original }}</div>
                        <div class="small text-muted mb-2">
                            {{ $documento->tipo }} · {{ number_format($documento->tamano / 1024, 1) }} KB
                        </div>
                        <a href="{{ asset('storage/' . $documento->ruta) }}" target="_blank"
                           class="btn btn-outline-primary btn-sm">
                            Abrir
                        </a>
                    </div>
                @empty
                    <div class="list-group-item text-muted small py-3">
                        Sin documentos adjuntos.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection
