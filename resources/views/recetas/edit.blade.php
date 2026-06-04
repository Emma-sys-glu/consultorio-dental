@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Editar Receta</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('recetas.actualizar', $receta) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Paciente</label>
                    <select name="paciente_id" class="form-select" required>
                        @foreach($pacientes as $paciente)
                            <option value="{{ $paciente->id }}" @selected(old('paciente_id', $receta->paciente_id) == $paciente->id)>
                                {{ $paciente->nombre }} {{ $paciente->apellido_paterno }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dentista</label>
                    <select name="dentista_id" class="form-select" required>
                        @foreach($dentistas as $dentista)
                            <option value="{{ $dentista->id }}" @selected(old('dentista_id', $receta->dentista_id) == $dentista->id)>
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
                            <option value="{{ $tratamiento->id }}" @selected(old('tratamiento_id', $receta->tratamiento_id) == $tratamiento->id)>
                                Tratamiento #{{ $tratamiento->id }} - {{ $tratamiento->nombre }}
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

                    <input type="text"
                           id="buscar-med"
                           class="form-control mb-1"
                           placeholder="Escribe para buscar en inventario..."
                           autocomplete="off"
                           value="{{ $receta->inventario_id ? ($receta->inventario->nombre ?? '') : '' }}">

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
                                    @selected(old('inventario_id', $receta->inventario_id) == $med->id)>
                                {{ $med->nombre }}
                                · stock general: {{ $med->cantidad }}
                                (C1:{{ $med->stock_c1 }} C2:{{ $med->stock_c2 }} C3:{{ $med->stock_c3 }} C4:{{ $med->stock_c4 }})
                            </option>
                        @endforeach
                    </select>

                    <input type="hidden" name="inventario_id" id="hidden-inv-id"
                           value="{{ old('inventario_id', $receta->inventario_id) }}">

                    <input type="text"
                           name="medicamento"
                           id="input-medicamento"
                           class="form-control mt-1"
                           value="{{ old('medicamento', $receta->medicamento) }}"
                           placeholder="Nombre del medicamento"
                           required>

                    <small class="text-muted">
                        Selecciona del listado para vincular al inventario · o escribe directamente si no está en inventario
                    </small>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control"
                           value="{{ old('cantidad', $receta->cantidad) }}" min="1" placeholder="Unidades">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Dosis</label>
                    <input type="text" name="dosis" class="form-control"
                           value="{{ old('dosis', $receta->dosis) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Frecuencia</label>
                    <input type="text" name="frecuencia" class="form-control"
                           value="{{ old('frecuencia', $receta->frecuencia) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Duración</label>
                    <input type="text" name="duracion" class="form-control"
                           value="{{ old('duracion', $receta->duracion) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha de emisión</label>
                    <input type="date" name="fecha_emision" class="form-control"
                           value="{{ old('fecha_emision', $receta->fecha_emision) }}" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Indicaciones</label>
                    <textarea name="indicaciones" class="form-control" rows="3">{{ old('indicaciones', $receta->indicaciones) }}</textarea>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">Actualizar receta</button>
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

    if (!buscar || !listbox) return;

    var opciones = Array.from(listbox.options);

    buscar.addEventListener('input', function () {
        var q = this.value.toLowerCase().trim();
        opciones.forEach(function (opt) {
            if (!opt.value) return;
            opt.hidden = q && !(opt.dataset.texto || '').includes(q);
        });
        var visibles = opciones.filter(function (o) { return o.value && !o.hidden; });
        if (visibles.length === 1) {
            visibles[0].selected = true;
            aplicarSeleccion(visibles[0]);
        }
    });

    listbox.addEventListener('change', function () {
        aplicarSeleccion(listbox.options[listbox.selectedIndex]);
    });

    function aplicarSeleccion(opt) {
        if (opt && opt.value) {
            hiddenId.value = opt.value;
            inputMed.value = opt.dataset.nombre || '';
        } else {
            hiddenId.value = '';
        }
    }

    inputMed.addEventListener('input', function () {
        if (hiddenId.value) {
            hiddenId.value = '';
            listbox.selectedIndex = 0;
        }
    });
}());
</script>
@endpush

@endsection
