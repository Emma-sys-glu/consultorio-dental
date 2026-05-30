@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Pacientes</h2>

    <a href="{{ route('pacientes.crear') }}" class="btn btn-primary">
        Nuevo Paciente
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
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Tipo Sangre</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

            @foreach($pacientes as $paciente)

                <tr>

                    <td>
                        {{ $paciente->id }}
                    </td>

                    <td>
                        {{ $paciente->nombre }}
                        {{ $paciente->apellido_paterno }}
                    </td>

                    <td>
                        {{ $paciente->telefono }}
                    </td>

                    <td>
                        {{ $paciente->correo }}
                    </td>

                    <td>
                        {{ $paciente->tipo_sangre }}
                    </td>

                    <td>
                        <a href="{{ route('pacientes.editar', $paciente) }}" class="btn btn-warning btn-sm">
                            Editar
                        </a>

                        <form action="{{ route('pacientes.eliminar', $paciente) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este paciente?')">
                                Eliminar
                            </button>
                        </form>
                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

        <div class="mt-3">
            {{ $pacientes->links() }}
        </div>

    </div>

</div>

@endsection