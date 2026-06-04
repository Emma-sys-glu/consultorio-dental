@extends('layouts.app')

@section('title', 'Detalle Paciente - DentalTec')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="mb-1">
            {{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}
        </h2>
        <p class="text-muted mb-0">Expediente del paciente · ID #{{ $paciente->id }}</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        @if(in_array(auth()->user()->rol, ['administrador', 'recepcionista']))
            <a href="{{ route('pacientes.editar', $paciente) }}" class="btn btn-primary btn-sm">Editar datos</a>
        @endif
        <a href="{{ route('pacientes.vista') }}" class="btn btn-secondary btn-sm">Volver</a>
    </div>
</div>

{{-- Datos personales --}}
<div class="card mb-4">
    <div class="card-header bg-white fw-semibold">Datos personales</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Teléfono</div>
                <div>{{ $paciente->telefono }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Correo</div>
                <div>{{ $paciente->correo }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Fecha de nacimiento</div>
                <div>{{ $paciente->fecha_nacimiento }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">CURP</div>
                <div>{{ $paciente->curp ?: '—' }}</div>
            </div>
            <div class="col-md-2">
                <div class="text-muted small">Tipo de sangre</div>
                <div>
                    @if($paciente->tipo_sangre)
                        <span class="badge text-bg-light border">{{ $paciente->tipo_sangre }}</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Alergias</div>
                <div class="{{ $paciente->alergias ? 'text-danger fw-semibold' : 'text-muted' }}">
                    {{ $paciente->alergias ?: 'Sin alergias registradas' }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Antecedentes médicos</div>
                <div>{{ $paciente->antecedentes_medicos ?: 'Sin antecedentes registrados' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Expediente clínico --}}
@if($paciente->expediente)
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Expediente clínico</span>
        <a href="{{ route('expedientes.detalle', $paciente->expediente) }}" class="btn btn-info btn-sm">
            Ver detalle
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Diagnóstico</div>
                <div>{{ $paciente->expediente->diagnostico ?: '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Observaciones</div>
                <div>{{ $paciente->expediente->observaciones ?: '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Procedimientos realizados</div>
                <div>{{ $paciente->expediente->procedimientos_realizados ?: '—' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Evolución del tratamiento</div>
                <div>{{ $paciente->expediente->evolucion_tratamiento ?: '—' }}</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Citas recientes --}}
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Citas recientes</span>
        <span class="badge text-bg-secondary">{{ $paciente->citas->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Dentista</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paciente->citas as $cita)
                        <tr>
                            <td>{{ $cita->fecha }}</td>
                            <td>{{ $cita->hora_inicio }}</td>
                            <td>{{ $cita->dentista->nombre ?? '—' }} {{ $cita->dentista->apellido_paterno ?? '' }}</td>
                            <td>{{ $cita->motivo }}</td>
                            <td>
                                @php $e = $cita->estado; @endphp
                                <span class="badge text-bg-{{ $e === 'programada' ? 'primary' : ($e === 'completada' ? 'success' : 'danger') }}">
                                    {{ ucfirst($e) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center py-3">Sin citas registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Tratamientos --}}
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Tratamientos</span>
        <span class="badge text-bg-secondary">{{ $tratamientos->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tratamiento</th>
                        <th>Dentista</th>
                        <th>Inicio</th>
                        <th>Estado</th>
                        <th>Costo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tratamientos as $t)
                        <tr>
                            <td>{{ $t->nombre }}</td>
                            <td>{{ $t->dentista->nombre ?? '—' }} {{ $t->dentista->apellido_paterno ?? '' }}</td>
                            <td>{{ $t->fecha_inicio }}</td>
                            <td>
                                <span class="badge text-bg-{{ $t->estado === 'activo' ? 'success' : ($t->estado === 'en proceso' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($t->estado) }}
                                </span>
                            </td>
                            <td>${{ number_format($t->costo, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center py-3">Sin tratamientos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
