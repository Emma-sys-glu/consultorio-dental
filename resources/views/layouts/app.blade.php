<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Consultorio Dental')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @laravelPWA
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
                transform: translateX(-220px);
                transition: transform .28s cubic-bezier(.4, 0, .2, 1),
                            box-shadow .28s ease;
                z-index: 1040;
            }

            .app-sidebar.sidebar-open {
                transform: translateX(0);
                box-shadow: 4px 0 24px rgba(6, 122, 126, .3);
            }

            .app-content {
                margin-left: 0;
            }

            /* Botón flotante para abrir/cerrar el sidebar */
            #sidebar-toggle {
                position: fixed;
                left: 0;
                bottom: 24px;
                z-index: 1050;
                width: 36px;
                height: 36px;
                background: var(--dental-teal);
                color: #fff;
                border: none;
                border-radius: 0 8px 8px 0;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 2px 2px 8px rgba(0,0,0,.2);
                transition: left .28s cubic-bezier(.4,0,.2,1), background .15s;
            }
            #sidebar-toggle:hover { background: var(--dental-teal-dark); }
            #sidebar-toggle.sidebar-open { left: 220px; }
            #sidebar-toggle .toggle-icon { font-size: 18px; line-height: 1; }
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
        @php
            $rol       = Auth::user()->rol ?? null;
            $homeRoute = match($rol) {
                'dentista' => route('dashboard.dentista'),
                'paciente' => route('dashboard.paciente'),
                default    => route('dashboard'),
            };
            $homeActive = request()->routeIs('dashboard') || request()->routeIs('dashboard.*');
        @endphp

        <a href="{{ $homeRoute }}" class="sidebar-link {{ $homeActive ? 'active' : '' }}">Inicio</a>

        @if($rol === 'administrador')
            <a href="{{ route('citas.vista') }}"        class="sidebar-link {{ request()->routeIs('citas.*')            ? 'active' : '' }}">Citas</a>
            <a href="{{ route('pacientes.vista') }}"    class="sidebar-link {{ request()->routeIs('pacientes.*')        ? 'active' : '' }}">Pacientes</a>
            <a href="{{ route('tratamientos.vista') }}" class="sidebar-link {{ request()->routeIs('tratamientos.*')     ? 'active' : '' }}">Tratamientos</a>
            <a href="{{ route('inventario.vista') }}"   class="sidebar-link {{ request()->routeIs('inventario.*')       ? 'active' : '' }}">Inventario</a>
            <a href="{{ route('inventario.alertas') }}" class="sidebar-link {{ request()->routeIs('inventario.alertas') ? 'active' : '' }}">Alertas Inventario</a>
            <a href="{{ route('dentistas.vista') }}"    class="sidebar-link {{ request()->routeIs('dentistas.*')        ? 'active' : '' }}">Dentistas</a>
            <a href="{{ route('expedientes.vista') }}"  class="sidebar-link {{ request()->routeIs('expedientes.*')      ? 'active' : '' }}">Expedientes</a>
            <a href="{{ route('recetas.vista') }}"      class="sidebar-link {{ request()->routeIs('recetas.*')          ? 'active' : '' }}">Recetas</a>
        @endif

        @if($rol === 'recepcionista')
            <a href="{{ route('citas.vista') }}"       class="sidebar-link {{ request()->routeIs('citas.*')       ? 'active' : '' }}">Citas</a>
            <a href="{{ route('pacientes.vista') }}"   class="sidebar-link {{ request()->routeIs('pacientes.*')   ? 'active' : '' }}">Pacientes</a>
            <a href="{{ route('expedientes.vista') }}" class="sidebar-link {{ request()->routeIs('expedientes.*') ? 'active' : '' }}">Expedientes</a>
            <a href="{{ route('inventario.vista') }}"  class="sidebar-link {{ request()->routeIs('inventario.*')  ? 'active' : '' }}">Inventario</a>
        @endif

        @if($rol === 'dentista')
            <a href="{{ route('citas.vista') }}"        class="sidebar-link {{ request()->routeIs('citas.*')        ? 'active' : '' }}">Mis Citas</a>
            <a href="{{ route('expedientes.vista') }}"  class="sidebar-link {{ request()->routeIs('expedientes.*')  ? 'active' : '' }}">Expedientes</a>
            <a href="{{ route('tratamientos.vista') }}" class="sidebar-link {{ request()->routeIs('tratamientos.*') ? 'active' : '' }}">Tratamientos</a>
            <a href="{{ route('recetas.vista') }}"      class="sidebar-link {{ request()->routeIs('recetas.*')      ? 'active' : '' }}">Recetas</a>
        @endif

        @if($rol === 'paciente')
            <a href="{{ route('citas.vista') }}"          class="sidebar-link {{ request()->routeIs('citas.*')          ? 'active' : '' }}">Mis Citas</a>
            <a href="{{ route('recetas.vista') }}"        class="sidebar-link {{ request()->routeIs('recetas.*')        ? 'active' : '' }}">Mis Recetas</a>
            <a href="{{ route('notificaciones.index') }}" class="sidebar-link {{ request()->routeIs('notificaciones.*') ? 'active' : '' }}">Mis Notificaciones</a>
        @endif

        <a href="{{ route('configuracion.index') }}" class="sidebar-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">Configuración</a>
    </div>
