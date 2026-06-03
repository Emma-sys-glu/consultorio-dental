@extends('layouts.app')

@section('title', 'Mi Panel - DentalTec')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Bienvenido, {{ $paciente->nombre }} {{ $paciente->apellido_paterno }}</h2>
    <p class="text-muted">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
</div>

{{-- Próxima cita destacada --}}
@if($proximaCita)
<div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid #067a7e !important;">
    <div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-3">
        <div class="text-center px-3">
            <div class="fs-1 fw-bold text-primary">{{ \Carbon\Carbon::parse($proximaCita->fecha)->format('d') }}</div>
            <div class="text-uppercase text-muted small fw-semibold">{{ \Carbon\Carbon::parse($proximaCita->fecha)->translatedFormat('M Y') }}</div>
        </div>
        <div class="border-start ps-3 flex-grow-1">
            <div class="fw-bold fs-5">Próxima cita</div>
            <div class="text-muted">
                {{ substr($proximaCita->hora_inicio, 0, 5) }} – {{ substr($proximaCita->hora_fin, 0, 5) }}
                &nbsp;·&nbsp;
                Dr. {{ $proximaCita->dentista->nombre ?? '' }} {{ $proximaCita->dentista->apellido_paterno ?? '' }}
            </div>
            @if($proximaCita->motivo)
                <div class="small text-muted mt-1">{{ $proximaCita->motivo }}</div>
            @endif
        </div>
        <span class="badge fs-6
            @if($proximaCita->estado === 'confirmada') bg-success
            @elseif($proximaCita->estado === 'pendiente') bg-warning text-dark
            @else bg-secondary @endif">
            {{ ucfirst($proximaCita->estado) }}
        </span>
    </div>
</div>
@else
<div class="alert alert-light border mb-4">
    No tiene citas próximas agendadas.
</div>
@endif

{{-- Notificaciones sin leer --}}
@if($noLeidas > 0)
<div class="alert alert-primary d-flex align-items-center gap-2 mb-4">
    <span class="badge bg-primary rounded-pill">{{ $noLeidas }}</span>
    Tiene {{ $noLeidas }} notificación{{ $noLeidas > 1 ? 'es' : '' }} sin leer.
    <a href="{{ route('notificaciones.index') }}" class="ms-auto btn btn-sm btn-primary">Ver notificaciones</a>
</div>
@endif

<div class="row g-3">
    {{-- Historial de citas --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <strong>Mis citas recientes</strong>
            </div>
            <div class="card-body p-0">
                @forelse($citasRecientes as $cita)
                    <div class="d-flex align-items-center px-3 py-2 border-bottom">
                        <div class="me-3 text-center" style="min-width:55px">
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($cita->fecha)->translatedFormat('d M') }}</div>
                            <div class="small text-muted">{{ substr($cita->hora_inicio, 0, 5) }}</div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">Dr. {{ $cita->dentista->nombre ?? '' }} {{ $cita->dentista->apellido_paterno ?? '' }}</div>
                            <div class="small text-muted">{{ $cita->motivo ?? 'Sin motivo' }}</div>
                        </div>
                        <span class="badge
                            @if($cita->estado === 'confirmada') bg-success
                            @elseif($cita->estado === 'pendiente') bg-warning text-dark
                            @elseif($cita->estado === 'cancelada') bg-danger
                            @elseif($cita->estado === 'realizada') bg-secondary
                            @else bg-light text-dark @endif">
                            {{ ucfirst($cita->estado) }}
                        </span>
                    </div>
                @empty
                    <div class="px-3 py-4 text-center text-muted">Sin citas registradas</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tratamientos --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <strong>Mis tratamientos</strong>
            </div>
            <div class="card-body p-0">
                @forelse($tratamientos as $t)
                    <div class="px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ Str::limit($t->descripcion ?? 'Sin descripción', 60) }}</div>
                                <div class="small text-muted">Dr. {{ $t->dentista->nombre ?? '' }} {{ $t->dentista->apellido_paterno ?? '' }}</div>
                            </div>
                            <span class="badge ms-2
                                @if($t->estado === 'finalizado') bg-success
                                @elseif($t->estado === 'en_proceso') bg-warning text-dark
                                @elseif($t->estado === 'pendiente') bg-secondary
                                @else bg-danger @endif">
                                {{ ucfirst(str_replace('_', ' ', $t->estado)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-3 py-4 text-center text-muted">Sin tratamientos registrados</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Últimas notificaciones --}}
    @if($notificaciones->isNotEmpty())
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <strong>Últimas notificaciones</strong>
                <a href="{{ route('notificaciones.index') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-body p-0">
                @foreach($notificaciones as $n)
                    <div class="d-flex align-items-start px-3 py-2 border-bottom {{ $n->leida ? '' : 'bg-light' }}">
                        <div class="me-2 mt-1">
                            @if(!$n->leida)
                                <span class="badge bg-primary rounded-pill">&nbsp;</span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">{{ $n->titulo }}</div>
                            <div class="small text-muted">{{ $n->mensaje }}</div>
                            <div class="small text-muted mt-1">{{ $n->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
