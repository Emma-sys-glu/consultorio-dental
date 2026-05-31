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
                    <th>Cantidad</th>
                    <th>Stock mínimo</th>
                    <th>Caducidad</th>
                    <th>Proveedor</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
            @foreach($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->categoria }}</td>
                    <td>{{ $producto->cantidad }}</td>
                    <td>{{ $producto->stock_minimo }}</td>
                    <td>{{ $producto->fecha_caducidad }}</td>
                    <td>{{ $producto->proveedor }}</td>

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
