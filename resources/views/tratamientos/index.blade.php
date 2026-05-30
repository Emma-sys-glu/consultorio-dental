@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Tratamientos</h2>

    <a href="{{ route('tratamientos.crear') }}" class="btn btn-primary">
        Nuevo Tratamiento
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
                    <th>Tratamiento</th>
                    <th>Costo</th>
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
            @foreach($tratamientos as $tratamiento)
                <tr>
                    <td>{{ $tratamiento->id }}</td>

                    <td>
                        {{ $tratamiento->paciente->nombre ?? 'Sin paciente' }}
                        {{ $tratamiento->paciente->apellido_paterno ?? '' }}
                    </td>

                    <td>
                        {{ $tratamiento->dentista->nombre ?? 'Sin dentista' }}
                        {{ $tratamiento->dentista->apellido_paterno ?? '' }}
                    </td>

                    <td>{{ $tratamiento->nombre }}</td>
                    <td>${{ number_format($tratamiento->costo, 2) }}</td>

                    <td>
                        @if($tratamiento->estado == 'pendiente')
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @elseif($tratamiento->estado == 'en_proceso')
                            <span class="badge bg-info">En proceso</span>
                        @elseif($tratamiento->estado == 'finalizado')
                            <span class="badge bg-success">Finalizado</span>
                        @else
                            <span class="badge bg-danger">Cancelado</span>
                        @endif
                    </td>

                    <td>{{ $tratamiento->fecha_inicio }}</td>
                    <td>
                        <a href="{{ route('tratamientos.editar', $tratamiento) }}" class="btn btn-warning btn-sm">
                            Editar
                        </a>

                        <form action="{{ route('tratamientos.eliminar', $tratamiento) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este tratamiento?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $tratamientos->links() }}
        </div>

    </div>
</div>

@endsection