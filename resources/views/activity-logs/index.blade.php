<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de cambios</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="w-full px-4 py-6">
        @include('components.navbar')

        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">ADMINISTRACIÓN</p>
            <h1 class="mt-2 text-2xl font-bold">Historial de cambios</h1>
            <p class="mt-1 text-sm text-slate-500">Registro de modificaciones en clientes, socios, usuarios y transportadoras.</p>
        </div>

        <form action="{{ route('activity-logs.index') }}" method="GET" class="mb-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row">
            <select name="entity" aria-label="Filtrar por entidad" class="flex-1 rounded-xl border border-slate-300 px-3 py-3 text-sm">
                <option value="">Todas las entidades</option>
                @foreach (['client' => 'Clientes', 'partner' => 'Socios', 'user' => 'Usuarios', 'transportadora' => 'Transportadoras'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['entity'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="action" aria-label="Filtrar por acción" class="flex-1 rounded-xl border border-slate-300 px-3 py-3 text-sm">
                <option value="">Todas las acciones</option>
                @foreach ($actionLabels as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['action'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Filtrar</button>
            <a href="{{ route('activity-logs.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Limpiar</a>
        </form>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Fecha</th>
                        <th class="px-4 py-3 font-semibold">Usuario</th>
                        <th class="px-4 py-3 font-semibold">Acción</th>
                        <th class="px-4 py-3 font-semibold">Registro</th>
                        <th class="px-4 py-3 font-semibold">Cambios</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="align-top">
                            <td class="whitespace-nowrap px-4 py-3">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3">
                                <span class="font-medium">{{ $log->actor_name ?: 'Sistema' }}</span>
                                @if ($log->actor_role)
                                    <span class="block text-xs text-slate-500">{{ \App\Models\User::roleLabels()[$log->actor_role] ?? $log->actor_role }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $actionLabels[$log->action] ?? $log->action }}</td>
                            <td class="px-4 py-3">
                                {{ $entityLabels[$log->subject_type] ?? 'Registro' }} #{{ $log->subject_id }}
                                @php
                                    $subjectName = null;
                                    foreach (['nombre', 'name'] as $nameField) {
                                        $nameChange = $log->changes[$nameField] ?? null;
                                        if (is_array($nameChange)) {
                                            $subjectName = $nameChange[1] ?? $nameChange[0] ?? null;
                                            break;
                                        }
                                    }
                                @endphp
                                @if ($subjectName)
                                    <span class="block text-xs text-slate-500">{{ $subjectName }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($log->changes)
                                    <dl class="space-y-2">
                                        @foreach ($log->changes as $field => $values)
                                            <div>
                                                <dt class="font-medium">{{ str_replace('_', ' ', ucfirst($field)) }}</dt>
                                                @if (is_array($values) && count($values) === 2)
                                                    <dd class="text-slate-600">{{ $values[0] ?? '—' }} <span aria-label="cambió a">→</span> {{ $values[1] ?? '—' }}</dd>
                                                @else
                                                    <dd class="text-slate-600">{{ is_array($values) ? json_encode($values, JSON_UNESCAPED_UNICODE) : ($values ?? '—') }}</dd>
                                                @endif
                                            </div>
                                        @endforeach
                                    </dl>
                                @else
                                    <span class="text-slate-400">Sin detalle</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No hay cambios registrados para estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</body>
</html>
