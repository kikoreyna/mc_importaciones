<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paquetes registrados</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="max-w-4xl mx-auto px-4 py-6 md:px-6">
        @include('components.navbar')

        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">RECEPCIÓN</p>
                <h1 class="text-2xl font-bold mt-2 md:text-3xl">Paquetes registrados</h1>
            </div>
            <a href="{{ route('packages.index') }}" class="shrink-0 text-sm font-semibold text-blue-700 hover:text-blue-800 hover:underline">
                Registrar guía
            </a>
        </div>

        @if ($packages->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-sm text-slate-600">
                No hay paquetes registrados aún.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($packages as $package)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                        <p class="text-sm"><span class="font-semibold">Guía principal:</span> {{ $package->guia_principal }}</p>
                        <p class="text-sm mt-1"><span class="font-semibold">Guía secundaria:</span> {{ $package->guia_secundaria ?? 'N/A' }}</p>
                        <p class="text-sm mt-1"><span class="font-semibold">Estado:</span> {{ $package->estado }}</p>

                        @if ($package->photos->isNotEmpty())
                            <div class="mt-3 grid grid-cols-3 gap-2">
                                @foreach ($package->photos as $photo)
                                    <img src="{{ $photo->display_url }}" alt="Foto del paquete" class="w-full h-24 object-cover rounded-lg border border-slate-200">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
