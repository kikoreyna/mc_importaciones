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
<body class="bg-light text-dark" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    @php
        $dot = [
            'registrada' => 'bg-success',
            'color_cambiado' => 'bg-primary',
            'actualizada' => 'bg-warning',
            'foto_agregada' => 'bg-info',
            'foto_eliminada' => 'bg-secondary',
            'eliminada' => 'bg-danger',
            'restaurada' => 'bg-success',
        ];
    @endphp
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <div class="mb-5 d-flex flex-wrap align-items-end justify-content-between gap-3">
            <div>
                <p class="small fw-semibold text-uppercase letter-spaced text-primary">Historial</p>
                <h1 class="mt-1 fs-4 fw-bold tracking-tight text-dark fs-3">Guía {{ $package->guia_principal }}</h1>
                <p class="mt-1 d-flex flex-wrap align-items-center gap-2 small text-secondary">
                    Color actual
                    <x-package-color :package="$package" />
                    @if ($package->trashed())
                        <span class="d-inline-flex align-items-center rounded-pill bg-danger-subtle px-2 py-1 small fw-semibold text-danger">Eliminada</span>
                    @endif
                </p>
            </div>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('packages.search') }}" class="btn btn-secondary d-inline-flex cursor-pointer align-items-center rounded-3 bg-secondary px-3 py-2 small fw-semibold text-white shadow-sm">
                Volver
            </a>
        </div>

        <div class="row g-3 g-4 row-cols-lg-3">
            <section class="rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm col-lg-1">
                <p class="border-bottom border-light pb-2 small fw-semibold text-uppercase letter-spaced text-secondary">Participantes</p>
                @if ($participants->isEmpty())
                    <p class="mt-3 small text-secondary">Sin usuarios registrados.</p>
                @else
                    <ul class="mt-3 stack-gap-3">
                        @foreach ($participants as $participant)
                            <li class="d-flex align-items-center gap-2">
                                <span class="d-flex h-9 w-9 flex-shrink-0 align-items-center justify-content-center rounded-pill bg-primary small fw-bold text-uppercase text-white">{{ mb_substr($participant['name'], 0, 1) }}</span>
                                <span class="min-w-0 lh-sm">
                                    <span class="d-block text-truncate small fw-semibold text-dark">{{ $participant['name'] }}</span>
                                    <span class="d-block small text-secondary">{{ $participant['role'] ?: 'Sin rol' }} · {{ $participant['count'] }} {{ $participant['count'] === 1 ? 'acción' : 'acciones' }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm col-lg-2">
                <p class="border-bottom border-light pb-2 small fw-semibold text-uppercase letter-spaced text-secondary">Línea de tiempo</p>

                @if ($logs->isEmpty())
                    <p class="mt-3 small text-secondary">Esta guía no tiene movimientos registrados.</p>
                @else
                    <ol class="mt-4 stack-gap-5 border-start border-2 border-secondary-subtle ps-5">
                        @foreach ($logs as $log)
                            <li class="position-relative">
                                <span class="position-absolute left-[-1.95rem] top-1 h-3.5 w-3.5 rounded-pill {{ $dot[$log->action] ?? 'bg-secondary' }}"></span>
                                <p class="small fw-semibold text-dark">{{ $log->description }}</p>
                                <p class="mt-1 small text-secondary">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                    · {{ $log->user_name ?: 'Sistema' }}@if ($log->user_role) ({{ \App\Models\User::roleLabels()[$log->user_role] ?? $log->user_role }})@endif
                                </p>

                                @if (! empty($log->changes) && $log->action === 'actualizada')
                                    <ul class="mt-2 stack-gap-1 rounded-3 bg-light p-2 small text-secondary">
                                        @foreach ($log->changes as $label => [$old, $new])
                                            <li><span class="fw-semibold text-body">{{ $label }}:</span> {{ $old ?? 'vacío' }} → {{ $new ?? 'vacío' }}</li>
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
