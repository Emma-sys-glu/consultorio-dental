@extends('layouts.app')

@section('title', 'Expedientes - DentalCare')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="mb-1">Expedientes clinicos</h2>
        <p class="text-muted mb-0">Historia medica, seguimiento y documentos del paciente.</p>
    </div>

    <a href="{{ route('expedientes.crear') }}" class="btn btn-info mt-3 mt-md-0">
        Nuevo expediente
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('expedientes.vista') }}" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label class="form-label">Buscar por paciente</label>
                <input type="search" name="buscar" class="form-control" value="{{ $buscar }}" placeholder="Nombre o apellidos">
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill">Buscar</button>
                @if($buscar)
                    <a href="{{ route('expedientes.vista') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Lista de expedientes</strong>
        <span class="text-muted small">{{ $expedientes->total() }} registros</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Paciente</th>
                        <th>Diagnostico</th>
                        <th>Seguimiento</th>
                        <th>Documentos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($expedientes as $expediente)
                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $expediente->paciente->nombre ?? 'Sin paciente' }}
                                    {{ $expediente->paciente->apellido_paterno ?? '' }}
                                    {{ $expediente->paciente->apellido_materno ?? '' }}
                                </div>
                                <div class="small text-muted">Expediente #{{ $expediente->id }}</div>
                            </td>

                            <td style="max-width: 260px;">
                                <div class="text-truncate">{{ $expediente->diagnostico ?: 'Sin diagnostico registrado' }}</div>
                                <div class="small text-muted text-truncate">{{ $expediente->observaciones ?: 'Sin observaciones' }}</div>
                            </td>

                            <td style="max-width: 260px;">
                                <div class="text-truncate">{{ $expediente->procedimientos_realizados ?: 'Sin procedimientos registrados' }}</div>
                                <div class="small text-muted text-truncate">{{ $expediente->evolucion_tratamiento ?: 'Sin evolucion registrada' }}</div>
                            </td>

                            <td>
                                <span class="badge text-bg-light border">
                                    {{ $expediente->documentos->count() }} archivos
                                </span>
                            </td>

                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('expedientes.editar', $expediente) }}" class="btn btn-primary btn-sm">Abrir</a>

                                    <form action="{{ route('expedientes.eliminar', $expediente) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este expediente?')">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No se encontraron expedientes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($expedientes->hasPages())
        <div class="card-footer bg-white">
            {{ $expedientes->links() }}
        </div>
    @endif
</div>

@endsection
