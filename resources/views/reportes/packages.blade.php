<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportería de paquetes</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="w-full px-4 py-6">
        @include('components.navbar')

        <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">REPORTERÍA</p>
                <h1 class="text-2xl font-bold mt-2">Reporte de paquetes</h1>
            </div>
            <span class="rounded-lg bg-blue-100 px-3 py-2 text-sm font-semibold text-blue-700">
                {{ $packages->count() }} resultados
            </span>
        </div>

        <form action="{{ route('reports.packages') }}" method="GET" class="mb-6 grid grid-cols-1 gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-2 lg:grid-cols-5">
            <input name="guia_principal" value="{{ request('guia_principal') }}" placeholder="Buscar guía" class="rounded-xl border border-slate-300 px-3 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            <select name="client_id" class="rounded-xl border border-slate-300 px-3 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los clientes</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->nombre }}</option>
                @endforeach
            </select>
            <select name="transportadora" class="rounded-xl border border-slate-300 px-3 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las transportadoras</option>
                @foreach ($transportadoras as $transportadora)
                    <option value="{{ $transportadora }}" @selected(request('transportadora') === $transportadora)>{{ $transportadora }}</option>
                @endforeach
            </select>
            <select name="estado" class="rounded-xl border border-slate-300 px-3 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ ucfirst($estado) }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Filtrar</button>
                <a href="{{ route('reports.packages') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Limpiar</a>
            </div>
        </form>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Número de guía</th>
                        <th class="px-4 py-3 font-semibold">Guía secundaria</th>
                        <th class="px-4 py-3 font-semibold">Secuencia de cajas</th>
                        <th class="px-4 py-3 font-semibold">Transportadora</th>
                        <th class="px-4 py-3 font-semibold">Socio</th>
                        <th class="px-4 py-3 font-semibold">Cliente</th>
                        <th class="px-4 py-3 font-semibold">Fecha de recibido</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($packages as $package)
                        <tr class="hover:bg-blue-50">
                            <td class="whitespace-nowrap px-4 py-3 font-semibold">
                                @auth
                                    @if (auth()->user()->isPackageManager())
                                        <a href="{{ route('reports.comparison.edit', [$package, 'return_to' => 'reporteria']) }}" class="inline-flex max-w-full items-center rounded-lg bg-blue-50 px-3 py-2 text-blue-700 hover:bg-blue-100 hover:text-blue-900">
                                            <span class="truncate">{{ $package->guia_principal }}</span>
                                        </a>
                                    @else
                                        {{ $package->guia_principal }}
                                    @endif
                                @else
                                    {{ $package->guia_principal }}
                                @endauth
                            </td>
                            <td class="px-4 py-3">{{ $package->guia_secundaria ?: 'Sin guía secundaria' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $package->caja_numero && $package->total_cajas ? $package->caja_numero . ' de ' . $package->total_cajas : 'Sin secuencia' }}</td>
                            <td class="px-4 py-3">
                                @if ($package->transportadora && $transportadoraWebs->has($package->transportadora))
                                    <a href="{{ $transportadoraWebs->get($package->transportadora) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">
                                        {{ $package->transportadora }}
                                    </a>
                                @else
                                    {{ $package->transportadora ?: 'Sin transportadora' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $package->partner?->nombre ?: 'Sin socio' }}</td>
                            <td class="px-4 py-3">{{ $package->client?->nombre ?: 'Sin cliente' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $package->created_at?->format('d/m/Y H:i') ?: 'Sin fecha' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No hay paquetes que coincidan con los filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
