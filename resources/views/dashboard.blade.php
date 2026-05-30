@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Dashboard del Consultorio Dental</h2>
    <p class="text-muted">Resumen general del sistema.</p>
</div>

<div class="row g-4">

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Pacientes</h6>
                <h2 class="fw-bold">{{ $totalPacientes }}</h2>
                <span class="badge bg-primary">Registrados</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Dentistas</h6>
                <h2 class="fw-bold">{{ $totalDentistas }}</h2>
                <span class="badge bg-info">Activos</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Citas</h6>
                <h2 class="fw-bold">{{ $totalCitas }}</h2>
                <span class="badge bg-success">Agendadas</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Stock bajo</h6>
                <h2 class="fw-bold">{{ $stockBajo }}</h2>
                <span class="badge bg-danger">Alertas</span>
            </div>
        </div>
    </div>

</div>

<div class="card mt-4 shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h5 class="fw-bold">Estado del sistema</h5>
        <p class="mb-1">Laravel funcionando correctamente.</p>
        <p class="mb-1">PostgreSQL conectado mediante Docker.</p>
        <p class="mb-1">API REST protegida con token Sanctum.</p>
        <p class="mb-0">ORM Eloquent utilizado para relaciones y carga masiva de datos.</p>
    </div>
</div>

@endsection