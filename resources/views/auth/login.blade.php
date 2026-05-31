<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - DentalCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dental-teal: #067a7e;
            --dental-teal-dark: #056b70;
            --dental-bg: #f4fafb;
        }

        body {
            background: var(--dental-bg);
            color: #203238;
        }

        .login-side {
            background: var(--dental-teal);
            color: #fff;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .login-side::before,
        .login-side::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
        }

        .login-side::before {
            width: 190px;
            height: 190px;
            right: 30px;
            top: 70px;
        }

        .login-side::after {
            width: 260px;
            height: 260px;
            left: -90px;
            bottom: -80px;
        }

        .login-card,
        .info-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(22, 66, 72, .12);
        }

        .btn-primary {
            --bs-btn-bg: var(--dental-teal);
            --bs-btn-border-color: var(--dental-teal);
            --bs-btn-hover-bg: var(--dental-teal-dark);
            --bs-btn-hover-border-color: var(--dental-teal-dark);
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row min-vh-100">
        <div class="col-lg-5 login-side d-flex align-items-center p-5">
            <div class="position-relative">
                <h1 class="h3 fw-bold mb-2">DentalCare</h1>
                <p class="mb-5 text-white-50">Sistema integral para la administracion de un consultorio dental.</p>

                <div class="card info-card" style="max-width: 290px;">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; background: #eef9fb; color: #067a7e; font-size: 24px; font-weight: 700;">DC</div>
                        <h2 class="h6 fw-bold text-dark">Atencion organizada, rapida y profesional.</h2>
                        <p class="small text-muted mb-0">Control de citas, pacientes, tratamientos e inventario desde una sola plataforma.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 d-flex align-items-center justify-content-center p-4">
            <div class="card login-card w-100" style="max-width: 380px;">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-1">Bienvenido</h2>
                    <p class="text-muted small mb-4">Inicia sesion para continuar</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Credenciales incorrectas.
                        </div>
                    @endif

                    <form action="{{ route('login.procesar') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Correo electronico</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Contrasena</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button class="btn btn-primary w-100">Iniciar sesion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
