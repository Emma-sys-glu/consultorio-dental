@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Editar Cita Dental</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('citas.actualizar', $cita) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Paciente</label>
                    @if(auth()->user()->rol === 'paciente')
    <input type="hidden" name="paciente_id" value="{{ auth()->user()->paciente_id }}">

    <div class="col-md-6">
        <label class="form-label">Paciente</label>
        <input type="text" class="form-control"
               value="{{ auth()->user()->paciente->nombre }} {{ auth()->user()->paciente->apellido_paterno }}"
               disabled>
    </div>
@else
    <div class="col-md-6">
        <label class="form-label">Paciente</label>
        <select name="paciente_id" class="form-select" required>
            @foreach($pacientes as $paciente)
                <option value="{{ $paciente->id }}" @selected(old('paciente_id', $cita->paciente_id) == $paciente->id)>
                    {{ $paciente->nombre }} {{ $paciente->apellido_paterno }} {{ $paciente->apellido_materno }}
                </option>
            @endforeach
        </select>
    </div>
@endif
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dentista</label>
                    <select name="dentista_id" class="form-select" required>
                        @foreach($dentistas as $dentista)
                            <option value="{{ $dentista->id }}" @selected(old('dentista_id', $cita->dentista_id) == $dentista->id)>
                                {{ $dentista->nombre }}
                                {{ $dentista->apellido_paterno }}
                                - {{ $dentista->especialidad }}
                                ({{ $dentista->horario_inicio }} a {{ $dentista->horario_fin }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $cita->fecha) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Hora inicio</label>
                    <input type="time" name="hora_inicio" class="form-control" value="{{ old('hora_inicio', $cita->hora_inicio) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Duración</label>
                    <select name="duracion_minutos" class="form-select" required>
                        @foreach([30, 45, 60, 90, 120] as $duracion)
                            <option value="{{ $duracion }}" @selected(old('duracion_minutos', $cita->duracion_minutos) == $duracion)>
                                {{ $duracion }} minutos
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Motivo</label>
                    <input type="text" name="motivo" class="form-control" value="{{ old('motivo', $cita->motivo) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" required>
                        @foreach(['pendiente', 'confirmada', 'cancelada', 'finalizada'] as $estado)
                            <option value="{{ $estado }}" @selected(old('estado', $cita->estado) == $estado)>
                                {{ ucfirst($estado) }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">
                    Actualizar cita
                </button>

                <a href="{{ route('citas.vista') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@endsection