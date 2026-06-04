@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Agendar Cita Dental</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('citas.guardar') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Paciente</label>
                    @if(auth()->user()->rol === 'paciente')
                        <input type="hidden" name="paciente_id" value="{{ auth()->user()->paciente_id }}">
                        <input type="text" class="form-control"
                               value="{{ auth()->user()->paciente->nombre ?? '' }} {{ auth()->user()->paciente->apellido_paterno ?? '' }}"
                               disabled>
                    @else
                        {{-- Buscador en tiempo real --}}
                        <input type="text"
                               id="buscar-paciente"
                               class="form-control mb-1"
                               placeholder="Escribe nombre o apellido para buscar..."
                               autocomplete="off">

                        <select id="listbox-paciente"
                                class="form-select mb-1"
                                size="5"
                                style="height:auto;">
                            <option value="">— Seleccionar paciente —</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}"
                                        data-nombre="{{ $p->nombre }} {{ $p->apellido_paterno }} {{ $p->apellido_materno }}"
                                        data-texto="{{ strtolower($p->nombre . ' ' . $p->apellido_paterno . ' ' . ($p->apellido_materno ?? '')) }}"
                                        @selected(old('paciente_id') == $p->id)>
                                    {{ $p->nombre }} {{ $p->apellido_paterno }} {{ $p->apellido_materno }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Campo real enviado al servidor --}}
                        <input type="hidden" name="paciente_id" id="hidden-paciente-id" value="{{ old('paciente_id') }}" required>

                        <div id="paciente-seleccionado" class="small text-success mt-1 {{ old('paciente_id') ? '' : 'd-none' }}">
                            ✓ Paciente seleccionado: <span id="nombre-paciente-sel">{{ old('paciente_id') ? '' : '' }}</span>
                        </div>
                        <div id="paciente-requerido" class="small text-danger mt-1 d-none">
                            Selecciona un paciente del listado.
                        </div>

                        <small class="text-muted">Escribe para filtrar · haz clic en el nombre para seleccionar</small>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dentista</label>
                    @if(auth()->user()->rol === 'dentista')
                        @php $dentistaActual = auth()->user()->dentista; @endphp
                        <input type="hidden" name="dentista_id" value="{{ auth()->user()->dentista_id }}">
                        <input type="text" class="form-control"
                               value="{{ $dentistaActual->nombre ?? '' }} {{ $dentistaActual->apellido_paterno ?? '' }} — {{ $dentistaActual->especialidad ?? '' }}"
                               disabled>
                    @else
                        <select name="dentista_id" class="form-select" required>
                            <option value="">Seleccionar dentista</option>
                            @foreach($dentistas as $dentista)
                                <option value="{{ $dentista->id }}" @selected(old('dentista_id') == $dentista->id)>
                                    {{ $dentista->nombre }}
                                    {{ $dentista->apellido_paterno }}
                                    - {{ $dentista->especialidad }}
                                    ({{ $dentista->horario_inicio }} a {{ $dentista->horario_fin }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ old('fecha') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Hora inicio</label>
                    <input type="time" name="hora_inicio" class="form-control" value="{{ old('hora_inicio') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Duración</label>
                    <select name="duracion_minutos" class="form-select" required>
                        <option value="">Seleccionar duración</option>
                        <option value="30"  @selected(old('duracion_minutos') == 30)>30 minutos</option>
                        <option value="45"  @selected(old('duracion_minutos') == 45)>45 minutos</option>
                        <option value="60"  @selected(old('duracion_minutos') == 60)>1 hora</option>
                        <option value="90"  @selected(old('duracion_minutos') == 90)>1 hora 30 minutos</option>
                        <option value="120" @selected(old('duracion_minutos') == 120)>2 horas</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Motivo de consulta</label>
                    <input type="text" name="motivo" class="form-control" value="{{ old('motivo') }}" required>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success" id="btn-guardar-cita">
                    Guardar cita
                </button>

                <a href="{{ route('citas.vista') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@push('scripts')
<script>
(function () {
    var buscar   = document.getElementById('buscar-paciente');
    var listbox  = document.getElementById('listbox-paciente');
    var hiddenId = document.getElementById('hidden-paciente-id');
    var divSel   = document.getElementById('paciente-seleccionado');
    var spanNom  = document.getElementById('nombre-paciente-sel');
    var divReq   = document.getElementById('paciente-requerido');

    if (!buscar || !listbox) return;

    var opciones = Array.from(listbox.options);

    // ── Filtrar mientras se escribe ───────────────────────────
    buscar.addEventListener('input', function () {
        var q = this.value.toLowerCase().trim();

        opciones.forEach(function (opt) {
            if (!opt.value) return; // mantiene el placeholder
            opt.hidden = q && !(opt.dataset.texto || '').includes(q);
        });

        // Si queda exactamente una coincidencia, la preselecciona
        var visibles = opciones.filter(function (o) { return o.value && !o.hidden; });
        if (visibles.length === 1) {
            visibles[0].selected = true;
            aplicarSeleccion(visibles[0]);
        }
    });

    // ── Al hacer clic / cambiar en el listbox ─────────────────
    listbox.addEventListener('change', function () {
        aplicarSeleccion(listbox.options[listbox.selectedIndex]);
    });

    function aplicarSeleccion(opt) {
        if (opt && opt.value) {
            hiddenId.value   = opt.value;
            spanNom.textContent = opt.dataset.nombre || opt.text;
            divSel.classList.remove('d-none');
            divReq.classList.add('d-none');
        } else {
            hiddenId.value = '';
            divSel.classList.add('d-none');
        }
    }

    // ── Validación antes de submit ────────────────────────────
    var form = listbox.closest('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!hiddenId.value) {
                e.preventDefault();
                divReq.classList.remove('d-none');
                listbox.focus();
            }
        });
    }
}());
</script>
@endpush

@endsection
