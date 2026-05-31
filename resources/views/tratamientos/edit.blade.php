@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Editar Tratamiento</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('tratamientos.actualizar', $tratamiento) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Paciente</label>
                    <select name="paciente_id" class="form-select" required>
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}" @selected(old('paciente_id', $tratamiento->paciente_id) == $paciente->id)>
                                {{ $paciente->nombre }} {{ $paciente->apellido_paterno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dentista</label>
                    <select name="dentista_id" class="form-select" required>
                        @foreach($dentistas as $dentista)
                            <option value="{{ $dentista->id }}" @selected(old('dentista_id', $tratamiento->dentista_id) == $dentista->id)>
                                {{ $dentista->nombre }} {{ $dentista->apellido_paterno }} - {{ $dentista->especialidad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Expediente</label>
                    <select name="expediente_id" class="form-select" required>
                        @foreach($expedientes as $expediente)
                            <option value="{{ $expediente->id }}" @selected(old('expediente_id', $tratamiento->expediente_id) == $expediente->id)>
                                Expediente #{{ $expediente->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cita relacionada</label>
                    <select name="cita_id" class="form-select">
                        <option value="">Sin cita relacionada</option>
                        @foreach($citas as $cita)
                            <option value="{{ $cita->id }}" @selected(old('cita_id', $tratamiento->cita_id) == $cita->id)>
                                Cita #{{ $cita->id }} - {{ $cita->fecha }} {{ $cita->hora_inicio }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tratamiento</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $tratamiento->nombre) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Costo</label>
                    <input type="number" step="0.01" name="costo" class="form-control" value="{{ old('costo', $tratamiento->costo) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" required>
                        @foreach(['pendiente', 'en_proceso', 'finalizado', 'cancelado'] as $estado)
                            <option value="{{ $estado }}" @selected(old('estado', $tratamiento->estado) == $estado)>
                                {{ ucfirst(str_replace('_', ' ', $estado)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', $tratamiento->fecha_inicio) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin', $tratamiento->fecha_fin) }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $tratamiento->descripcion) }}</textarea>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">Actualizar tratamiento</button>
                <a href="{{ route('tratamientos.vista') }}" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>

    </div>
</div>

@endsection