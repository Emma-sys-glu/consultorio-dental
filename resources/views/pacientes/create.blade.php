@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Registrar Paciente</h2>

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

        <form action="{{ route('pacientes.guardar') }}" method="POST">
            @csrf

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
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" required>
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
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Alergias</label>
                    <input type="text" name="alergias" class="form-control" value="{{ old('alergias') }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Antecedentes médicos</label>
                    <textarea name="antecedentes_medicos" class="form-control" rows="3">{{ old('antecedentes_medicos') }}</textarea>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">
                    Guardar paciente
                </button>

                <a href="{{ route('pacientes.vista') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@endsection