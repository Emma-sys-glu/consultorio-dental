@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Expedientes Clínicos</h2>

    <a href="{{ route('expedientes.crear') }}" class="btn btn-primary">
        Nuevo Expediente
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
                    <th>Diagnóstico</th>
                    <th>Observaciones</th>
                    <th>Procedimientos</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
            @foreach($expedientes as $expediente)
                <tr>
                    <td>{{ $expediente->id }}</td>

                    <td>
                        {{ $expediente->paciente->nombre ?? 'Sin paciente' }}
                        {{ $expediente->paciente->apellido_paterno ?? '' }}
                    </td>

                    <td>{{ $expediente->diagnostico }}</td>
                    <td>{{ $expediente->observaciones }}</td>
                    <td>{{ $expediente->procedimientos_realizados }}</td>
                    <td>
                        <a href="{{ route('expedientes.editar', $expediente) }}" class="btn btn-secondary btn-sm">
                            Editar
                        </a>

                        <form action="{{ route('expedientes.eliminar', $expediente) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este expediente?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>

        <div class="mt-3">
            {{ $expedientes->links() }}
        </div>

    </div>
</div>

@endsection