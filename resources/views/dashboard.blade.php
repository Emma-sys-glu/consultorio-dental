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

<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <strong>Calendario de citas</strong>
                <span class="text-muted text-capitalize ms-md-2 d-block d-md-inline">{{ $mesActual }}</span>
            </div>

            <div class="btn-group btn-group-sm" role="group" aria-label="Navegar calendario">
                <a href="{{ route('dashboard', ['mes' => $mesAnterior]) }}" class="btn btn-outline-secondary">Anterior</a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">Hoy</a>
                <a href="{{ route('dashboard', ['mes' => $mesSiguiente]) }}" class="btn btn-outline-secondary">Siguiente</a>
            </div>
        </div>
    </div>

    <div class="table-responsive d-none d-md-block">
        <table class="table table-bordered align-top mb-0">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Lun</th>
                    <th>Mar</th>
                    <th>Mie</th>
                    <th>Jue</th>
                    <th>Vie</th>
                    <th>Sab</th>
                    <th>Dom</th>
                </tr>
            </thead>
            <tbody>
                @php $cursorCalendario = $inicioCalendario->copy(); @endphp

                @while($cursorCalendario->lte($finCalendario))
                    <tr>
                        @for($dia = 1; $dia <= 7; $dia++)
                            @php
                                $fueraDeMes = !$cursorCalendario->isSameMonth($fechaCalendario);
                                $esHoy = $cursorCalendario->isSameDay($hoy);
                                $citasDia = $fueraDeMes ? collect() : ($citasMes->get($cursorCalendario->day) ?? collect());
                            @endphp

                            <td class="{{ $fueraDeMes ? 'bg-light text-muted' : '' }}" style="width: 14.28%; min-width: 130px; height: 120px;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="{{ $esHoy ? 'badge bg-primary' : 'fw-semibold' }}">
                                        {{ $cursorCalendario->day }}
                                    </span>
                                </div>

                                @foreach($citasDia->take(3) as $cita)
                                    <a href="{{ route('citas.editar', $cita) }}" class="btn btn-light border text-start w-100 p-1 mb-1 small">
                                        <div class="fw-semibold">{{ substr($cita->hora_inicio, 0, 5) }}</div>
                                        <div class="text-truncate">{{ $cita->paciente->nombre ?? 'Paciente' }}</div>
                                    </a>
                                @endforeach

                                @if($citasDia->count() > 3)
                                    <div class="small text-muted">+{{ $citasDia->count() - 3 }} mas</div>
                                @endif
                            </td>

                            @php $cursorCalendario->addDay(); @endphp
                        @endfor
                    </tr>
                @endwhile
            </tbody>
        </table>
    </div>

    <div class="list-group list-group-flush d-md-none">
        @php $diaMovil = $fechaCalendario->copy()->startOfMonth(); @endphp

        @while($diaMovil->lte($fechaCalendario->copy()->endOfMonth()))
            @php $citasDia = $citasMes->get($diaMovil->day) ?? collect(); @endphp

            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong class="{{ $diaMovil->isSameDay($hoy) ? 'text-primary' : '' }}">
                        {{ ucfirst($diaMovil->translatedFormat('D d')) }}
                    </strong>
                    <span class="badge text-bg-light">{{ $citasDia->count() }}</span>
                </div>

                @forelse($citasDia->take(4) as $cita)
                    <a href="{{ route('citas.editar', $cita) }}" class="d-block text-decoration-none text-body border rounded p-2 mb-2">
                        <div class="fw-semibold">
                            {{ substr($cita->hora_inicio, 0, 5) }} - {{ substr($cita->hora_fin, 0, 5) }}
                        </div>
                        <div class="small text-muted">
                            {{ $cita->paciente->nombre ?? 'Paciente' }}
                            {{ $cita->paciente->apellido_paterno ?? '' }}
                        </div>
                    </a>
                @empty
                    <div class="small text-muted">Sin citas</div>
                @endforelse

                @if($citasDia->count() > 4)
                    <div class="small text-muted">+{{ $citasDia->count() - 4 }} citas mas</div>
                @endif
            </div>

            @php $diaMovil->addDay(); @endphp
        @endwhile
    </div>
</div>

@endsection
