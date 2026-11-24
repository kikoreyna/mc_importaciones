<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MC Importaciones</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
        <section class="card border-0 shadow-sm content-narrow w-100">
            <div class="card-body p-4 p-md-5 text-center">
                <p class="small text-uppercase letter-spaced text-primary fw-semibold mb-2">MC Importaciones</p>
                <h1 class="h2 fw-bold mb-3">Gestión de importaciones</h1>
                <p class="text-body-secondary mb-4">Administra la recepción, documentación y seguimiento de tus paquetes desde un solo lugar.</p>
                <a class="btn btn-primary px-4" href="{{ url('/login') }}">Iniciar sesión</a>
            </div>
        </section>
    </main>
</body>
</html>
