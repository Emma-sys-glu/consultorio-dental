@extends('layouts.app')

@section('title', 'Alertas de Inventario')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold">Alertas de Inventario</h2>
    <p class="text-muted">Productos con stock bajo y próximos a caducar.</p>
</div>

<div class="row g-4">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <strong>Stock bajo</strong>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Stock mínimo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockBajo as $producto)
                            <tr>
                                <td>{{ $producto->nombre }}</td>
                                <td>{{ $producto->cantidad }}</td>
                                <td>{{ $producto->stock_minimo }}</td>
                                <td>
                                    <span class="badge bg-danger">
                                        Stock bajo
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No hay productos con stock bajo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <strong>Próximos a caducar</strong>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Fecha caducidad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proximosCaducar as $producto)
                            <tr>
                                <td>{{ $producto->nombre }}</td>
                                <td>{{ $producto->fecha_caducidad }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        Próximo a caducar
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No hay productos próximos a caducar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection