<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de guía</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 antialiased" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    @php
        $dot = [
            'registrada' => 'bg-green-500',
            'estado_cambiado' => 'bg-blue-600',
            'actualizada' => 'bg-amber-500',
            'foto_agregada' => 'bg-sky-500',
            'foto_eliminada' => 'bg-slate-500',
            'eliminada' => 'bg-red-600',
            'restaurada' => 'bg-emerald-600',
        ];
    @endphp
    <div class="w-full px-4 py-6">
        @include('components.navbar')

        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Historial</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">Guía {{ $package->guia_principal }}</h1>
                <p class="mt-1 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                    Estado actual
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold capitalize text-green-800">{{ str_replace('_', ' ', $package->estado) }}</span>
                    @if ($package->trashed())
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">Eliminada</span>
                    @endif
                </p>
            </div>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('packages.search') }}" class="inline-flex cursor-pointer items-center rounded-lg bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                Volver
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-1">
                <p class="border-b border-slate-100 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Participantes</p>
                @if ($participants->isEmpty())
                    <p class="mt-4 text-sm text-slate-500">Sin usuarios registrados.</p>
                @else
                    <ul class="mt-4 space-y-3">
                        @foreach ($participants as $participant)
                            <li class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold uppercase text-white">{{ mb_substr($participant['name'], 0, 1) }}</span>
                                <span class="min-w-0 leading-tight">
                                    <span class="block truncate text-sm font-semibold text-slate-900">{{ $participant['name'] }}</span>
                                    <span class="block text-xs text-slate-500">{{ $participant['role'] ?: 'Sin rol' }} · {{ $participant['count'] }} {{ $participant['count'] === 1 ? 'acción' : 'acciones' }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <p class="border-b border-slate-100 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Línea de tiempo</p>

                @if ($logs->isEmpty())
                    <p class="mt-4 text-sm text-slate-500">Esta guía no tiene movimientos registrados.</p>
                @else
                    <ol class="mt-5 space-y-6 border-l-2 border-slate-200 pl-6">
                        @foreach ($logs as $log)
                            <li class="relative">
                                <span class="absolute -left-[1.95rem] top-1 h-3.5 w-3.5 rounded-full ring-4 ring-white {{ $dot[$log->action] ?? 'bg-slate-400' }}"></span>
                                <p class="text-sm font-semibold text-slate-900">{{ $log->description }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                    · {{ $log->user_name ?: 'Sistema' }}@if ($log->user_role) ({{ \App\Models\User::roleLabels()[$log->user_role] ?? $log->user_role }})@endif
                                </p>

                                @if (! empty($log->changes) && $log->action === 'actualizada')
                                    <ul class="mt-2 space-y-1 rounded-lg bg-slate-50 p-3 text-xs text-slate-600">
                                        @foreach ($log->changes as $label => [$old, $new])
                                            <li><span class="font-semibold text-slate-700">{{ $label }}:</span> {{ $old ?? 'vacío' }} → {{ $new ?? 'vacío' }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>
        </div>
    </div>
</body>
</html>
