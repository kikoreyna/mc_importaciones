<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex min-vh-100 align-items-center justify-content-center bg-body-tertiary px-3 text-dark">
    <main class="w-100 content-narrow rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm">
        <p class="small fw-semibold text-uppercase letter-spaced text-primary">MC IMPORTACIONES</p>
        <h1 class="mt-2 fs-4 fw-bold">Iniciar sesión</h1>
        <p class="mt-1 small text-secondary">Accede para consultar y administrar las guías.</p>

        @include('components.flash-messages')

        <form action="{{ route('login.store') }}" method="POST" class="mt-5 stack-gap-4">
            @csrf
            <div>
                <label for="email" class="mb-2 d-block small fw-medium">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2">
            </div>
            <div>
                <label for="password" class="mb-2 d-block small fw-medium">Contraseña</label>
                <input id="password" name="password" type="password" required class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2">
            </div>
            <label class="d-flex align-items-center gap-2 small text-secondary">
                <input type="checkbox" name="remember" value="1" class="form-check-input rounded border-secondary-subtle text-primary">
                Recordarme
            </label>
            <button type="submit" class="btn btn-primary w-100 rounded-4 bg-primary px-3 py-2 small fw-semibold text-white hover-bg-primary">Entrar</button>
        </form>
    </main>
</body>
</html>