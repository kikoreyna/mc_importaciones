<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción en bodega</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="w-full px-4 py-6">
        @include('components.navbar')
    </div>

    <div class="w-full max-w-2xl mx-auto px-4 py-6">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">BODEGA</p>
            <h1 class="text-2xl font-bold mt-2 flex items-center gap-3">
                Recepción
                <span id="pending-counter" class="rounded-full px-3 py-1 text-sm font-semibold" style="background-color: #facc15; color: #422006" title="Guías por recibir">{{ $pendingCount }}</span>
            </h1>
        </div>

        @if (session('warning'))
            <div class="bg-amber-100 border border-amber-300 text-amber-900 px-4 py-3 rounded-xl mb-5 text-sm font-medium">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl mb-5 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl mb-5 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('bodega.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 space-y-4">
            @csrf

            <x-camera-scanner
                id="warehouse-guide-scanner"
                name="guia_principal"
                label="Escanee la guía"
                placeholder="Guía principal"
            />

            <button type="submit" class="w-full bg-blue-600 text-white px-5 py-3.5 rounded-xl font-semibold hover:bg-blue-700 active:bg-blue-800">
                Validar guía
            </button>
        </form>
    </div>
</body>
</html>
