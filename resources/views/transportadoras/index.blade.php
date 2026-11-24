<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportadoras</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <div class="mb-5 d-flex align-items-center justify-content-between gap-3">
            <div>
                <p class="small text-uppercase letter-spaced text-primary fw-semibold">ADMINISTRACIÓN</p>
                <h1 class="fs-4 fw-bold mt-2">Transportadoras</h1>
            </div>
            <a href="{{ route('transportadoras.create') }}" class="btn btn-primary rounded-3 bg-primary px-3 py-2 small fw-semibold text-white hover-bg-primary">Nueva transportadora</a>
        </div>

        @include('components.flash-messages')

        <div class="overflow-hidden rounded-4 border border-secondary-subtle bg-white shadow-sm">
            <div class="divide-y divide-slate-100">
                @forelse ($transportadoras as $transportadora)
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3">
                        <div>
                            <p class="fw-semibold">{{ $transportadora->nombre }}</p>
                            <p class="small text-secondary">
                                <a href="{{ $transportadora->web }}" target="_blank" rel="noopener noreferrer" class="text-primary hover-underline">{{ $transportadora->web }}</a>
                                · {{ $transportadora->telefono ?: 'Sin teléfono' }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('transportadoras.edit', $transportadora) }}" class="rounded-3 border border-secondary-subtle px-2 py-2 small fw-semibold hover-bg-light">Editar</a>
                            <form action="{{ route('transportadoras.destroy', $transportadora) }}" method="POST" onsubmit="return confirm('¿Eliminar esta transportadora?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger rounded-3 bg-danger px-2 py-2 small fw-semibold text-white">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-4 small text-secondary">No hay transportadoras registradas.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
