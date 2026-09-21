<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paquetes documentados</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="max-w-6xl mx-auto px-4 py-6">
        @include('components.navbar')

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">DOCUMENTACIÓN</p>
                <h1 class="text-2xl font-bold mt-2">Paquetes documentados</h1>
            </div>
            <a href="{{ route('documentacion.index') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                Volver a pendientes
            </a>
        </div>

        @include('components.flash-messages')

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="divide-y divide-slate-100">
                @forelse ($packages as $package)
                    <div class="flex flex-wrap items-center justify-between gap-4 p-4">
                        <div>
                            <p class="font-semibold">{{ $package->guia_principal }}</p>
                            <p class="text-sm text-slate-500">
                                Cliente: {{ $package->client?->nombre ?: 'Sin cliente' }}
                                · Socio: {{ $package->partner?->nombre ?: 'Sin socio' }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('documentacion.index', ['guia_principal' => $package->guia_principal]) }}" class="rounded-lg border border-blue-600 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                                Editar guía
                            </a>
                            <form action="{{ route('documentacion.markAsReceived', $package) }}" method="POST" onsubmit="return confirm('¿Regresar esta guía al estado recibido?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    Marcar como recibido
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-500">No hay paquetes documentados.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
