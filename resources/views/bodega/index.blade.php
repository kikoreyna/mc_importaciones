<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción en bodega</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="max-w-6xl mx-auto px-4 py-6">
        @include('components.navbar')
    </div>

    <div class="max-w-md mx-auto px-4 py-6">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">BODEGA</p>
            <h1 class="text-2xl font-bold mt-2">Recepción</h1>
        </div>

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

            <div>
                <label for="guia_principal" class="block text-sm font-medium mb-1.5">Escanee la guía</label>
                <input id="guia_principal" name="guia_principal" type="text" required class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Guía principal">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white px-5 py-3.5 rounded-xl font-semibold hover:bg-blue-700 active:bg-blue-800">
                Validar guía
            </button>
        </form>
    </div>
</body>
</html>
