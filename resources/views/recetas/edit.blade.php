@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Editar Receta</h2>

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

        <form action="{{ route('recetas.actualizar', $receta) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Paciente</label>
                    <select name="paciente_id" class="form-select" required>
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}" @selected(old('paciente_id', $receta->paciente_id) == $paciente->id)>
                                {{ $paciente->nombre }} {{ $paciente->apellido_paterno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dentista</label>
                    <select name="dentista_id" class="form-select" required>
                        @foreach($dentistas as $dentista)
                            <option value="{{ $dentista->id }}" @selected(old('dentista_id', $receta->dentista_id) == $dentista->id)>
                                {{ $dentista->nombre }} {{ $dentista->apellido_paterno }} - {{ $dentista->especialidad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Tratamiento relacionado</label>
                    <select name="tratamiento_id" class="form-select">
                        <option value="">Sin tratamiento relacionado</option>
                        @foreach($tratamientos as $tratamiento)
                            <option value="{{ $tratamiento->id }}" @selected(old('tratamiento_id', $receta->tratamiento_id) == $tratamiento->id)>
                                Tratamiento #{{ $tratamiento->id }} - {{ $tratamiento->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Medicamento</label>
                    <input type="text" name="medicamento" class="form-control" value="{{ old('medicamento', $receta->medicamento) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dosis</label>
                    <input type="text" name="dosis" class="form-control" value="{{ old('dosis', $receta->dosis) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Frecuencia</label>
                    <input type="text" name="frecuencia" class="form-control" value="{{ old('frecuencia', $receta->frecuencia) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Duración</label>
                    <input type="text" name="duracion" class="form-control" value="{{ old('duracion', $receta->duracion) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha de emisión</label>
                    <input type="date" name="fecha_emision" class="form-control" value="{{ old('fecha_emision', $receta->fecha_emision) }}" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Indicaciones</label>
                    <textarea name="indicaciones" class="form-control" rows="3">{{ old('indicaciones', $receta->indicaciones) }}</textarea>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">Actualizar receta</button>
                <a href="{{ route('recetas.vista') }}" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>

    </div>
</div>

@endsection