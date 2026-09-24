<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar guía</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-6xl px-4 py-6">
        @include('components.navbar')

        <main class="mx-auto max-w-2xl">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">ADMINISTRACIÓN DE GUÍA</p>
                <h1 class="mt-2 text-2xl font-bold md:text-3xl">{{ $package->guia_principal }}</h1>
                <p class="mt-1 text-sm text-slate-500">Actualiza el estado o elimina esta guía.</p>
            </div>

            @include('components.flash-messages')

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Estado actual</p><p class="mt-1 text-sm font-semibold">{{ $statuses[$package->estado] ?? ucfirst($package->estado) }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Paquetes</p><p class="mt-1 text-sm font-semibold">{{ $package->total_paquetes }}</p></div>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Recibida USA</p><p class="mt-1 text-sm font-semibold">{{ $package->created_at?->format('d/m/Y H:i') }}</p></div>
                </div>

                <div class="mb-6 border-t border-slate-200 pt-5">
                    <div class="mb-3">
                        <h2 class="font-semibold">Fotografías de la guía</h2>
                        <p class="mt-1 text-sm text-slate-500">El administrador puede eliminar fotografías individuales.</p>
                    </div>
                    @if ($package->photos->isNotEmpty())
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($package->photos as $photo)
                                <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                    <img src="{{ $photo->display_url }}" alt="Fotografía de {{ $package->guia_principal }}" class="h-32 w-full object-cover">
                                    @if (auth()->user()->role === 'administrador')
                                        <form action="{{ route('reports.comparison.photo.destroy', $photo) }}" method="POST" class="p-2" onsubmit="return confirm('¿Eliminar esta fotografía? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Eliminar foto</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">Esta guía no tiene fotografías.</p>
                    @endif
                </div>

                <form action="{{ route('reports.comparison.update', $package) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="estado" class="mb-1.5 block text-sm font-medium">Nuevo estado</label>
                        <select id="estado" name="estado" class="w-full rounded-xl border border-slate-300 px-3 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                            @foreach ($statuses as $status => $label)
                                <option value="{{ $status }}" @selected($package->estado === $status)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route('reports.comparison') }}" class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Volver al comparativo</a>
                        <button type="submit" class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Guardar estado</button>
                    </div>
                </form>

                @if (auth()->user()->role === 'administrador')
                    <form action="{{ route('reports.comparison.destroy', $package) }}" method="POST" class="mt-6 border-t border-slate-200 pt-5" onsubmit="return confirm('¿Enviar esta guía a la papelera? Podrás conservarla para auditoría.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-700">Enviar guía a la papelera</button>
                    </form>
                @endif
            </section>
        </main>
    </div>
</body>
</html>