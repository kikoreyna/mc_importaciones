<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar guía</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 antialiased" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    <div class="w-full px-4 py-6">
        @include('components.navbar')

        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Búsqueda</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">Buscar guía</h1>
            @if ($term !== '')
                <p class="mt-1 text-sm text-slate-500">{{ $packages->count() }} {{ $packages->count() === 1 ? 'resultado' : 'resultados' }} para "{{ $term }}"</p>
            @endif
        </div>

        @if ($term === '')
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600 shadow-sm">
                Escribe una guía principal, secundaria o máster en el buscador de la barra.
            </div>
        @elseif ($packages->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-600 shadow-sm">
                No se encontraron guías que coincidan con "{{ $term }}".
            </div>
        @else
            <div class="grid gap-4 lg:grid-cols-2 2xl:grid-cols-3">
                @foreach ($packages as $package)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 sm:grid-cols-3">
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Guía principal</dt>
                                <dd class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $package->guia_principal }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Guía secundaria</dt>
                                <dd class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $package->guia_secundaria ?: 'N/A' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Guía máster</dt>
                                <dd class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $package->guia_master ?: 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Estado</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold capitalize text-green-800">{{ str_replace('_', ' ', $package->estado) }}</span>
                                </dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Cliente</dt>
                                <dd class="mt-1 truncate text-sm text-slate-700">{{ $package->client?->nombre ?: 'Sin cliente' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Socio</dt>
                                <dd class="mt-1 truncate text-sm text-slate-700">{{ $package->partner?->nombre ?: 'Sin socio' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Transportadora</dt>
                                <dd class="mt-1 truncate text-sm text-slate-700">{{ $package->transportadora ?: 'Sin transportadora' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Cajas</dt>
                                <dd class="mt-1 truncate text-sm text-slate-700">{{ $package->caja_numero && $package->total_cajas ? $package->caja_numero . ' de ' . $package->total_cajas : 'Sin secuencia' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Recibido</dt>
                                <dd class="mt-1 truncate text-sm text-slate-700">{{ $package->created_at?->format('d/m/Y H:i') ?: 'Sin fecha' }}</dd>
                            </div>
                        </dl>

                        @if ($package->photos->isNotEmpty())
                            <div class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-4">
                                @foreach ($package->photos as $photo)
                                    <a href="{{ $photo->display_url }}" target="_blank" rel="noopener" class="aspect-square cursor-pointer overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                                        <img src="{{ $photo->display_url }}" alt="Foto del paquete" loading="lazy" class="h-full w-full object-cover transition hover:scale-105">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
