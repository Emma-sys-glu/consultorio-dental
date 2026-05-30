<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Consultorio Dental</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div class="card shadow border-0 rounded-4" style="max-width: 420px; width: 100%;">
        <div class="card-body p-4">

            <h3 class="fw-bold text-center mb-2" style="color:#007C89;">
                Consultorio Dental
            </h3>

            <p class="text-muted text-center mb-4">
                Inicio de sesión del sistema
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    Credenciales incorrectas.
                </div>
            @endif

            <form action="{{ route('login.procesar') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn w-100 text-white" style="background-color:#007C89;">
                    Iniciar sesión
                </button>
            </form>

        </div>
    </div>

</div>

</body>
</html>