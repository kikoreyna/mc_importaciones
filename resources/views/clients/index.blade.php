<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')
        <div class="mb-5 d-flex align-items-center justify-content-between gap-3">
            <div><p class="small text-uppercase letter-spaced text-primary fw-semibold">ADMINISTRACIÓN</p><h1 class="fs-4 fw-bold mt-2">Clientes</h1></div>
            <a href="{{ route('clients.create') }}" class="btn btn-primary rounded-3 bg-primary px-3 py-2 small fw-semibold text-white hover-bg-primary">Nuevo cliente</a>
        </div>
        @include('components.flash-messages')
        <div class="overflow-hidden rounded-4 border border-secondary-subtle bg-white shadow-sm">
            <div class="divide-y divide-slate-100">
                @forelse ($clients as $client)
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3">
                        <div><p class="fw-semibold">{{ $client->nombre }}</p><p class="small text-secondary">{{ $client->alias ?: 'Sin alias' }} · {{ $client->partner?->alias ?: ($client->partner?->nombre ?: 'Sin socio') }}</p></div>
                        <div class="d-flex align-items-center gap-2"><a href="{{ route('clients.edit', $client) }}" class="rounded-3 border border-secondary-subtle px-2 py-2 small fw-semibold hover-bg-light">Editar</a><form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('¿Eliminar este cliente?');">@csrf @method('DELETE')<button class="btn btn-danger rounded-3 bg-danger px-2 py-2 small fw-semibold text-white">Eliminar</button></form></div>
                    </div>
                @empty
                    <p class="p-4 small text-secondary">No hay clientes registrados.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
