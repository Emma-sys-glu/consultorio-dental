@extends('layouts.app')

@section('content')

<h2 class="mb-4 fw-bold">Registrar Producto</h2>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Revisa los datos ingresados.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('inventario.guardar') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Nombre del producto</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select" required>
                        <option value="">Seleccionar</option>
                        <option value="Medicamentos">Medicamentos</option>
                        <option value="Material dental">Material dental</option>
                        <option value="Instrumental">Instrumental</option>
                        <option value="Protección">Protección</option>
                        <option value="Limpieza">Limpieza</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Proveedor</label>
                    <input type="text" name="proveedor" class="form-control" value="{{ old('proveedor') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" value="{{ old('cantidad') }}" min="0" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Stock mínimo</label>
                    <input type="number" name="stock_minimo" class="form-control" value="{{ old('stock_minimo') }}" min="0" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha caducidad</label>
                    <input type="date" name="fecha_caducidad" class="form-control" value="{{ old('fecha_caducidad') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Precio unitario</label>
                    <input type="number" step="0.01" name="precio_unitario" class="form-control" value="{{ old('precio_unitario') }}" min="0" required>
                </div>

            </div>

            <div class="mt-4">
                <button class="btn btn-success">
                    Guardar producto
                </button>

                <a href="{{ route('inventario.vista') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@endsection