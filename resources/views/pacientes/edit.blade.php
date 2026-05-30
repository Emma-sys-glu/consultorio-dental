@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Editar Paciente</h2>

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

        <form action="{{ route('pacientes.actualizar', $paciente) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $paciente->nombre) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Apellido paterno</label>
                    <input type="text" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno', $paciente->apellido_paterno) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Apellido materno</label>
                    <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno', $paciente->apellido_materno) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $paciente->telefono) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" value="{{ old('correo', $paciente->correo) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">CURP</label>
                    <input type="text" name="curp" class="form-control" value="{{ old('curp', $paciente->curp) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tipo de sangre</label>
                    <select name="tipo_sangre" class="form-select">
                        <option value="">Seleccionar</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                            <option value="{{ $tipo }}" @selected(old('tipo_sangre', $paciente->tipo_sangre) == $tipo)>
                                {{ $tipo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Alergias</label>
                    <input type="text" name="alergias" class="form-control" value="{{ old('alergias', $paciente->alergias) }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Antecedentes médicos</label>
                    <textarea name="antecedentes_medicos" class="form-control" rows="3">{{ old('antecedentes_medicos', $paciente->antecedentes_medicos) }}</textarea>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">
                    Actualizar paciente
                </button>

                <a href="{{ route('pacientes.vista') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@endsection