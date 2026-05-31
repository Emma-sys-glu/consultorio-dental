@extends('layouts.app')

@section('title', 'Nuevo paciente - DentalTec')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Nuevo paciente</h2>
        <p class="text-muted mb-0">Registra los datos generales y antecedentes del paciente.</p>
    </div>

    <a href="{{ route('pacientes.vista') }}" class="btn btn-outline-secondary">Volver</a>
</div>

<form action="{{ route('pacientes.guardar') }}" method="POST">
    @csrf

    <div class="card mb-4">
        <div class="card-header bg-white">
            <strong>Datos personales</strong>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Apellido paterno</label>
                    <input type="text" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Apellido materno</label>
                    <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">CURP</label>
                    <input type="text" name="curp" class="form-control" value="{{ old('curp') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tipo de sangre</label>
                    <select name="tipo_sangre" class="form-select">
                        <option value="">Seleccionar</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $tipo)
                            <option value="{{ $tipo }}" @selected(old('tipo_sangre') == $tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white">
            <strong>Contacto y antecedentes</strong>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Telefono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Alergias</label>
                    <input type="text" name="alergias" class="form-control" value="{{ old('alergias') }}" placeholder="Ej. Penicilina, latex, anestesia">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Antecedentes medicos</label>
                    <textarea name="antecedentes_medicos" class="form-control" rows="4">{{ old('antecedentes_medicos') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('pacientes.vista') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary">Guardar paciente</button>
    </div>
</form>

@endsection
