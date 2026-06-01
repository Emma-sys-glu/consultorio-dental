@extends('layouts.app')

@section('title', 'Citas - DentalTec')

@section('content')

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Citas Dentales</h2>
        <p class="text-muted mb-0">Calendario y listado de citas.</p>
    </div>

    <a href="{{ route('citas.crear') }}" class="btn btn-primary">
        Nueva Cita
    </a>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <strong>Calendario de citas</strong>
                <span class="text-muted text-capitalize ms-md-2 d-block d-md-inline">{{ $mesActual }}</span>
            </div>

            <div class="btn-group btn-group-sm" role="group" aria-label="Navegar calendario">
                <a href="{{ route('citas.vista', ['mes' => $mesAnterior]) }}" class="btn btn-outline-secondary">Anterior</a>
                <a href="{{ route('citas.vista') }}" class="btn btn-outline-primary">Hoy</a>
                <a href="{{ route('citas.vista', ['mes' => $mesSiguiente]) }}" class="btn btn-outline-secondary">Siguiente</a>
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

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Dentista</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($citas as $cita)
                    <tr>
                        <td>{{ $cita->id }}</td>

                        <td>
                            {{ $cita->paciente->nombre ?? 'Sin paciente' }}
                            {{ $cita->paciente->apellido_paterno ?? '' }}
                        </td>

                        <td>
                            {{ $cita->dentista->nombre ?? 'Sin dentista' }}
                            {{ $cita->dentista->apellido_paterno ?? '' }}
                        </td>

                        <td>{{ $cita->fecha }}</td>

                        <td>
                            {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}
                        </td>

                        <td>{{ $cita->motivo }}</td>

                        <td>
                            @if($cita->estado == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @elseif($cita->estado == 'confirmada')
                                <span class="badge bg-success">Confirmada</span>
                            @elseif($cita->estado == 'cancelada')
                                <span class="badge bg-danger">Cancelada</span>
                            @else
                                <span class="badge bg-secondary">Finalizada</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('citas.editar', $cita) }}" class="btn btn-secondary btn-sm">
                                    Editar
                                </a>

                                <form action="{{ route('citas.cancelar', $cita) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <button class="btn btn-secondary btn-sm" onclick="return confirm('¿Seguro que deseas cancelar esta cita?')">
                                        Cancelar
                                    </button>
                                </form>

                                <form action="{{ route('citas.eliminar', $cita) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta cita?')">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $citas->links() }}
        </div>
    </div>
</div>

@endsection
