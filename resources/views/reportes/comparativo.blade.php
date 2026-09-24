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
            <span class="rounded-lg bg-blue-100 px-3 py-2 text-sm font-semibold text-blue-700">
                {{ $arrivalPercentage }}% recibidas en MEX
            </span>
        </div>

        <form action="{{ route('reports.comparison') }}" method="GET" class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="fecha" class="mb-1.5 block text-sm font-medium">Fecha de recepción USA</label>
                <input id="fecha" name="fecha" type="date" value="{{ $fecha }}" class="w-full rounded-xl border border-slate-300 px-3 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            </div>
            <button class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700" type="submit">Filtrar</button>
            <a href="{{ route('reports.comparison') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Todas las fechas</a>
        </form>

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Recibidas en USA</p>
                <p class="mt-2 text-3xl font-bold text-blue-950">{{ $receivedInUsa }}</p>
                <p class="mt-1 text-sm text-blue-700">{{ $totalPackages }} paquetes</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Recibidas en Bodega MEX</p>
                <p class="mt-2 text-3xl font-bold text-emerald-950">{{ $receivedInMexico }}</p>
                <p class="mt-1 text-sm text-emerald-700">{{ $arrivedPackages }} paquetes</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Pendientes de llegada</p>
                <p class="mt-2 text-3xl font-bold text-amber-950">{{ $pendingInMexico }}</p>
                <p class="mt-1 text-sm text-amber-700">Guías aún en USA</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cumplimiento de llegada</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $arrivalPercentage }}%</p>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $arrivalPercentage }}%"></div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-4 py-4">
                <h2 class="font-semibold">Guías pendientes de recibir en Bodega MEX</h2>
                <p class="mt-1 text-sm text-slate-500">Estas guías fueron registradas en USA, pero aún no tienen ingreso confirmado en Bodega.</p>
            </div>
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Guía principal</th>
                        <th class="px-4 py-3 font-semibold">Guía secundaria</th>
                        <th class="px-4 py-3 font-semibold">Paquetes</th>
                        <th class="px-4 py-3 font-semibold">Recibida USA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pendingGuides as $package)
                        <tr class="hover:bg-amber-50">
                            <td class="whitespace-nowrap px-4 py-3 font-semibold">{{ $package->guia_principal }}</td>
                            <td class="px-4 py-3">{{ $package->guia_secundaria ?: 'Sin guía secundaria' }}</td>
                            <td class="px-4 py-3">{{ $package->total_paquetes }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $package->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-emerald-700">Todas las guías del filtro tienen ingreso confirmado en Bodega MEX.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>