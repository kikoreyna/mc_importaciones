<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparativo USA - MEX</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <div class="mb-5 d-flex flex-wrap align-items-end justify-content-between gap-2">
            <div>
                <p class="small fw-semibold text-uppercase letter-spaced text-primary">REPORTERÍA</p>
                <h1 class="mt-2 fs-4 fw-bold fs-3">Comparativo USA - Bodega MEX</h1>
                <p class="mt-1 small text-secondary">Control de guías recibidas en origen y confirmadas en destino.</p>
            </div>
            <a href="{{ route('reports.comparison', ['fecha' => $fecha, 'grupo' => 'mex']) }}" class="rounded-3 bg-primary-subtle px-2 py-2 small fw-semibold text-primary">
                {{ $arrivalPercentage }}% recibidas en MEX · Ver guías
            </a>
        </div>

        <form action="{{ route('reports.comparison') }}" method="GET" class="mb-5 d-flex flex-column gap-2 rounded-4 border border-secondary-subtle bg-white p-3 shadow-sm flex-sm-row align-items-sm-end">
            <div class="flex-fill">
                <label for="fecha" class="mb-2 d-block small fw-medium">Fecha de recepción USA</label>
                <input id="fecha" name="fecha" type="date" value="{{ $fecha }}" class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2 small">
            </div>
            <button class="btn btn-primary rounded-4 bg-primary px-4 py-2 small fw-semibold text-white hover-bg-primary" type="submit">Filtrar</button>
            <a href="{{ route('reports.comparison', ['fecha' => today()->toDateString()]) }}" class="rounded-4 border border-secondary-subtle px-4 py-2 text-center small fw-semibold text-body hover-bg-light">Hoy</a>
        </form>

        @include('components.flash-messages')

        @if ($grupo)
            <div class="mb-5 d-flex align-items-center justify-content-between gap-2 rounded-4 border border-primary-subtle bg-primary-subtle px-3 py-2 small text-primary">
                <span>Mostrando: <strong>{{ ['usa' => 'Recibidas en USA', 'mex' => 'Recibidas en Bodega MEX', 'pendientes' => 'Pendientes de llegada'][$grupo] }}</strong></span>
                <a href="{{ route('reports.comparison', ['fecha' => $fecha]) }}" class="fw-semibold underline">Ver todas</a>
            </div>
        @endif

        <div class="mb-3 row g-3 row-cols-3 g-2 g-3">
            <a href="{{ route('reports.comparison', ['fecha' => $fecha, 'grupo' => 'usa']) }}" class="min-w-0 rounded-4 border border-primary-subtle bg-primary-subtle p-2 transition-soft hover-shadow p-sm-4">
                <p class="small fw-semibold text-uppercase letter-spaced text-primary">Recibidas en USA</p>
                <p class="mt-2 fs-3 fw-bold text-dark">{{ $receivedInUsa }}</p>
                <p class="mt-1 small text-primary">{{ $totalPackages }} paquetes · Ver guías</p>
            </a>
            <a href="{{ route('reports.comparison', ['fecha' => $fecha, 'grupo' => 'mex']) }}" class="min-w-0 rounded-4 border border-secondary-subtle bg-success-subtle p-2 transition-soft hover-shadow p-sm-4">
                <p class="small fw-semibold text-uppercase letter-spaced text-success">Recibidas en Bodega MEX</p>
                <p class="mt-2 fs-3 fw-bold text-dark">{{ $receivedInMexico }}</p>
                <p class="mt-1 small text-success">{{ $arrivedPackages }} paquetes · Ver guías</p>
            </a>
            <a href="{{ route('reports.comparison', ['grupo' => 'pendientes']) }}" class="min-w-0 rounded-4 border border-warning-subtle bg-warning-subtle p-2 transition-soft hover-shadow p-sm-4">
                <p class="small fw-semibold text-uppercase letter-spaced text-warning">Pendientes de llegada</p>
                <p class="mt-2 fs-3 fw-bold text-warning-emphasis">{{ $pendingInMexico }}</p>
                <p class="mt-1 small text-warning">Todas las fechas · Ver guías</p>
            </a>
        </div>

        <div class="mb-5 rounded-4 border border-secondary-subtle bg-white p-4">
                <p class="small fw-semibold text-uppercase letter-spaced text-secondary">Cumplimiento de llegada</p>
                <p class="mt-2 fs-3 fw-bold text-dark">{{ $arrivalPercentage }}%</p>
                <div class="mt-2 h-2 overflow-hidden rounded-pill bg-secondary-subtle">
                    <div class="h-100 rounded-pill bg-success" style="width: {{ $arrivalPercentage }}%"></div>
                </div>
        </div>

        <div class="overflow-x-auto rounded-4 border border-secondary-subtle bg-white shadow-sm">
            <div class="border-bottom border-secondary-subtle px-3 py-3">
                <h2 class="fw-semibold">Comparativo de guías</h2>
                <p class="mt-1 small text-secondary">
                    @if (auth()->user()->isPackageManager())
                        Consulta el color de cada guía. Selecciona una guía para administrarla.
                    @else
                        Consulta el color de cada guía.
                    @endif
                </p>
            </div>
            <table class="w-100 text-start small">
                <thead class="border-bottom border-secondary-subtle bg-light small text-uppercase letter-spaced text-secondary">
                    <tr>
                        <th class="px-3 py-2 fw-semibold">Guía principal</th>
                        <th class="px-3 py-2 fw-semibold">Recibida USA</th>
                        <th class="px-3 py-2 fw-semibold">Color</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($packages as $package)
                        <tr class="">
                            <td class="text-nowrap px-3 py-2 fw-semibold">
                                @auth
                                    @if (auth()->user()->isPackageManager())
                                        <a href="{{ route('reports.comparison.edit', [$package, 'return_to' => 'comparativo']) }}" class="d-inline-flex mw-100 align-items-center rounded-3 bg-primary-subtle px-2 py-2 text-primary hover-text-primary-emphasis">
                                            <span class="text-truncate">{{ $package->guia_principal }}</span>
                                        </a>
                                    @else
                                        {{ $package->guia_principal }}
                                    @endif
                                @else
                                    {{ $package->guia_principal }}
                                @endauth
                                @if ($package->guia_secundaria)
                                    <span class="mt-1 d-block px-1 small fw-normal text-secondary">{{ $package->guia_secundaria }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap px-3 py-2">{{ $package->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2">
                                <x-package-color :package="$package" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-secondary">No hay guías para el filtro seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>