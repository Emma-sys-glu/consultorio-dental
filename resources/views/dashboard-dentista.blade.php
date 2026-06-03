@extends('layouts.app')

@section('title', 'Mi Panel - DentalTec')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Bienvenido, Dr. {{ $dentista->nombre }} {{ $dentista->apellido_paterno }}</h2>
    <p class="text-muted">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
</div>

{{-- Tarjetas resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Citas hoy</div>
                <div class="fs-3 fw-semibold">{{ $citasHoy->count() }}</div>
                <span class="badge bg-primary">{{ now()->translatedFormat('d M') }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Próximas citas</div>
                <div class="fs-3 fw-semibold">{{ $citasProximas->count() }}</div>
                <span class="badge bg-secondary">Pendientes</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Tratamientos activos</div>
                <div class="fs-3 fw-semibold">{{ $tratamientosActivos->count() }}</div>
                <span class="badge bg-warning text-dark">En proceso</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Citas este mes</div>
                <div class="fs-3 fw-semibold">{{ $totalCitasMes }}</div>
                <span class="badge bg-info text-dark">{{ $mesActual }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Citas de hoy --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <strong>Citas de hoy</strong>
            </div>
            <div class="card-body p-0">
                @forelse($citasHoy as $cita)
                    <div class="d-flex align-items-center px-3 py-2 border-bottom">
                        <div class="me-3 text-center" style="min-width:50px">
                            <div class="fw-semibold text-primary">{{ substr($cita->hora_inicio, 0, 5) }}</div>
                            <div class="small text-muted">{{ substr($cita->hora_fin, 0, 5) }}</div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $cita->paciente->nombre ?? '' }} {{ $cita->paciente->apellido_paterno ?? '' }}</div>
                            <div class="small text-muted">{{ $cita->motivo ?? 'Sin motivo' }}</div>
                        </div>
                        <span class="badge
                            @if($cita->estado === 'confirmada') bg-success
                            @elseif($cita->estado === 'pendiente') bg-warning text-dark
                            @elseif($cita->estado === 'cancelada') bg-danger
                            @else bg-secondary @endif">
                            {{ ucfirst($cita->estado) }}
                        </span>
                    </div>
                @empty
                    <div class="px-3 py-4 text-center text-muted">Sin citas para hoy</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Próximas citas --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <strong>Próximas citas</strong>
            </div>
            <div class="card-body p-0">
                @forelse($citasProximas as $cita)
                    <div class="d-flex align-items-center px-3 py-2 border-bottom">
                        <div class="me-3 text-center" style="min-width:60px">
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($cita->fecha)->translatedFormat('d M') }}</div>
                            <div class="small text-muted">{{ substr($cita->hora_inicio, 0, 5) }}</div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $cita->paciente->nombre ?? '' }} {{ $cita->paciente->apellido_paterno ?? '' }}</div>
                            <div class="small text-muted">{{ $cita->motivo ?? 'Sin motivo' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="px-3 py-4 text-center text-muted">Sin próximas citas</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Tratamientos activos --}}
@if($tratamientosActivos->isNotEmpty())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <strong>Tratamientos en proceso</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Paciente</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Última actualización</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tratamientosActivos as $t)
                <tr>
                    <td>{{ $t->paciente->nombre ?? '' }} {{ $t->paciente->apellido_paterno ?? '' }}</td>
                    <td>{{ Str::limit($t->descripcion ?? 'Sin descripción', 50) }}</td>
                    <td><span class="badge bg-warning text-dark">En proceso</span></td>
                    <td class="text-muted small">{{ $t->updated_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Calendario del mes --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <strong>Mis citas de {{ $mesActual }}</strong>
            </div>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('dashboard.dentista', ['mes' => $mesAnterior]) }}" class="btn btn-outline-secondary">Anterior</a>
                <a href="{{ route('dashboard.dentista') }}" class="btn btn-outline-primary">Hoy</a>
                <a href="{{ route('dashboard.dentista', ['mes' => $mesSiguiente]) }}" class="btn btn-outline-secondary">Siguiente</a>
            </div>
        </div>
    </div>
    <div class="table-responsive d-none d-md-block">
        <table class="table table-bordered align-top mb-0">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Lun</th><th>Mar</th><th>Mie</th><th>Jue</th><th>Vie</th><th>Sab</th><th>Dom</th>
                </tr>
            </thead>
            <tbody>
                @php $cursor = $inicioCalendario->copy(); @endphp
                @while($cursor->lte($finCalendario))
                <tr>
                    @for($d = 1; $d <= 7; $d++)
                        @php
                            $fuera   = !$cursor->isSameMonth($fechaCalendario);
                            $esHoy   = $cursor->isSameDay($hoy);
                            $cDia    = $fuera ? collect() : ($citasMes->get($cursor->day) ?? collect());
                        @endphp
                        <td class="{{ $fuera ? 'bg-light text-muted' : '' }}" style="width:14.28%;min-width:110px;height:100px">
                            <div class="mb-1">
                                <span class="{{ $esHoy ? 'badge bg-primary' : 'fw-semibold' }}">{{ $cursor->day }}</span>
                            </div>
                            @foreach($cDia->take(3) as $cita)
                                <div class="small border rounded px-1 mb-1 text-truncate bg-white">
                                    <span class="text-primary fw-semibold">{{ substr($cita->hora_inicio, 0, 5) }}</span>
                                    {{ $cita->paciente->nombre ?? '' }}
                                </div>
                            @endforeach
                            @if($cDia->count() > 3)
                                <div class="small text-muted">+{{ $cDia->count() - 3 }} más</div>
                            @endif
                        </td>
                        @php $cursor->addDay(); @endphp
                    @endfor
                </tr>
                @endwhile
            </tbody>
        </table>
    </div>
</div>

@endsection
