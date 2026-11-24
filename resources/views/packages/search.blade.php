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
<body class="bg-light text-dark" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <div class="mb-5">
            <p class="small fw-semibold text-uppercase letter-spaced text-primary">Búsqueda</p>
            <h1 class="mt-1 fs-4 fw-bold tracking-tight text-dark fs-3">Buscar guía</h1>
            @if ($term !== '')
                <p class="mt-1 small text-secondary">{{ $packages->count() }} {{ $packages->count() === 1 ? 'resultado' : 'resultados' }} para "{{ $term }}"</p>
            @endif
        </div>

        @if ($term === '')
            <div class="rounded-4 border border-secondary-subtle bg-white p-4 small text-secondary shadow-sm">
                Escribe una guía principal, secundaria o máster en el buscador de la barra.
            </div>
        @elseif ($packages->isEmpty())
            <div class="rounded-4 border border-secondary-subtle bg-white p-4 small text-secondary shadow-sm">
                No se encontraron guías que coincidan con "{{ $term }}".
            </div>
        @else
            <div class="row g-3 g-3 row-cols-lg-2 row-cols-xxl-3">
                @foreach ($packages as $package)
                    <article class="rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm">
                        <dl class="row g-3 row-cols-2 column-gap-3 row-gap-2 row-cols-sm-3">
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Guía principal</dt>
                                <dd class="mt-1 text-truncate small fw-semibold text-dark">{{ $package->guia_principal }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Guía secundaria</dt>
                                <dd class="mt-1 text-truncate small fw-semibold text-dark">{{ $package->guia_secundaria ?: 'N/A' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Guía máster</dt>
                                <dd class="mt-1 text-truncate small fw-semibold text-dark">{{ $package->guia_master ?: 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Color</dt>
                                <dd class="mt-1">
                                    <x-package-color :package="$package" />
                                </dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Cliente</dt>
                                <dd class="mt-1 text-truncate small text-body">{{ $package->client?->nombre ?: 'Sin cliente' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Socio</dt>
                                <dd class="mt-1 text-truncate small text-body">{{ $package->partner?->nombre ?: 'Sin socio' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Transportadora</dt>
                                <dd class="mt-1 text-truncate small text-body">{{ $package->transportadora ?: 'Sin transportadora' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Cajas</dt>
                                <dd class="mt-1 text-truncate small text-body">{{ $package->caja_numero && $package->total_cajas ? $package->caja_numero . ' de ' . $package->total_cajas : '1 de 1' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Recibido</dt>
                                <dd class="mt-1 text-truncate small text-body">{{ $package->created_at?->format('d/m/Y H:i') ?: 'Sin fecha' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <a href="{{ route('documentacion.index', ['guia' => $package->id]) }}" class="btn btn-primary d-inline-flex cursor-pointer align-items-center rounded-3 bg-primary px-3 py-2 small fw-semibold text-white shadow-sm hover-bg-primary">Editar documentación</a>
                            <a href="{{ route('packages.history', $package) }}" class="btn btn-secondary d-inline-flex cursor-pointer align-items-center rounded-3 bg-secondary px-3 py-2 small fw-semibold text-white shadow-sm">Ver historial</a>
                        </div>
                        @if ($package->photos->isNotEmpty())
                            <div class="mt-3 row g-3 row-cols-3 g-2 row-cols-sm-4">
                                @foreach ($package->photos as $photo)
                                    <a href="{{ $photo->display_url }}" target="_blank" rel="noopener" class="aspect-square cursor-pointer overflow-hidden rounded-3 border border-secondary-subtle bg-body-tertiary">
                                        <img src="{{ $photo->display_url }}" alt="Foto del paquete" loading="lazy" decoding="async" class="h-100 w-100 object-fit-cover transition-soft hover-scale">
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
