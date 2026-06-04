@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Inventario Dental</h2>

    <a href="{{ route('inventario.crear') }}" class="btn btn-primary">
        Nuevo Producto
    </a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock general</th>
                    <th>C1</th>
                    <th>C2</th>
                    <th>C3</th>
                    <th>C4</th>
                    <th>Mínimo</th>
                    <th>Caducidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
            @foreach($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $producto->nombre }}</div>
                        @if($producto->proveedor)
                            <div class="small text-muted">{{ $producto->proveedor }}</div>
                        @endif
                    </td>
                    <td>{{ $producto->categoria }}</td>
                    <td class="fw-semibold">{{ $producto->cantidad }}</td>
                    <td class="text-muted">{{ $producto->stock_c1 }}</td>
                    <td class="text-muted">{{ $producto->stock_c2 }}</td>
                    <td class="text-muted">{{ $producto->stock_c3 }}</td>
                    <td class="text-muted">{{ $producto->stock_c4 }}</td>
                    <td>{{ $producto->stock_minimo }}</td>
                    <td>{{ $producto->fecha_caducidad ?? '—' }}</td>

                    <td>
                        @if($producto->cantidad <= $producto->stock_minimo)
                            <span class="badge bg-danger">Stock bajo</span>
                        @else
                            <span class="badge bg-success">Disponible</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('inventario.editar', $producto) }}" class="btn btn-secondary btn-sm">
                            Editar
                        </a>

                        <form action="{{ route('inventario.eliminar', $producto) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este producto?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>

        <div class="mt-3">
            {{ $productos->links() }}
        </div>

    </div>
</div>

@endsection
