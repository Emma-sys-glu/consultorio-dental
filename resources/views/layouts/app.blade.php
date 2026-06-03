<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Consultorio Dental')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dental-teal:      #067a7e;
            --dental-teal-dark: #056b70;
            --dental-cyan:      #12b8d7;
            --dental-bg:        #f4fafb;
            --dental-soft:      #e6f7fa;
        }

        body {
            background: var(--dental-bg);
            color: #203238;
            font-size: 14px;
        }

        .app-sidebar {
            background: var(--dental-teal);
            width: 220px;
            flex-direction: column;
        }

        @media (min-width: 992px) {
            .app-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                overflow-y: auto;
                
                transform: translateX(calc(-220px + 8px));
                transition: transform .28s cubic-bezier(.4, 0, .2, 1),
                            box-shadow .28s ease;
                z-index: 1040;
            }

            .app-sidebar:hover {
                transform: translateX(0);
                box-shadow: 4px 0 24px rgba(6, 122, 126, .3);
            }

            /* el contenido ocupa toda la pantalla; el sidebar se superpone */
            .app-content {
                margin-left: 0;
            }
        }

        .brand-dot,
        .user-dot {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #fff;
            color: var(--dental-teal);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .sidebar-link {
            color: rgba(255, 255, 255, .9);
            border-radius: 9px;
            padding: .65rem .9rem;
            text-decoration: none;
            display: block;
            margin-bottom: .3rem;
            transition: background .15s, color .15s;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, .15);
            color: #fff;
        }

        .sidebar-link.active {
            background: #fff;
            color: var(--dental-teal);
            font-weight: 600;
        }

        /* ── Cards ───────────────────────────────── */
        .card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 8px 22px rgba(22, 66, 72, .08);
        }

        .card-header {
            border-bottom: 0;
            border-radius: 14px 14px 0 0 !important;
        }

        .table-light th {
            background: var(--dental-soft);
            color: var(--dental-teal-dark);
            border: 0;
            font-size: 12px;
        }

        .btn-primary {
            --bs-btn-bg: var(--dental-teal);
            --bs-btn-border-color: var(--dental-teal);
            --bs-btn-hover-bg: var(--dental-teal-dark);
            --bs-btn-hover-border-color: var(--dental-teal-dark);
        }

        .btn-info {
            --bs-btn-bg: var(--dental-cyan);
            --bs-btn-border-color: var(--dental-cyan);
            --bs-btn-color: #fff;
            --bs-btn-hover-color: #fff;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark d-lg-none" style="background: var(--dental-teal);">
    <div class="container-fluid">
        <span class="navbar-brand fw-semibold">DentalCare</span>
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMovil"
                aria-controls="sidebarMovil"
                aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<div class="offcanvas offcanvas-start d-lg-none"
     style="background: var(--dental-teal); width: 220px;"
     tabindex="-1"
     id="sidebarMovil">

    <div class="offcanvas-header pb-1">
        <a href="{{ route('dashboard') }}"
           class="text-white text-decoration-none d-flex align-items-center gap-2">
            <span class="brand-dot">D</span>
            <span>
                <strong class="d-block lh-sm">DentalCare</strong>
                <small class="text-white-50">Sistema clínico</small>
            </span>
        </a>
        <button type="button"
                class="btn-close btn-close-white ms-2"
                data-bs-dismiss="offcanvas"
                aria-label="Cerrar"></button>
    </div>

    <div class="offcanvas-body pt-3 px-3">
        @php $rol = Auth::user()->rol ?? null; @endphp

        @if($rol === 'administrador')
            <a href="{{ route('citas.vista') }}"       class="sidebar-link {{ request()->routeIs('citas.*')         ? 'active' : '' }}">Citas</a>
            <a href="{{ route('pacientes.vista') }}"   class="sidebar-link {{ request()->routeIs('pacientes.*')     ? 'active' : '' }}">Pacientes</a>
            <a href="{{ route('tratamientos.vista') }}" class="sidebar-link {{ request()->routeIs('tratamientos.*') ? 'active' : '' }}">Tratamientos</a>
            <a href="{{ route('inventario.vista') }}"  class="sidebar-link {{ request()->routeIs('inventario.*')    ? 'active' : '' }}">Inventario</a>
            <a href="{{ route('inventario.alertas') }}" class="sidebar-link {{ request()->routeIs('inventario.alertas') ? 'active' : '' }}">Alertas Inventario</a>
            <a href="{{ route('dentistas.vista') }}"   class="sidebar-link {{ request()->routeIs('dentistas.*')     ? 'active' : '' }}">Dentistas</a>
            <a href="{{ route('expedientes.vista') }}" class="sidebar-link {{ request()->routeIs('expedientes.*')   ? 'active' : '' }}">Expedientes</a>
            <a href="{{ route('recetas.vista') }}"     class="sidebar-link {{ request()->routeIs('recetas.*')       ? 'active' : '' }}">Recetas</a>
        @endif

        @if($rol === 'recepcionista')
            <a href="{{ route('citas.vista') }}"      class="sidebar-link {{ request()->routeIs('citas.*')      ? 'active' : '' }}">Citas</a>
            <a href="{{ route('pacientes.vista') }}"  class="sidebar-link {{ request()->routeIs('pacientes.*')  ? 'active' : '' }}">Pacientes</a>
            <a href="{{ route('inventario.vista') }}" class="sidebar-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}">Inventario</a>
        @endif

        @if($rol === 'dentista')
            <a href="{{ route('citas.vista') }}"       class="sidebar-link {{ request()->routeIs('citas.*')         ? 'active' : '' }}">Mis Citas</a>
            <a href="{{ route('expedientes.vista') }}" class="sidebar-link {{ request()->routeIs('expedientes.*')   ? 'active' : '' }}">Expedientes</a>
            <a href="{{ route('tratamientos.vista') }}" class="sidebar-link {{ request()->routeIs('tratamientos.*') ? 'active' : '' }}">Tratamientos</a>
            <a href="{{ route('recetas.vista') }}"     class="sidebar-link {{ request()->routeIs('recetas.*')       ? 'active' : '' }}">Recetas</a>
        @endif

        @if($rol === 'paciente')
            <a href="{{ route('citas.vista') }}"         class="sidebar-link {{ request()->routeIs('citas.*')          ? 'active' : '' }}">Mis Citas</a>
            <a href="{{ route('recetas.vista') }}"       class="sidebar-link {{ request()->routeIs('recetas.*')        ? 'active' : '' }}">Mis Recetas</a>
            <a href="{{ route('notificaciones.index') }}" class="sidebar-link {{ request()->routeIs('notificaciones.*') ? 'active' : '' }}">Mis Notificaciones</a>
        @endif

        <a href="{{ route('configuracion.index') }}" class="sidebar-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">Configuracion</a>
    </div>
</div>


<aside class="app-sidebar d-none d-lg-flex p-4">
    <a href="{{ route('dashboard') }}"
       class="text-white text-decoration-none d-flex align-items-center gap-2 mb-4">
        <span class="brand-dot">D</span>
        <span>
            <strong class="d-block lh-sm">DentalCare</strong>
            <small class="text-white-50">Sistema clínico</small>
        </span>
    </a>

    @php $rol = Auth::user()->rol ?? null; @endphp

    @if($rol === 'administrador')
        <a href="{{ route('citas.vista') }}"       class="sidebar-link {{ request()->routeIs('citas.*')         ? 'active' : '' }}">Citas</a>
        <a href="{{ route('pacientes.vista') }}"   class="sidebar-link {{ request()->routeIs('pacientes.*')     ? 'active' : '' }}">Pacientes</a>
        <a href="{{ route('tratamientos.vista') }}" class="sidebar-link {{ request()->routeIs('tratamientos.*') ? 'active' : '' }}">Tratamientos</a>
        <a href="{{ route('inventario.vista') }}"  class="sidebar-link {{ request()->routeIs('inventario.*')    ? 'active' : '' }}">Inventario</a>
        <a href="{{ route('inventario.alertas') }}" class="sidebar-link {{ request()->routeIs('inventario.alertas') ? 'active' : '' }}">Alertas Inventario</a>
        <a href="{{ route('dentistas.vista') }}"   class="sidebar-link {{ request()->routeIs('dentistas.*')     ? 'active' : '' }}">Dentistas</a>
        <a href="{{ route('expedientes.vista') }}" class="sidebar-link {{ request()->routeIs('expedientes.*')   ? 'active' : '' }}">Expedientes</a>
        <a href="{{ route('recetas.vista') }}"     class="sidebar-link {{ request()->routeIs('recetas.*')       ? 'active' : '' }}">Recetas</a>
    @endif

    @if($rol === 'recepcionista')
        <a href="{{ route('citas.vista') }}"      class="sidebar-link {{ request()->routeIs('citas.*')      ? 'active' : '' }}">Citas</a>
        <a href="{{ route('pacientes.vista') }}"  class="sidebar-link {{ request()->routeIs('pacientes.*')  ? 'active' : '' }}">Pacientes</a>
        <a href="{{ route('inventario.vista') }}" class="sidebar-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}">Inventario</a>
    @endif

    @if($rol === 'dentista')
        <a href="{{ route('citas.vista') }}"       class="sidebar-link {{ request()->routeIs('citas.*')         ? 'active' : '' }}">Mis Citas</a>
        <a href="{{ route('expedientes.vista') }}" class="sidebar-link {{ request()->routeIs('expedientes.*')   ? 'active' : '' }}">Expedientes</a>
        <a href="{{ route('tratamientos.vista') }}" class="sidebar-link {{ request()->routeIs('tratamientos.*') ? 'active' : '' }}">Tratamientos</a>
        <a href="{{ route('recetas.vista') }}"     class="sidebar-link {{ request()->routeIs('recetas.*')       ? 'active' : '' }}">Recetas</a>
    @endif

    @if($rol === 'paciente')
        <a href="{{ route('citas.vista') }}"         class="sidebar-link {{ request()->routeIs('citas.*')          ? 'active' : '' }}">Mis Citas</a>
        <a href="{{ route('recetas.vista') }}"       class="sidebar-link {{ request()->routeIs('recetas.*')        ? 'active' : '' }}">Mis Recetas</a>
        <a href="{{ route('notificaciones.index') }}" class="sidebar-link {{ request()->routeIs('notificaciones.*') ? 'active' : '' }}">Mis Notificaciones</a>
    @endif

    <a href="{{ route('configuracion.index') }}" class="sidebar-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">Configuracion</a>
</aside>


<div class="app-content">
    <header class="container-fluid px-4 pt-4">
        <div class="d-flex justify-content-end align-items-center gap-2 gap-sm-3">
            <span class="text-muted small d-none d-sm-inline">{{ Auth::user()->name ?? 'Usuario' }}</span>
            <span class="user-dot">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-secondary btn-sm">Cerrar sesion</button>
            </form>
        </div>
    </header>

    <main class="container-fluid px-4 py-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Revisa los datos ingresados.</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
