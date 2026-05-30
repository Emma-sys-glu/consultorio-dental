<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultorio Dental</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#007C89;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            Consultorio Dental
        </a>

        <div class="d-flex">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
            <a href="{{ route('pacientes.vista') }}" class="btn btn-outline-light btn-sm me-2">Pacientes</a>
            <a href="{{ route('dentistas.vista') }}" class="btn btn-outline-light btn-sm me-2">Dentistas</a>
            <a href="{{ route('citas.vista') }}" class="btn btn-outline-light btn-sm me-2">Citas</a>
            <a href="{{ route('inventario.vista') }}" class="btn btn-outline-light btn-sm">Inventario</a>
            <a href="{{ route('expedientes.vista') }}" class="btn btn-outline-light btn-sm me-2">Expedientes</a>
            <a href="{{ route('tratamientos.vista') }}" class="btn btn-outline-light btn-sm me-2">Tratamientos</a>
            <a href="{{ route('recetas.vista') }}" class="btn btn-outline-light btn-sm me-2">Recetas</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-danger btn-sm">
                Cerrar sesión
            </button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-4">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>