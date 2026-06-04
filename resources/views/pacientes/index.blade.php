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
