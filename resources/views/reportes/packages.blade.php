<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportería de paquetes</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <div class="mb-5 d-flex flex-wrap align-items-end justify-content-between gap-2">
            <div>
                <p class="small text-uppercase letter-spaced text-primary fw-semibold">REPORTERÍA</p>
                <h1 class="fs-4 fw-bold mt-2">Reporte de paquetes</h1>
            </div>
            <span class="rounded-3 bg-primary-subtle px-2 py-2 small fw-semibold text-primary">
                {{ $packages->count() }} resultados
            </span>
        </div>

        <form action="{{ route('reports.packages') }}" method="GET" class="mb-5 row g-3 row-cols-1 g-2 rounded-4 border border-secondary-subtle bg-white p-3 shadow-sm row-cols-md-2 row-cols-lg-5">
            <input name="guia_principal" value="{{ request('guia_principal') }}" placeholder="Buscar guía" class="form-control rounded-4 border border-secondary-subtle px-2 py-2 small">
            <select name="client_id" class="form-select rounded-4 border border-secondary-subtle px-2 py-2 small">
                <option value="">Todos los clientes</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->nombre }}</option>
                @endforeach
            </select>
            <select name="transportadora" class="form-select rounded-4 border border-secondary-subtle px-2 py-2 small">
                <option value="">Todas las transportadoras</option>
                @foreach ($transportadoras as $transportadora)
                    <option value="{{ $transportadora }}" @selected(request('transportadora') === $transportadora)>{{ $transportadora }}</option>
                @endforeach
            </select>
            <select name="color" class="form-select rounded-4 border border-secondary-subtle px-2 py-2 small">
                <option value="">Todos los colores</option>
                @foreach ($colores as $value => $label)
                    <option value="{{ $value }}" @selected(request('color') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="d-flex gap-2">
                <button class="btn btn-primary flex-fill rounded-4 bg-primary px-3 py-2 small fw-semibold text-white hover-bg-primary">Filtrar</button>
                <a href="{{ route('reports.packages') }}" class="rounded-4 border border-secondary-subtle px-3 py-2 small fw-semibold text-body hover-bg-light">Limpiar</a>
            </div>
        </form>

        <div class="overflow-x-auto rounded-4 border border-secondary-subtle bg-white shadow-sm">
            <table class="w-100 text-start small">
                <thead class="border-bottom border-secondary-subtle bg-light small text-uppercase letter-spaced text-secondary">
                    <tr>
                        <th class="px-3 py-2 fw-semibold">Número de guía</th>
                        <th class="px-3 py-2 fw-semibold">Fecha de recibido</th>
                        <th class="px-3 py-2 fw-semibold">Transportadora</th>
                        <th class="px-3 py-2 fw-semibold">Cliente</th>
                        <th class="px-3 py-2 fw-semibold">Secuencia de cajas</th>
                        <th class="px-3 py-2 fw-semibold">Color</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($packages as $package)
                        <tr class="">
                            <td class="text-nowrap px-3 py-2 fw-semibold">
                                @auth
                                    @if (auth()->user()->isPackageManager())
                                        <a href="{{ route('reports.comparison.edit', [$package, 'return_to' => 'reporteria']) }}" class="d-inline-flex mw-100 align-items-center rounded-3 bg-primary-subtle px-2 py-2 text-primary hover-text-primary-emphasis">
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
                            <td class="text-nowrap px-3 py-2">{{ $package->created_at?->format('d/m/Y H:i') ?: 'Sin fecha' }}</td>
                            <td class="px-3 py-2">
                                @if ($package->transportadora && $transportadoraWebs->has($package->transportadora))
                                    <a href="{{ $transportadoraWebs->get($package->transportadora) }}" target="_blank" rel="noopener noreferrer" class="text-primary hover-underline">
                                        {{ $package->transportadora }}
                                    </a>
                                @else
                                    {{ $package->transportadora ?: 'Sin transportadora' }}
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                {{ $package->client?->nombre ?: 'Sin cliente' }}
                                @if ($package->partner)
                                    <span class="mt-1 d-block small text-secondary">{{ $package->partner->nombre }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap px-3 py-2">
                                {{ $package->caja_numero && $package->total_cajas ? $package->caja_numero . ' de ' . $package->total_cajas : '1 de 1' }}
                                @if ($package->guia_master)
                                    <span class="mt-1 d-block small text-secondary">{{ $package->guia_master }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <x-package-color :package="$package" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-4 text-center text-secondary">No hay paquetes que coincidan con los filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
