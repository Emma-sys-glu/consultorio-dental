@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Dentistas</h2>

    <a href="{{ route('dentistas.crear') }}" class="btn btn-primary">
        Nuevo Dentista
    </a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Cédula</th>
                    <th>Horario</th>
                    <th>Consultorio</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
            @foreach($dentistas as $dentista)
                <tr>
                    <td>{{ $dentista->id }}</td>
                    <td>{{ $dentista->nombre }} {{ $dentista->apellido_paterno }}</td>
                    <td>{{ $dentista->especialidad }}</td>
                    <td>{{ $dentista->cedula_profesional }}</td>
                    <td>{{ $dentista->horario_inicio }} - {{ $dentista->horario_fin }}</td>
                    <td>{{ $dentista->consultorio }}</td>
                    <td>
                        <a href="{{ route('dentistas.editar', $dentista) }}" class="btn btn-secondary btn-sm">
                            Editar
                        </a>

                        <form action="{{ route('dentistas.eliminar', $dentista) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este dentista?')">
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
            {{ $dentistas->links() }}
        </div>

    </div>
</div>

@endsection