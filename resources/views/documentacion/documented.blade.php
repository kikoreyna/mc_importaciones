<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paquetes documentados</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <div class="mb-5 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <p class="small text-uppercase letter-spaced text-primary fw-semibold">DOCUMENTACIÓN</p>
                <h1 class="fs-4 fw-bold mt-2">Paquetes documentados</h1>
            </div>
            <a href="{{ route('documentacion.index') }}" class="btn btn-primary rounded-3 bg-primary px-3 py-2 small fw-semibold text-white hover-bg-primary">
                Volver a pendientes
            </a>
        </div>

        @include('components.flash-messages')

        <div class="overflow-hidden rounded-4 border border-secondary-subtle bg-white shadow-sm">
            <div class="divide-y divide-slate-100">
                @forelse ($packages as $package)
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3">
                        <div>
                            <p class="fw-semibold">{{ $package->guia_principal }}</p>
                            <p class="small text-secondary">
                                Cliente: {{ $package->client?->nombre ?: 'Sin cliente' }}
                                · Socio: {{ $package->partner?->nombre ?: 'Sin socio' }}
                            </p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('documentacion.index', ['guia_principal' => $package->guia_principal]) }}" class="rounded-3 border border-primary px-2 py-2 small fw-semibold text-primary">
                                Editar guía
                            </a>
                            <form action="{{ route('documentacion.markAsReceived', $package) }}" method="POST" onsubmit="return confirm('¿Regresar esta guía a pendiente de documentar?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary rounded-3 bg-primary px-2 py-2 small fw-semibold text-white hover-bg-primary">
                                    Marcar como recibido
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-4 small text-secondary">No hay paquetes documentados.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
