<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparativo USA - MEX</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-6xl px-4 py-6">
        @include('components.navbar')

        <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">REPORTERÍA</p>
                <h1 class="mt-2 text-2xl font-bold md:text-3xl">Comparativo USA - Bodega MEX</h1>
                <p class="mt-1 text-sm text-slate-500">Control de guías recibidas en origen y confirmadas en destino.</p>
            </div>
            <a href="{{ route('reports.comparison', ['fecha' => $fecha, 'grupo' => 'mex']) }}" class="rounded-lg bg-blue-100 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-200">
                {{ $arrivalPercentage }}% recibidas en MEX · Ver guías
            </a>
        </div>

        <form action="{{ route('reports.comparison') }}" method="GET" class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="fecha" class="mb-1.5 block text-sm font-medium">Fecha de recepción USA</label>
                <input id="fecha" name="fecha" type="date" value="{{ $fecha }}" class="w-full rounded-xl border border-slate-300 px-3 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            </div>
            <button class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700" type="submit">Filtrar</button>
            <a href="{{ route('reports.comparison', ['fecha' => today()->toDateString()]) }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Hoy</a>
        </form>

        @include('components.flash-messages')

        @if ($grupo)
            <div class="mb-6 flex items-center justify-between gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                <span>Mostrando: <strong>{{ ['usa' => 'Recibidas en USA', 'mex' => 'Recibidas en Bodega MEX', 'pendientes' => 'Pendientes de llegada'][$grupo] }}</strong></span>
                <a href="{{ route('reports.comparison', ['fecha' => $fecha]) }}" class="font-semibold underline">Ver todas</a>
            </div>
        @endif

        <div class="mb-4 grid grid-cols-3 gap-2 sm:gap-4">
            <a href="{{ route('reports.comparison', ['fecha' => $fecha, 'grupo' => 'usa']) }}" class="min-w-0 rounded-2xl border border-blue-200 bg-blue-50 p-3 transition hover:border-blue-400 hover:shadow-sm sm:p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Recibidas en USA</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $receivedInUsa }}</p>
                <p class="mt-1 text-sm text-blue-700">{{ $totalPackages }} paquetes · Ver guías</p>
            </a>
            <a href="{{ route('reports.comparison', ['fecha' => $fecha, 'grupo' => 'mex']) }}" class="min-w-0 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 transition hover:border-emerald-400 hover:shadow-sm sm:p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Recibidas en Bodega MEX</p>
                <p class="mt-2 text-3xl font-bold text-emerald-950">{{ $receivedInMexico }}</p>
                <p class="mt-1 text-sm text-emerald-700">{{ $arrivedPackages }} paquetes · Ver guías</p>
            </a>
            <a href="{{ route('reports.comparison', ['grupo' => 'pendientes']) }}" class="min-w-0 rounded-2xl border border-amber-200 bg-amber-50 p-3 transition hover:border-amber-400 hover:shadow-sm sm:p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Pendientes de llegada</p>
                <p class="mt-2 text-3xl font-bold text-amber-950">{{ $pendingInMexico }}</p>
                <p class="mt-1 text-sm text-amber-700">Todas las fechas · Ver guías</p>
            </a>
        </div>

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cumplimiento de llegada</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $arrivalPercentage }}%</p>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $arrivalPercentage }}%"></div>
                </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-4">
                <h2 class="font-semibold">Comparativo de guías</h2>
                <p class="mt-1 text-sm text-slate-500">Consulta el estado de cada guía y administra devoluciones o correcciones autorizadas.</p>
            </div>
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Guía principal</th>
                        <th class="px-4 py-3 font-semibold">Guía secundaria</th>
                        <th class="px-4 py-3 font-semibold">Paquetes</th>
                        <th class="px-4 py-3 font-semibold">Recibida USA</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        @auth
                            @if (auth()->user()->isPackageManager())
                                <th class="px-4 py-3 font-semibold">Acciones</th>
                            @endif
                        @endauth
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($packages as $package)
                        <tr class="hover:bg-blue-50">
                            <td class="whitespace-nowrap px-4 py-3 font-semibold">{{ $package->guia_principal }}</td>
                            <td class="px-4 py-3">{{ $package->guia_secundaria ?: 'Sin guía secundaria' }}</td>
                            <td class="px-4 py-3">{{ $package->total_paquetes }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $package->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $statuses[$package->estado] ?? ucfirst($package->estado) }}</span>
                            </td>
                            @auth
                                @if (auth()->user()->isPackageManager())
                                    <td class="min-w-70 px-4 py-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <form action="{{ route('reports.comparison.update', $package) }}" method="POST" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <label for="estado-{{ $package->id }}" class="sr-only">Estado de {{ $package->guia_principal }}</label>
                                                <select id="estado-{{ $package->id }}" name="estado" class="rounded-lg border border-slate-300 px-2 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                                                    @foreach ($statuses as $status => $label)
                                                        <option value="{{ $status }}" @selected($package->estado === $status)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Guardar</button>
                                            </form>
                                            <form action="{{ route('reports.comparison.destroy', $package) }}" method="POST" onsubmit="return confirm('¿Eliminar esta guía? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            @endauth
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->check() && auth()->user()->isPackageManager() ? 6 : 5 }}" class="px-4 py-8 text-center text-slate-500">No hay guías para el filtro seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>