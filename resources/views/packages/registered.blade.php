<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paquetes registrados</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 antialiased" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    <div class="w-full px-4 py-6">
        @include('components.navbar')

        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Recepción</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">Paquetes registrados</h1>
                <p class="mt-1 text-sm text-slate-500">Registros de hoy · {{ $packages->count() }} {{ $packages->count() === 1 ? 'paquete' : 'paquetes' }}</p>
            </div>
            <a href="{{ route('packages.index') }}" class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                <span class="text-lg leading-none">+</span> Registrar guía
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        @if ($packages->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-sm text-slate-600">
                No hay paquetes registrados aún.
            </div>
        @else
            <div class="grid gap-4 lg:grid-cols-2 2xl:grid-cols-3">
                @foreach ($packages as $package)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 sm:grid-cols-3">
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Guía principal</dt>
                                <dd class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $package->guia_principal }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Guía secundaria</dt>
                                <dd class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $package->guia_secundaria ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Estado</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold capitalize text-green-800">{{ str_replace('_', ' ', $package->estado) }}</span>
                                </dd>
                            </div>
                        </dl>

                        @if ($package->photos->isNotEmpty())
                            <div class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-4">
                                @foreach ($package->photos as $photo)
                                    <button type="button" data-lightbox="{{ $photo->display_url }}" class="aspect-square cursor-pointer overflow-hidden rounded-lg border border-slate-200 bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <img src="{{ $photo->display_url }}" alt="Foto del paquete" loading="lazy" class="h-full w-full object-cover transition hover:scale-105">
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('packages.photos.store', $package) }}" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                            @csrf
                            <input type="file" name="photos[]" multiple accept="image/*" required class="min-w-0 flex-1 text-sm text-slate-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border file:border-slate-300 file:bg-slate-200 file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-800 hover:file:bg-slate-300">
                            <button type="submit" class="cursor-pointer rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Subir fotos</button>
                        </form>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    <div id="lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/80 p-4" role="dialog" aria-modal="true">
        <button type="button" id="lightbox-close" class="absolute right-4 top-4 cursor-pointer rounded-full bg-white/90 px-3 py-1 text-lg font-bold text-slate-900 hover:bg-white" aria-label="Cerrar">&times;</button>
        <img id="lightbox-img" src="" alt="Foto ampliada" class="max-h-full max-w-full rounded-lg object-contain shadow-xl">
    </div>

    <script>
        (() => {
            const box = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            const close = () => { box.classList.add('hidden'); box.classList.remove('flex'); img.src = ''; };
            document.querySelectorAll('[data-lightbox]').forEach((el) => el.addEventListener('click', () => {
                img.src = el.dataset.lightbox;
                box.classList.remove('hidden');
                box.classList.add('flex');
            }));
            box.addEventListener('click', (e) => { if (e.target !== img) close(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        })();
    </script>
</body>
</html>
