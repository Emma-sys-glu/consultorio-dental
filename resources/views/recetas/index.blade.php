@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Recetas Médicas</h2>

    @if(auth()->user()->rol !== 'paciente')
    <a href="{{ route('recetas.crear') }}" class="btn btn-primary">
        Nueva Receta
    </a>
@endif
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
                    <th>Medicamento</th>
                    <th>Dosis</th>
                    <th>Frecuencia</th>
                    <th>Fecha</th>
                    @if(auth()->user()->rol !== 'paciente')
                        <th>Acciones</th>
                    @endif
                </tr>
            </thead>

            <tbody>
            @foreach($recetas as $receta)
                <tr>
                    <td>{{ $receta->id }}</td>

                    <td>
                        {{ $receta->paciente->nombre ?? 'Sin paciente' }}
                        {{ $receta->paciente->apellido_paterno ?? '' }}
                    </td>

                    <td>
                        {{ $receta->dentista->nombre ?? 'Sin dentista' }}
                        {{ $receta->dentista->apellido_paterno ?? '' }}
                    </td>

                    <td>{{ $receta->medicamento }}</td>
                    <td>{{ $receta->dosis }}</td>
                    <td>{{ $receta->frecuencia }}</td>
                    <td>{{ $receta->fecha_emision }}</td>
                    @if(auth()->user()->rol !== 'paciente')
                        <td>
                            <a href="{{ route('recetas.editar', $receta) }}" class="btn btn-warning btn-sm">
                                Editar
                            </a>

                            <form action="{{ route('recetas.eliminar', $receta) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta receta?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>

        <div class="mt-3">
            {{ $recetas->links() }}
        </div>

    </div>
</div>

@endsection