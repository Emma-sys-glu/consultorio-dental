@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Crear Expediente Clínico</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('expedientes.guardar') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Paciente</label>
                <select name="paciente_id" class="form-select" required>
                    <option value="">Seleccionar paciente</option>
                    @foreach($pacientes as $paciente)
                        <option value="{{ $paciente->id }}">
                            {{ $paciente->nombre }} {{ $paciente->apellido_paterno }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Diagnóstico</label>
                <textarea name="diagnostico" class="form-control" rows="3">{{ old('diagnostico') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Procedimientos realizados</label>
                <textarea name="procedimientos_realizados" class="form-control" rows="3">{{ old('procedimientos_realizados') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Evolución del tratamiento</label>
                <textarea name="evolucion_tratamiento" class="form-control" rows="3">{{ old('evolucion_tratamiento') }}</textarea>
            </div>

            <button class="btn btn-success">Guardar expediente</button>
            <a href="{{ route('expedientes.vista') }}" class="btn btn-secondary">Cancelar</a>

        </form>

    </div>
</div>

@endsection