@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Editar Producto</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('inventario.actualizar', $producto) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Nombre del producto</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $producto->nombre) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select" required>
                        @foreach(['Medicamentos', 'Material dental', 'Instrumental', 'Protección', 'Limpieza'] as $categoria)
                            <option value="{{ $categoria }}" @selected(old('categoria', $producto->categoria) == $categoria)>
                                {{ $categoria }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Proveedor</label>
                    <input type="text" name="proveedor" class="form-control" value="{{ old('proveedor', $producto->proveedor) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" value="{{ old('cantidad', $producto->cantidad) }}" min="0" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Stock mínimo</label>
                    <input type="number" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', $producto->stock_minimo) }}" min="0" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha caducidad</label>
                    <input type="date" name="fecha_caducidad" class="form-control" value="{{ old('fecha_caducidad', $producto->fecha_caducidad) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Precio unitario</label>
                    <input type="number" step="0.01" name="precio_unitario" class="form-control" value="{{ old('precio_unitario', $producto->precio_unitario) }}" min="0" required>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">
                    Actualizar producto
                </button>

                <a href="{{ route('inventario.vista') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@endsection