</div>


<aside class="app-sidebar d-none d-lg-flex p-4">
    @php
        $rol       = Auth::user()->rol ?? null;
        $homeRoute = match($rol) {
            'dentista' => route('dashboard.dentista'),
            'paciente' => route('dashboard.paciente'),
            default    => route('dashboard'),
        };
        $homeActive = request()->routeIs('dashboard') || request()->routeIs('dashboard.*');
    @endphp

    <a href="{{ $homeRoute }}"
       class="text-white text-decoration-none d-flex align-items-center gap-2 mb-4">
        <span class="brand-dot">D</span>
        <span>
            <strong class="d-block lh-sm">DentalCare</strong>
            <small class="text-white-50">Sistema clínico</small>
        </span>
    </a>

    <a href="{{ $homeRoute }}" class="sidebar-link {{ $homeActive ? 'active' : '' }}">Inicio</a>

    @if($rol === 'administrador')
        <a href="{{ route('citas.vista') }}"        class="sidebar-link {{ request()->routeIs('citas.*')             ? 'active' : '' }}">Citas</a>
        <a href="{{ route('pacientes.vista') }}"    class="sidebar-link {{ request()->routeIs('pacientes.*')         ? 'active' : '' }}">Pacientes</a>
        <a href="{{ route('tratamientos.vista') }}" class="sidebar-link {{ request()->routeIs('tratamientos.*')      ? 'active' : '' }}">Tratamientos</a>
        <a href="{{ route('inventario.vista') }}"   class="sidebar-link {{ request()->routeIs('inventario.*')        ? 'active' : '' }}">Inventario</a>
        <a href="{{ route('inventario.alertas') }}" class="sidebar-link {{ request()->routeIs('inventario.alertas')  ? 'active' : '' }}">Alertas Inventario</a>
        <a href="{{ route('dentistas.vista') }}"    class="sidebar-link {{ request()->routeIs('dentistas.*')         ? 'active' : '' }}">Dentistas</a>
        <a href="{{ route('expedientes.vista') }}"  class="sidebar-link {{ request()->routeIs('expedientes.*')       ? 'active' : '' }}">Expedientes</a>
        <a href="{{ route('recetas.vista') }}"      class="sidebar-link {{ request()->routeIs('recetas.*')           ? 'active' : '' }}">Recetas</a>
    @endif

    @if($rol === 'recepcionista')
        <a href="{{ route('citas.vista') }}"       class="sidebar-link {{ request()->routeIs('citas.*')       ? 'active' : '' }}">Citas</a>
        <a href="{{ route('pacientes.vista') }}"   class="sidebar-link {{ request()->routeIs('pacientes.*')   ? 'active' : '' }}">Pacientes</a>
        <a href="{{ route('expedientes.vista') }}" class="sidebar-link {{ request()->routeIs('expedientes.*') ? 'active' : '' }}">Expedientes</a>
        <a href="{{ route('inventario.vista') }}"  class="sidebar-link {{ request()->routeIs('inventario.*')  ? 'active' : '' }}">Inventario</a>
    @endif

    @if($rol === 'dentista')
        <a href="{{ route('citas.vista') }}"        class="sidebar-link {{ request()->routeIs('citas.*')        ? 'active' : '' }}">Mis Citas</a>
        <a href="{{ route('expedientes.vista') }}"  class="sidebar-link {{ request()->routeIs('expedientes.*')  ? 'active' : '' }}">Expedientes</a>
        <a href="{{ route('tratamientos.vista') }}" class="sidebar-link {{ request()->routeIs('tratamientos.*') ? 'active' : '' }}">Tratamientos</a>
        <a href="{{ route('recetas.vista') }}"      class="sidebar-link {{ request()->routeIs('recetas.*')      ? 'active' : '' }}">Recetas</a>
    @endif

    @if($rol === 'paciente')
        <a href="{{ route('citas.vista') }}"          class="sidebar-link {{ request()->routeIs('citas.*')          ? 'active' : '' }}">Mis Citas</a>
        <a href="{{ route('recetas.vista') }}"        class="sidebar-link {{ request()->routeIs('recetas.*')        ? 'active' : '' }}">Mis Recetas</a>
        <a href="{{ route('notificaciones.index') }}" class="sidebar-link {{ request()->routeIs('notificaciones.*') ? 'active' : '' }}">Mis Notificaciones</a>
    @endif

    <a href="{{ route('configuracion.index') }}" class="sidebar-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">Configuración</a>
