@extends('layouts.app')

@section('title', 'Configuracion - DentalCare')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Configuracion</h2>
        <p class="text-muted mb-0">Administra los datos de acceso de tu cuenta.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <strong>Cuenta actual</strong>
            </div>

            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="user-dot border">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</span>
                    <div>
                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                        <div class="text-muted small">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <dl class="row mb-0 small">
                    <dt class="col-4">Rol</dt>
                    <dd class="col-8 text-capitalize">{{ Auth::user()->rol ?? 'Sin rol' }}</dd>

                    <dt class="col-4">Usuario</dt>
                    <dd class="col-8">#{{ Auth::id() }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <strong>Cambiar contraseña</strong>
            </div>

            <div class="card-body">
                <form action="{{ route('configuracion.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Contraseña actual</label>
                        <input type="password" name="password_actual" class="form-control" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                            <div class="form-text">Minimo 8 caracteres.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary">Actualizar contraseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
