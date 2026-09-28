<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportadoras</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="w-full px-4 py-6">
        @include('components.navbar')

        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">ADMINISTRACIÓN</p>
                <h1 class="text-2xl font-bold mt-2">Transportadoras</h1>
            </div>
            <a href="{{ route('transportadoras.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Nueva transportadora</a>
        </div>

        @include('components.flash-messages')

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="divide-y divide-slate-100">
                @forelse ($transportadoras as $transportadora)
                    <div class="flex flex-wrap items-center justify-between gap-4 p-4">
                        <div>
                            <p class="font-semibold">{{ $transportadora->nombre }}</p>
                            <p class="text-sm text-slate-500">
                                <a href="{{ $transportadora->web }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">{{ $transportadora->web }}</a>
                                · {{ $transportadora->telefono ?: 'Sin teléfono' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('transportadoras.edit', $transportadora) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold hover:bg-slate-50">Editar</a>
                            <form action="{{ route('transportadoras.destroy', $transportadora) }}" method="POST" onsubmit="return confirm('¿Eliminar esta transportadora?');">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-500">No hay transportadoras registradas.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
