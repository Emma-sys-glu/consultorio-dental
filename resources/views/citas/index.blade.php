@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Citas Dentales</h2>

    <a href="{{ route('citas.crear') }}" class="btn btn-primary">
        Nueva Cita
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

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
                        <a href="{{ route('citas.editar', $cita) }}" class="btn btn-warning btn-sm">
                            Editar
                        </a>

                        <form action="{{ route('citas.cancelar', $cita) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')

                            <button class="btn btn-secondary btn-sm" onclick="return confirm('¿Seguro que deseas cancelar esta cita?')">
                                Cancelar
                            </button>
                        </form>

                        <form action="{{ route('citas.eliminar', $cita) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta cita?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $citas->links() }}
        </div>

    </div>
</div>

@endsection