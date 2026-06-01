@extends('layouts.app')

@section('title', 'Home - DentalTec')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Dashboard del Consultorio Dental</h2>
    <p class="text-muted">Resumen general del sistema.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Pacientes</div>
                <div class="fs-3 fw-semibold">{{ number_format($totalPacientes) }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Dentistas</div>
                <div class="fs-3 fw-semibold">{{ number_format($totalDentistas) }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Citas hoy</div>
                <div class="fs-3 fw-semibold">{{ number_format($citasHoy) }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Tratamientos</h6>
                <h2 class="fw-bold">{{ $totalTratamientos }}</h2>
                <span class="badge bg-secondary">Registrados</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Recetas</h6>
                <h2 class="fw-bold">{{ $totalRecetas }}</h2>
                <span class="badge bg-dark">Emitidas</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="text-muted">Productos</h6>
                <h2 class="fw-bold">{{ $totalInventario }}</h2>
                <span class="badge bg-primary">Inventario</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Stock bajo</div>
                <div class="fs-3 fw-semibold">{{ number_format($stockBajo) }}</div>
            </div>
        </div>
    </div>
</div>

@endsection
