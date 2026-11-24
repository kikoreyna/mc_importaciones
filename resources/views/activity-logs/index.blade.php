<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de cambios</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <div class="mb-5">
            <p class="small fw-semibold text-uppercase letter-spaced text-primary">ADMINISTRACIÓN</p>
            <h1 class="mt-2 fs-4 fw-bold">Historial de cambios</h1>
            <p class="mt-1 small text-secondary">Registro de modificaciones en clientes, socios, usuarios y transportadoras.</p>
        </div>

        <form action="{{ route('activity-logs.index') }}" method="GET" class="mb-5 d-flex flex-column gap-2 rounded-4 border border-secondary-subtle bg-white p-3 shadow-sm flex-sm-row">
            <select name="entity" aria-label="Filtrar por entidad" class="form-select flex-fill rounded-4 border border-secondary-subtle px-2 py-2 small">
                <option value="">Todas las entidades</option>
                @foreach (['client' => 'Clientes', 'partner' => 'Socios', 'user' => 'Usuarios', 'transportadora' => 'Transportadoras'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['entity'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="action" aria-label="Filtrar por acción" class="form-select flex-fill rounded-4 border border-secondary-subtle px-2 py-2 small">
                <option value="">Todas las acciones</option>
                @foreach ($actionLabels as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['action'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary rounded-4 bg-primary px-4 py-2 small fw-semibold text-white hover-bg-primary">Filtrar</button>
            <a href="{{ route('activity-logs.index') }}" class="rounded-4 border border-secondary-subtle px-4 py-2 text-center small fw-semibold text-body hover-bg-light">Limpiar</a>
        </form>

        <div class="overflow-x-auto rounded-4 border border-secondary-subtle bg-white shadow-sm">
            <table class="w-100 text-start small">
                <thead class="border-bottom border-secondary-subtle bg-light small text-uppercase letter-spaced text-secondary">
                    <tr>
                        <th class="px-3 py-2 fw-semibold">Fecha</th>
                        <th class="px-3 py-2 fw-semibold">Usuario</th>
                        <th class="px-3 py-2 fw-semibold">Acción</th>
                        <th class="px-3 py-2 fw-semibold">Registro</th>
                        <th class="px-3 py-2 fw-semibold">Cambios</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr class="align-top">
                            <td class="text-nowrap px-3 py-2">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                            <td class="px-3 py-2">
                                <span class="fw-medium">{{ $log->actor_name ?: 'Sistema' }}</span>
                                @if ($log->actor_role)
                                    <span class="d-block small text-secondary">{{ \App\Models\User::roleLabels()[$log->actor_role] ?? $log->actor_role }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">{{ $actionLabels[$log->action] ?? $log->action }}</td>
                            <td class="px-3 py-2">
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
                                    <span class="d-block small text-secondary">{{ $subjectName }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if ($log->changes)
                                    <dl class="stack-gap-2">
                                        @foreach ($log->changes as $field => $values)
                                            <div>
                                                <dt class="fw-medium">{{ str_replace('_', ' ', ucfirst($field)) }}</dt>
                                                @if (is_array($values) && count($values) === 2)
                                                    <dd class="text-secondary">{{ $values[0] ?? '—' }} <span aria-label="cambió a">→</span> {{ $values[1] ?? '—' }}</dd>
                                                @else
                                                    <dd class="text-secondary">{{ is_array($values) ? json_encode($values, JSON_UNESCAPED_UNICODE) : ($values ?? '—') }}</dd>
                                                @endif
                                            </div>
                                        @endforeach
                                    </dl>
                                @else
                                    <span class="text-secondary">Sin detalle</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-4 text-center text-secondary">No hay cambios registrados para estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $logs->links() }}</div>
    </div>
</body>
</html>
