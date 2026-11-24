<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción en bodega</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')
    </div>

    <div class="w-100 content-medium mx-auto px-3 py-4">
        <div class="mb-5">
            <p class="small text-uppercase letter-spaced text-primary fw-semibold">BODEGA</p>
            <h1 class="fs-4 fw-bold mt-2 d-flex align-items-center gap-2">
                Recepción
                <span id="pending-counter" class="rounded-pill px-2 py-1 small fw-semibold" style="background-color: #facc15; color: #422006" title="Guías por recibir">{{ $pendingCount }}</span>
            </h1>
        </div>

        @if (session('warning'))
            <div class="bg-warning-subtle border border-warning-subtle text-warning-emphasis px-3 py-2 rounded-4 mb-4 small fw-medium">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-success-subtle border border-success-subtle text-success px-3 py-2 rounded-4 mb-4 small fw-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-danger-subtle border border-secondary-subtle text-danger px-3 py-2 rounded-4 mb-4 small fw-medium">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('bodega.store') }}" method="POST" class="bg-white rounded-4 shadow-sm border border-secondary-subtle p-3 stack-gap-4">
            @csrf

            <x-camera-scanner
                id="warehouse-guide-scanner"
                name="guia_principal"
                label="Escanee la guía"
                placeholder="Guía principal"
            />

            <button type="submit" class="btn btn-primary w-100 bg-primary text-white px-4 py-3 rounded-4 fw-semibold hover-bg-primary">
                Validar guía
            </button>
        </form>
    </div>
</body>
</html>
