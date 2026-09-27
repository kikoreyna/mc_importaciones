<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $transportadora->exists ? 'Editar transportadora' : 'Nueva transportadora' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="w-full px-4 py-6">
        @include('components.navbar')
    </div>

    <div class="w-full max-w-xl mx-auto px-4 pb-6">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">ADMINISTRACIÓN</p>
            <h1 class="text-2xl font-bold mt-2">{{ $transportadora->exists ? 'Editar transportadora' : 'Nueva transportadora' }}</h1>
        </div>

        @include('components.flash-messages')

        <form action="{{ $transportadora->exists ? route('transportadoras.update', $transportadora) : route('transportadoras.store') }}" method="POST" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            @csrf
            @if ($transportadora->exists)
                @method('PUT')
            @endif

            <div>
                <label for="nombre" class="mb-1.5 block text-sm font-medium">Nombre de la transportadora</label>
                <input id="nombre" name="nombre" required value="{{ old('nombre', $transportadora->nombre) }}" class="w-full rounded-xl border border-slate-300 px-3 py-3">
            </div>
            <div>
                <label for="web" class="mb-1.5 block text-sm font-medium">Sitio web</label>
                <input id="web" name="web" type="url" required value="{{ old('web', $transportadora->web) }}" placeholder="https://ejemplo.com" class="w-full rounded-xl border border-slate-300 px-3 py-3">
            </div>
            <div>
                <label for="telefono" class="mb-1.5 block text-sm font-medium">Teléfono <span class="font-normal text-slate-500">(opcional)</span></label>
                <input id="telefono" name="telefono" type="tel" value="{{ old('telefono', $transportadora->telefono) }}" class="w-full rounded-xl border border-slate-300 px-3 py-3">
            </div>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('transportadoras.index') }}" class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-semibold">Cancelar</a>
                <button class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Guardar</button>
            </div>
        </form>
    </div>
</body>
</html>
