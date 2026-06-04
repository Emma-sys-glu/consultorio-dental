@extends('layouts.app')

@section('title', 'Alertas de Inventario')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Alertas de Inventario</h2>
    <p class="text-muted">Productos con stock bajo y próximos a caducar.</p>
</div>

{{-- ── Stock general bajo ──────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong class="text-danger">⚠ Stock general bajo</strong>
        <span class="badge bg-danger">{{ $stockBajoGeneral->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-center">Stock general</th>
                    <th class="text-center">Stock mínimo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockBajoGeneral as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->nombre }}</td>
                        <td><span class="badge text-bg-secondary">{{ $p->categoria }}</span></td>
                        <td class="text-center fw-bold text-danger">{{ $p->cantidad }}</td>
                        <td class="text-center text-muted">{{ $p->stock_minimo }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Sin productos con stock general bajo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Stock bajo por consultorio (solo los que tienen alertas) ── --}}
@php
    $consultoiosConAlertas = array_filter([1,2,3,4], fn($n) => $stockBajoConsultorios[$n]->count() > 0);
@endphp

@if(!empty($consultoiosConAlertas))
<div class="row g-4 mb-4">
    @foreach($consultoiosConAlertas as $n)
    @php $col = 'stock_c' . $n; @endphp
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong class="text-warning">⚠ Consultorio {{ $n }} — stock bajo</strong>
                <span class="badge bg-warning text-dark">{{ $stockBajoConsultorios[$n]->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Stock C{{ $n }}</th>
                            <th class="text-center">Stock mínimo</th>
                            <th class="text-center">Stock general</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockBajoConsultorios[$n] as $p)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $p->nombre }}</div>
                                    <div class="small text-muted">{{ $p->categoria }}</div>
                                </td>
                                <td class="text-center fw-bold text-warning">{{ $p->$col }}</td>
                                <td class="text-center text-muted">{{ $p->stock_minimo }}</td>
                                <td class="text-center">{{ $p->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ── Próximos a caducar ──────────────────────────────────────── --}}
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong class="text-warning">⏰ Próximos a caducar (30 días)</strong>
        <span class="badge bg-warning text-dark">{{ $proximosCaducar->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Fecha caducidad</th>
                    <th class="text-center">Días restantes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proximosCaducar as $p)
                    @php
                        $dias     = (int) abs(now()->diffInDays(\Carbon\Carbon::parse($p->fecha_caducidad), false));
                        $vencido  = \Carbon\Carbon::parse($p->fecha_caducidad)->isPast();
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $p->nombre }}</td>
                        <td><span class="badge text-bg-secondary">{{ $p->categoria }}</span></td>
                        <td>{{ $p->fecha_caducidad }}</td>
                        <td class="text-center">
                            @if($vencido)
                                <span class="badge bg-danger">Vencido hace {{ $dias }} días</span>
                            @elseif($dias <= 7)
                                <span class="badge bg-danger">{{ $dias }} días</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ $dias }} días</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Sin productos próximos a caducar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
