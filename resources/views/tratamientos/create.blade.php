@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Registrar Tratamiento</h2>

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

        <form action="{{ route('tratamientos.guardar') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
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

                <div class="col-md-6">
                    <label class="form-label">Dentista</label>
                    <select name="dentista_id" class="form-select" required>
                        <option value="">Seleccionar dentista</option>
                        @foreach($dentistas as $dentista)
                            <option value="{{ $dentista->id }}">
                                {{ $dentista->nombre }} {{ $dentista->apellido_paterno }} - {{ $dentista->especialidad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Expediente</label>
                    <select name="expediente_id" class="form-select" required>
                        <option value="">Seleccionar expediente</option>
                        @foreach($expedientes as $expediente)
                            <option value="{{ $expediente->id }}">
                                Expediente #{{ $expediente->id }} - 
                                {{ $expediente->paciente->nombre ?? '' }}
                                {{ $expediente->paciente->apellido_paterno ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cita relacionada</label>
                    <select name="cita_id" class="form-select">
                        <option value="">Sin cita relacionada</option>
                        @foreach($citas as $cita)
                            <option value="{{ $cita->id }}">
                                Cita #{{ $cita->id }} - {{ $cita->fecha }} {{ $cita->hora_inicio }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tratamiento</label>
                    <select name="nombre" class="form-select" required>
                        <option value="">Seleccionar tratamiento</option>
                        <option value="Limpieza dental">Limpieza dental</option>
                        <option value="Extracción">Extracción</option>
                        <option value="Endodoncia">Endodoncia</option>
                        <option value="Ortodoncia">Ortodoncia</option>
                        <option value="Implante dental">Implante dental</option>
                        <option value="Blanqueamiento">Blanqueamiento</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Costo</label>
                    <input type="number" step="0.01" name="costo" class="form-control" value="{{ old('costo') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" required>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En proceso</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">Guardar tratamiento</button>
                <a href="{{ route('tratamientos.vista') }}" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>

    </div>
</div>

@endsection