</aside>

{{-- Botón toggle del sidebar (solo desktop) --}}
<button id="sidebar-toggle" class="d-none d-lg-flex" title="Abrir/cerrar menú" aria-label="Abrir/cerrar menú">
    <span class="toggle-icon">☰</span>
</button>

<div class="app-content">
    <header class="container-fluid px-4 pt-4">
        <div class="d-flex justify-content-end align-items-center gap-2 gap-sm-3">
            <span class="text-muted small d-none d-sm-inline">{{ Auth::user()->name ?? 'Usuario' }}</span>
            <span class="user-dot">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-secondary btn-sm">Cerrar sesión</button>
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

{{-- Banner para activar notificaciones push --}}
@auth
<div id="push-banner" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1100; display:none;">
    <div class="toast show border-0 shadow" role="alert">
        <div class="toast-header bg-primary text-white">
            <strong class="me-auto"><i class="bi bi-bell"></i> Notificaciones</strong>
            <button type="button" class="btn-close btn-close-white" onclick="ocultarBanner()"></button>
        </div>
        <div class="toast-body">
            Activa las notificaciones para recibir recordatorios de citas y avisos.
            <div class="mt-2 d-flex gap-2">
                <button id="btn-activar-push" class="btn btn-primary btn-sm">Activar</button>
                <button class="btn btn-secondary btn-sm" onclick="ocultarBanner()">Ahora no</button>
            </div>
        </div>
    </div>
</div>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

