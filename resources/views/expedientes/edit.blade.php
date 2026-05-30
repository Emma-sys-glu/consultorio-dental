@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Editar Expediente Clínico</h2>

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

        <form action="{{ route('expedientes.actualizar', $expediente) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Paciente</label>
                <input type="text" class="form-control" value="{{ $expediente->paciente->nombre ?? '' }} {{ $expediente->paciente->apellido_paterno ?? '' }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Diagnóstico</label>
                <textarea name="diagnostico" class="form-control" rows="3">{{ old('diagnostico', $expediente->diagnostico) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $expediente->observaciones) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Procedimientos realizados</label>
                <textarea name="procedimientos_realizados" class="form-control" rows="3">{{ old('procedimientos_realizados', $expediente->procedimientos_realizados) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Evolución del tratamiento</label>
                <textarea name="evolucion_tratamiento" class="form-control" rows="3">{{ old('evolucion_tratamiento', $expediente->evolucion_tratamiento) }}</textarea>
            </div>

            <button class="btn btn-success">Actualizar expediente</button>
            <a href="{{ route('expedientes.vista') }}" class="btn btn-secondary">Cancelar</a>

        </form>

    </div>
</div>

@endsection