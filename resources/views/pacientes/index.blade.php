@extends('layouts.app')

@section('title', 'Pacientes - DentalTec')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="mb-1">Pacientes</h2>
        <p class="text-muted mb-0">Registro y expediente clinico de pacientes.</p>
    </div>

    <a href="{{ route('pacientes.crear') }}" class="btn btn-info mt-3 mt-md-0">
        Nuevo paciente
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('pacientes.vista') }}" class="row g-2 align-items-end" id="formBuscarPacientes">
            <div class="col-md-8 col-lg-9">
                <label for="buscar" class="form-label">Buscar paciente</label>
                <input
                    type="search"
                    id="buscar"
                    name="buscar"
                    class="form-control"
                    value="{{ $buscar }}"
                    placeholder="Nombre o apellidos"
                    autocomplete="off"
                >
            </div>

            <div class="col-md-4 col-lg-3 d-flex gap-2">
                <button class="btn btn-primary flex-fill">Buscar</button>
                @if($buscar)
                    <a href="{{ route('pacientes.vista') }}" class="btn btn-outline-secondary">Limpiar</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card" id="tablaPacientes">
    @include('pacientes.partials.tabla', ['pacientes' => $pacientes])
</div>

@if($pacientes->count())
    @php
        $pacienteResumen = $pacientes->first();
    @endphp

    <div class="card mt-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: #dff7fb; color: #067a7e;">
                    <strong>
                        {{ strtoupper(substr($pacienteResumen->nombre, 0, 1) . substr($pacienteResumen->apellido_paterno, 0, 1)) }}
                    </strong>
                </div>

                <div class="flex-grow-1">
                    <h3 class="h5 mb-1">
                        {{ $pacienteResumen->nombre }}
                        {{ $pacienteResumen->apellido_paterno }}
                        {{ $pacienteResumen->apellido_materno }}
                    </h3>
                    <p class="text-muted small mb-2">
                        Paciente desde {{ $pacienteResumen->created_at?->format('d/m/Y') ?? 'sin fecha' }}
                        · Tel. {{ $pacienteResumen->telefono }}
                    </p>
                    <p class="mb-0">
                        Ultimo registro:
                        {{ $pacienteResumen->antecedentes_medicos ?: 'Sin antecedentes medicos registrados.' }}
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('expedientes.vista') }}" class="btn btn-info btn-sm">Ver expediente</a>
                    <a href="{{ route('citas.crear') }}" class="btn btn-primary btn-sm">Nueva cita</a>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('buscar');
        var form = document.getElementById('formBuscarPacientes');
        var contenedor = document.getElementById('tablaPacientes');
        var timer = null;

        function cargarPacientes(url) {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (respuesta) {
                    return respuesta.text();
                })
                .then(function (html) {
                    contenedor.innerHTML = html;
                    window.history.replaceState({}, '', url);
                });
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);

            timer = setTimeout(function () {
                var params = new URLSearchParams(new FormData(form));
                var url = form.action + '?' + params.toString();

                cargarPacientes(url);
            }, 300);
        });

        contenedor.addEventListener('click', function (event) {
            var link = event.target.closest('.pagination a');

            if (!link) {
                return;
            }

            event.preventDefault();
            cargarPacientes(link.href);
        });
    });
</script>

@endsection
