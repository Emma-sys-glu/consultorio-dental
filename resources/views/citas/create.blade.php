@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Agendar Cita Dental</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('citas.guardar') }}" method="POST">
            @csrf

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
                                <option value="">Seleccionar paciente</option>
                                @foreach($pacientes as $paciente)
                                    <option value="{{ $paciente->id }}">
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
                        <option value="">Seleccionar dentista</option>
                        @foreach($dentistas as $dentista)
                            <option value="{{ $dentista->id }}">
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
                    <input type="date" name="fecha" class="form-control" value="{{ old('fecha') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Hora inicio</label>
                    <input type="time" name="hora_inicio" class="form-control" value="{{ old('hora_inicio') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Duración</label>
                    <select name="duracion_minutos" class="form-select" required>
                        <option value="">Seleccionar duración</option>
                        <option value="30">30 minutos</option>
                        <option value="45">45 minutos</option>
                        <option value="60">1 hora</option>
                        <option value="90">1 hora 30 minutos</option>
                        <option value="120">2 horas</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Motivo de consulta</label>
                    <input type="text" name="motivo" class="form-control" value="{{ old('motivo') }}" required>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">
                    Guardar cita
                </button>

                <a href="{{ route('citas.vista') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@endsection