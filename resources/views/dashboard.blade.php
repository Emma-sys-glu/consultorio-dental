@extends('layouts.app')

@section('title', 'Home - DentalTec')

@section('content')

@php
    $cursor = $inicioCalendario->copy();
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="mb-1">Bienvenido</h2>
        <p class="text-muted mb-0">Agenda del consultorio y resumen del dia.</p>
    </div>

    <a href="{{ route('citas.crear') }}" class="btn btn-primary mt-3 mt-md-0">Nueva cita</a>
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

    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Stock bajo</div>
                <div class="fs-3 fw-semibold">{{ number_format($stockBajo) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <strong>Calendario de citas</strong>
                        <span class="text-muted text-capitalize ms-2">{{ $mesActual }}</span>
                    </div>

                    <div class="btn-group btn-group-sm" role="group" aria-label="Navegar calendario">
                        <a href="{{ route('dashboard', ['mes' => $mesAnterior]) }}" class="btn btn-outline-secondary">Anterior</a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">Hoy</a>
                        <a href="{{ route('dashboard', ['mes' => $mesSiguiente]) }}" class="btn btn-outline-secondary">Siguiente</a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
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
                        @while($cursor->lte($finCalendario))
                            <tr>
                                @for($dia = 1; $dia <= 7; $dia++)
                                    @php
                                        $fueraDeMes = !$cursor->isSameMonth($fechaCalendario);
                                        $esHoy = $cursor->isSameDay($hoy);
                                        $citasDia = $fueraDeMes ? collect() : ($citasMes->get($cursor->day) ?? collect());
                                    @endphp

                                    <td class="{{ $fueraDeMes ? 'bg-light text-muted' : '' }}" style="width: 14.28%; min-width: 120px; height: 120px;">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="{{ $esHoy ? 'badge bg-primary' : 'fw-semibold' }}">
                                                {{ $cursor->day }}
                                            </span>
                                        </div>

                                        @foreach($citasDia->take(3) as $cita)
                                            <button
                                                type="button"
                                                class="btn btn-light border text-start w-100 p-1 mb-1 small"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalCita"
                                                data-cita-fecha="{{ e($cita->fecha) }}"
                                                data-cita-hora="{{ e(substr($cita->hora_inicio, 0, 5) . ' - ' . substr($cita->hora_fin, 0, 5)) }}"
                                                data-cita-paciente="{{ e(($cita->paciente->nombre ?? 'Sin paciente') . ' ' . ($cita->paciente->apellido_paterno ?? '')) }}"
                                                data-cita-dentista="{{ e(($cita->dentista->nombre ?? 'Sin dentista') . ' ' . ($cita->dentista->apellido_paterno ?? '')) }}"
                                                data-cita-motivo="{{ e($cita->motivo) }}"
                                                data-cita-estado="{{ e(ucfirst($cita->estado)) }}"
                                                data-cita-url="{{ route('citas.editar', $cita) }}"
                                            >
                                                <div class="fw-semibold">{{ substr($cita->hora_inicio, 0, 5) }}</div>
                                                <div class="text-truncate">{{ $cita->paciente->nombre ?? 'Paciente' }}</div>
                                            </button>
                                        @endforeach

                                        @if($citasDia->count() > 3)
                                            <div class="small text-muted">+{{ $citasDia->count() - 3 }} mas</div>
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
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <strong>Proximas citas</strong>
            </div>
            <div class="list-group list-group-flush">
                @forelse($proximasCitas as $cita)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $cita->fecha }}</strong>
                            <span>{{ substr($cita->hora_inicio, 0, 5) }}</span>
                        </div>
                        <div>{{ $cita->paciente->nombre ?? 'Sin paciente' }} {{ $cita->paciente->apellido_paterno ?? '' }}</div>
                        <div class="small text-muted">{{ $cita->dentista->nombre ?? 'Sin dentista' }} {{ $cita->dentista->apellido_paterno ?? '' }}</div>
                    </div>
                @empty
                    <div class="list-group-item text-muted">No hay citas próximas.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <strong>Inventario por revisar</strong>
            </div>
            <div class="list-group list-group-flush">
                @forelse($productosBajos as $producto)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span>{{ $producto->nombre }}</span>
                            <span class="badge bg-danger">{{ $producto->cantidad }}</span>
                        </div>
                        <div class="small text-muted">Minimo: {{ $producto->stock_minimo }}</div>
                    </div>
                @empty
                    <div class="list-group-item text-muted">No hay productos con stock bajo.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCita" tabindex="-1" aria-labelledby="modalCitaTitulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCitaTitulo">Detalle de cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Fecha</dt>
                    <dd class="col-sm-8" id="modalCitaFecha"></dd>

                    <dt class="col-sm-4">Horario</dt>
                    <dd class="col-sm-8" id="modalCitaHora"></dd>

                    <dt class="col-sm-4">Paciente</dt>
                    <dd class="col-sm-8" id="modalCitaPaciente"></dd>

                    <dt class="col-sm-4">Dentista</dt>
                    <dd class="col-sm-8" id="modalCitaDentista"></dd>

                    <dt class="col-sm-4">Motivo</dt>
                    <dd class="col-sm-8" id="modalCitaMotivo"></dd>

                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8" id="modalCitaEstado"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" class="btn btn-primary" id="modalCitaEditar">Editar cita</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('modalCita');

        modal.addEventListener('show.bs.modal', function (event) {
            var boton = event.relatedTarget;

            document.getElementById('modalCitaFecha').textContent = boton.dataset.citaFecha;
            document.getElementById('modalCitaHora').textContent = boton.dataset.citaHora;
            document.getElementById('modalCitaPaciente').textContent = boton.dataset.citaPaciente;
            document.getElementById('modalCitaDentista').textContent = boton.dataset.citaDentista;
            document.getElementById('modalCitaMotivo').textContent = boton.dataset.citaMotivo;
            document.getElementById('modalCitaEstado').textContent = boton.dataset.citaEstado;
            document.getElementById('modalCitaEditar').href = boton.dataset.citaUrl;
        });
    });
</script>

@endsection
