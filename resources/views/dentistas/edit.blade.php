@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Editar Dentista</h2>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Revisa los datos ingresados.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dentistas.actualizar', $dentista) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $dentista->nombre) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Apellido paterno</label>
                    <input type="text" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno', $dentista->apellido_paterno) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Apellido materno</label>
                    <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno', $dentista->apellido_materno) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Especialidad</label>
                    <select name="especialidad" class="form-select" required>
                        @foreach(['Odontología general', 'Ortodoncia', 'Endodoncia', 'Periodoncia', 'Cirugía dental', 'Odontopediatría'] as $especialidad)
                            <option value="{{ $especialidad }}" @selected(old('especialidad', $dentista->especialidad) == $especialidad)>
                                {{ $especialidad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Cédula profesional</label>
                    <input type="text" name="cedula_profesional" class="form-control" value="{{ old('cedula_profesional', $dentista->cedula_profesional) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $dentista->telefono) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" value="{{ old('correo', $dentista->correo) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Horario inicio</label>
                    <input type="time" name="horario_inicio" class="form-control" value="{{ old('horario_inicio', $dentista->horario_inicio) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Horario fin</label>
                    <input type="time" name="horario_fin" class="form-control" value="{{ old('horario_fin', $dentista->horario_fin) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Consultorio</label>
                    <input type="text" name="consultorio" class="form-control" value="{{ old('consultorio', $dentista->consultorio) }}" required>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">
                    Actualizar dentista
                </button>

                <a href="{{ route('dentistas.vista') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@endsection