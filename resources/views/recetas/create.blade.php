@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Registrar Receta</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('recetas.guardar') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Paciente</label>
                    <select name="paciente_id" class="form-select" required>
                        <option value="">Seleccionar paciente</option>
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}" @selected(old('paciente_id') == $paciente->id)>
                                {{ $paciente->nombre }} {{ $paciente->apellido_paterno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dentista</label>
                    <select name="dentista_id" class="form-select" required>
                        <option value="">Seleccionar dentista</option>
                        @foreach($dentistas as $dentista)
                            <option value="{{ $dentista->id }}" @selected(old('dentista_id') == $dentista->id)>
                                {{ $dentista->nombre }} {{ $dentista->apellido_paterno }} - {{ $dentista->especialidad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Tratamiento relacionado</label>
                    <select name="tratamiento_id" class="form-select">
                        <option value="">Sin tratamiento relacionado</option>
                        @foreach($tratamientos as $tratamiento)
                            <option value="{{ $tratamiento->id }}" @selected(old('tratamiento_id') == $tratamiento->id)>
                                Tratamiento #{{ $tratamiento->id }} -
                                {{ $tratamiento->nombre }}
                                ({{ $tratamiento->paciente->nombre ?? '' }} {{ $tratamiento->paciente->apellido_paterno ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ── Buscador de medicamento del inventario ─────────── --}}
                <div class="col-md-12">
                    <label class="form-label fw-semibold">
                        Medicamento
                        <span class="text-muted fw-normal small">— busca y selecciona del inventario, o escribe uno libre</span>
                    </label>

                    {{-- Campo de búsqueda --}}
                    <input type="text"
                           id="buscar-med"
                           class="form-control mb-1"
                           placeholder="Escribe para buscar en inventario..."
                           autocomplete="off">

                    {{-- Listbox con medicamentos del inventario --}}
                    <select id="listbox-med"
                            class="form-select mb-1"
                            size="5"
                            style="height:auto;">
                        <option value="">— Sin vincular al inventario —</option>
                        @foreach($medicamentos as $med)
                            <option value="{{ $med->id }}"
                                    data-nombre="{{ $med->nombre }}"
                                    data-stock="{{ $med->cantidad }}"
                                    data-texto="{{ strtolower($med->nombre) }}"
                                    @selected(old('inventario_id') == $med->id)>
                                {{ $med->nombre }}
                                · stock general: {{ $med->cantidad }}
                                (C1:{{ $med->stock_c1 }} C2:{{ $med->stock_c2 }} C3:{{ $med->stock_c3 }} C4:{{ $med->stock_c4 }})
                            </option>
                        @endforeach
                    </select>

                    {{-- Campo real enviado al servidor --}}
                    <input type="hidden" name="inventario_id" id="hidden-inv-id" value="{{ old('inventario_id') }}">

                    {{-- Nombre del medicamento (requerido) --}}
                    <input type="text"
                           name="medicamento"
                           id="input-medicamento"
                           class="form-control mt-1"
                           value="{{ old('medicamento') }}"
                           placeholder="Nombre del medicamento (se llena al seleccionar, o escribe uno libre)"
                           required>

                    <small class="text-muted">
                        Selecciona del listado para vincular al inventario · o escribe directamente el nombre si no está en inventario
                    </small>

                    <div id="alerta-stock" class="text-danger small mt-1 d-none">
                        ⚠ La cantidad indicada supera el stock general disponible.
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Cantidad a dispensar</label>
                    <input type="number" name="cantidad" id="input-cantidad" class="form-control"
                           value="{{ old('cantidad') }}" min="1" placeholder="Unidades">
                    <small class="text-muted">Solo aplica si se vinculó al inventario</small>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Dosis</label>
                    <input type="text" name="dosis" class="form-control"
                           value="{{ old('dosis') }}" placeholder="400 mg" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Frecuencia</label>
                    <input type="text" name="frecuencia" class="form-control"
                           value="{{ old('frecuencia') }}" placeholder="Cada 8 horas" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Duración</label>
                    <input type="text" name="duracion" class="form-control"
                           value="{{ old('duracion') }}" placeholder="3 días" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha de emisión</label>
                    <input type="date" name="fecha_emision" class="form-control"
                           value="{{ old('fecha_emision', date('Y-m-d')) }}" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Indicaciones</label>
                    <textarea name="indicaciones" class="form-control" rows="3">{{ old('indicaciones') }}</textarea>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">Guardar receta</button>
                <a href="{{ route('recetas.vista') }}" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var buscar    = document.getElementById('buscar-med');
    var listbox   = document.getElementById('listbox-med');
    var hiddenId  = document.getElementById('hidden-inv-id');
    var inputMed  = document.getElementById('input-medicamento');
    var inputCant = document.getElementById('input-cantidad');
    var alerta    = document.getElementById('alerta-stock');

    if (!buscar || !listbox) return;

    var opciones = Array.from(listbox.options);

    // ── Filtrar opciones mientras se escribe ──────────────────
    buscar.addEventListener('input', function () {
        var q = this.value.toLowerCase().trim();
        opciones.forEach(function (opt) {
            if (!opt.value) return; // mantiene el placeholder
            opt.hidden = q && !(opt.dataset.texto || '').includes(q);
        });
        // Si queda una sola coincidencia visible, la preselecciona
        var visibles = opciones.filter(function (o) { return o.value && !o.hidden; });
        if (visibles.length === 1) {
            visibles[0].selected = true;
            aplicarSeleccion(visibles[0]);
        }
    });

    // ── Al hacer clic / cambiar en el listbox ─────────────────
    listbox.addEventListener('change', function () {
        var opt = listbox.options[listbox.selectedIndex];
        aplicarSeleccion(opt);
    });

    function aplicarSeleccion(opt) {
        if (opt && opt.value) {
            hiddenId.value   = opt.value;
            inputMed.value   = opt.dataset.nombre || '';
            verificarStock();
        } else {
            hiddenId.value = '';
            alerta.classList.add('d-none');
        }
    }

    // ── Alerta de stock insuficiente ──────────────────────────
    function verificarStock() {
        var opt = listbox.options[listbox.selectedIndex];
        if (!opt || !opt.value) { alerta.classList.add('d-none'); return; }
        var stockGen = parseInt(opt.dataset.stock) || 0;
        var cant     = parseInt(inputCant.value)    || 0;
        alerta.classList.toggle('d-none', cant === 0 || cant <= stockGen);
    }

    if (inputCant) inputCant.addEventListener('input', verificarStock);

    // ── Limpiar selección de inventario si el usuario edita a mano ──
    inputMed.addEventListener('input', function () {
        if (hiddenId.value) {
            // El usuario editó el nombre manualmente; desvincular inventario
            hiddenId.value = '';
            listbox.selectedIndex = 0;
            alerta.classList.add('d-none');
        }
    });
}());
</script>
@endpush

@endsection
