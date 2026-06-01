@extends('layouts.app')

@section('title', 'Citas - DentalTec')

@section('content')

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Citas Dentales</h2>
        <p class="text-muted mb-0">Listado de citas registradas.</p>
    </div>

    <a href="{{ route('citas.crear') }}" class="btn btn-primary">
        Nueva Cita
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Dentista</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($citas as $cita)
                    <tr>
                        <td>{{ $cita->id }}</td>

                        <td>
                            {{ $cita->paciente->nombre ?? 'Sin paciente' }}
                            {{ $cita->paciente->apellido_paterno ?? '' }}
                        </td>

                        <td>
                            {{ $cita->dentista->nombre ?? 'Sin dentista' }}
                            {{ $cita->dentista->apellido_paterno ?? '' }}
                        </td>

                        <td>{{ $cita->fecha }}</td>

                        <td>
                            {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}
                        </td>

                        <td>{{ $cita->motivo }}</td>

                        <td>
                            @if($cita->estado == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif($cita->estado == 'confirmada')
                                <span class="badge bg-success">Confirmada</span>
                            @elseif($cita->estado == 'cancelada')
                                <span class="badge bg-danger">Cancelada</span>
                            @else
                                <span class="badge bg-secondary">Finalizada</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('citas.editar', $cita) }}" class="btn btn-secondary btn-sm">
                                    Editar
                                </a>

                                <form action="{{ route('citas.cancelar', $cita) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <button class="btn btn-secondary btn-sm" onclick="return confirm('¿Seguro que deseas cancelar esta cita?')">
                                        Cancelar
                                    </button>
                                </form>

                                <form action="{{ route('citas.eliminar', $cita) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta cita?')">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $citas->links() }}
        </div>
    </div>
</div>

@endsection