<script>
// ── PWA Push Notifications ─────────────────────────────────────────
(function () {
    const LOG    = '[PWA Push]';
    const banner = document.getElementById('push-banner');
    const btnAct = document.getElementById('btn-activar-push');

    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

    const VAPID_PUBLIC = '{{ config("pwa.vapid_public_key") }}';
    if (!VAPID_PUBLIC) return;

    function urlBase64ToUint8Array(base64) {
        const pad = '='.repeat((4 - base64.length % 4) % 4);
        const b64 = (base64 + pad).replace(/-/g, '+').replace(/_/g, '/');
        const raw = atob(b64);
        return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
    }

    function enviarSuscripcion(sub) {
        const data = sub.toJSON();
        fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data),
        })
        .then(r => r.json())
        .then(r => {
            console.log(LOG, 'Suscripción guardada:', r);
            ocultarBanner();
        })
        .catch(e => console.error(LOG, 'Error al guardar suscripción:', e));
    }

    function suscribir(sw) {
        return sw.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC),
        });
    }

    function activarPush() {
        navigator.serviceWorker.ready.then(function (sw) {
            Notification.requestPermission().then(function (permiso) {
                console.log(LOG, 'Permiso:', permiso);
                if (permiso !== 'granted') return;

                suscribir(sw)
                    .then(function (newSub) {
                        console.log(LOG, 'Suscripción creada.');
                        enviarSuscripcion(newSub);
                    })
                    .catch(function (e) {
                        console.error(LOG, 'Error al suscribir:', e);
                        // Limpia suscripción vieja y reintenta
                        sw.pushManager.getSubscription()
                            .then(old => old ? old.unsubscribe() : null)
                            .then(() => suscribir(sw))
                            .then(newSub => enviarSuscripcion(newSub))
                            .catch(e2 => console.error(LOG, 'Re-intento fallido:', e2));
                    });
            });
        });
    }

    if (btnAct) btnAct.addEventListener('click', function () {
        activarPush();
        localStorage.setItem(BANNER_KEY, '1'); // recuerda que el usuario eligió
    });

    // Al cargar: si ya hay permiso y suscripción, re-sincroniza con servidor.
    // Solo muestra el banner si el usuario NO tomó una decisión antes.
    const BANNER_KEY = 'push_decision_{{ auth()->id() }}';

    navigator.serviceWorker.ready.then(function (sw) {
        sw.pushManager.getSubscription().then(function (sub) {
            if (sub) {
                console.log(LOG, 'Ya suscrito, sincronizando con servidor.');
                enviarSuscripcion(sub);
            } else if (
                Notification.permission !== 'denied' &&
                !localStorage.getItem(BANNER_KEY) &&
                banner
            ) {
                banner.style.display = '';
            }
        });
    });
}());

window.ocultarBanner = function () {
    const b = document.getElementById('push-banner');
    if (b) b.style.display = 'none';
    localStorage.setItem('push_decision_{{ auth()->id() }}', '1');
};

// ── Sidebar toggle (desktop) ───────────────────────────────────────
(function () {
    const btn     = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.app-sidebar');
    if (!btn || !sidebar) return;

    const KEY = 'sidebar_open';

    function aplicarEstado(open) {
        if (open) {
            sidebar.classList.add('sidebar-open');
            btn.classList.add('sidebar-open');
            btn.querySelector('.toggle-icon').textContent = '✕';
            btn.title = 'Cerrar menú';
        } else {
            sidebar.classList.remove('sidebar-open');
            btn.classList.remove('sidebar-open');
            btn.querySelector('.toggle-icon').textContent = '☰';
            btn.title = 'Abrir menú';
        }
    }

    // Restaura el estado guardado (abierto por defecto si nunca se eligió)
    const guardado = localStorage.getItem(KEY);
    aplicarEstado(guardado === null ? false : guardado === '1');

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const abierto = sidebar.classList.contains('sidebar-open');
        aplicarEstado(!abierto);
        localStorage.setItem(KEY, abierto ? '0' : '1');
    });

    // Cerrar al hacer click fuera del sidebar
    document.addEventListener('click', function (e) {
        if (
            sidebar.classList.contains('sidebar-open') &&
            !sidebar.contains(e.target) &&
            !btn.contains(e.target)
        ) {
            aplicarEstado(false);
            localStorage.setItem(KEY, '0');
        }
    });
}());
</script>

</body>
</html>
