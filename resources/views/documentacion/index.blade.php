<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 antialiased" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    <div class="w-full px-4 py-6">
        @include('components.navbar')

        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Documentación</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">Edición de guía</h1>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl mb-5 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl mb-5 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6">
            @if ($package)
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        @if ($posicion > 1)
                            <a id="previous-package" href="{{ route('documentacion.index', ['posicion' => $posicion - 1]) }}" class="rounded-xl border border-slate-500 bg-transparent px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100">&lsaquo; Anterior</a>
                        @else
                            <span class="cursor-not-allowed rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-300">&lsaquo; Anterior</span>
                        @endif

                        @if ($posicion < $total)
                            <a id="next-package" href="{{ route('documentacion.index', ['posicion' => $posicion + 1]) }}" class="rounded-xl border border-slate-500 bg-transparent px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100">Siguiente &rsaquo;</a>
                        @else
                            <span class="cursor-not-allowed rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-300">Siguiente &rsaquo;</span>
                        @endif
                    </div>
                    <span id="package-position" class="w-fit rounded-full px-4 py-1.5 text-sm font-semibold {{ $package->documented_at ? '' : 'bg-amber-100 text-amber-800' }}" @if ($package->documented_at) style="background-color: #dcfce7; color: #166534" @endif>{{ $package->documented_at ? 'Documentada' : $posicion . '/' . $total . ' sin documentar' }}</span>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <div class="min-w-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-6 lg:col-span-4">
                        <div class="mb-4 pt-1 border-b border-slate-200 pb-3">
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Edición</p>
                        </div>

                        <form id="documentation-form" action="{{ route('documentacion.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="guia_principal" value="{{ $package->guia_principal }}">
                            <input type="hidden" name="posicion" value="{{ $posicion }}">

                            <div>
                                <label class="block text-sm font-medium mb-1.5">Guía principal</label>
                                <div id="edicion-guia-principal" tabindex="0" class="w-full border border-slate-200 bg-slate-50 rounded-xl px-3 py-3 text-base font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    {{ $package->guia_principal }}
                                </div>
                            </div>

                            <div>
                                <label for="guia_secundaria" class="block text-sm font-medium mb-1.5">Guía secundaria</label>
                                <input id="guia_secundaria" name="guia_secundaria" type="text" value="{{ old('guia_secundaria', $package->guia_secundaria ?? '') }}" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Opcional">
                            </div>

                            <div>
                                <label for="client_search" class="block text-sm font-medium mb-1.5">Cliente</label>
                                @php
                                    $selectedClientId = old('client_id', $package->client_id ?? '');
                                    $selectedClient = $clients->firstWhere('id', $selectedClientId);
                                    $selectedClientLabel = $selectedClient
                                        ? $selectedClient->nombre . ($selectedClient->alias ? ' | ' . $selectedClient->alias : '')
                                        : old('client_search', '');
                                @endphp
                                <div class="relative">
                                    <input id="client_search" name="client_search" type="text" value="{{ $selectedClientLabel }}" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Escribe nombre, alias o código" autocomplete="off">
                                    <div id="clients_list" class="absolute z-20 mt-1 hidden max-h-56 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg"></div>
                                </div>
                                <input id="client_id" name="client_id" type="hidden" value="{{ $selectedClientId }}">
                                <div id="clients_data" class="hidden">
                                    @foreach ($clients as $client)
                                        <button type="button" data-id="{{ $client->id }}" data-partner-id="{{ $client->partner_id ?? '' }}" data-label="{{ $client->nombre }}{{ $client->alias ? ' | ' . $client->alias : '' }}" data-search="{{ strtolower($client->nombre . ' ' . ($client->alias ?? '')) }}">{{ $client->nombre }}{{ $client->alias ? ' | ' . $client->alias : '' }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label for="partner_id" class="block text-sm font-medium mb-1.5">Socio</label>
                                <select id="partner_selector" name="partner_selector" {{ $selectedClient?->partner_id ? 'disabled' : '' }} class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-slate-100 disabled:text-slate-500">
                                    <option value="">Selecciona un socio</option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}" {{ old('partner_id', $package->partner_id) == $partner->id ? 'selected' : '' }}>
                                            {{ $partner->alias ? $partner->alias . ' | ' : '' }}{{ $partner->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <input id="partner_id" name="partner_id" type="hidden" value="{{ old('partner_id', $package->partner_id) }}">
                            </div>

                            <div>
                                <label for="transportadora" class="block text-sm font-medium mb-1.5">Transportadora</label>
                                @php
                                    $selectedTransportadora = old('transportadora', $package->transportadora ?? '');
                                @endphp
                                <select id="transportadora" name="transportadora" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Selecciona una transportadora</option>
                                    @if ($selectedTransportadora && ! $transportadoras->contains('nombre', $selectedTransportadora))
                                        <option value="{{ $selectedTransportadora }}" selected>{{ $selectedTransportadora }} (actual)</option>
                                    @endif
                                    @foreach ($transportadoras as $transportadora)
                                        <option value="{{ $transportadora->nombre }}" {{ $selectedTransportadora === $transportadora->nombre ? 'selected' : '' }}>
                                            {{ $transportadora->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="guia_master" class="block text-sm font-medium mb-1.5">Guía máster</label>
                                <input id="guia_master" name="guia_master" type="text" value="{{ old('guia_master', $package->guia_master ?? '') }}" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Opcional">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="caja_numero" class="block text-sm font-medium mb-1.5">Caja #</label>
                                    <input id="caja_numero" name="caja_numero" type="number" min="1" value="{{ old('caja_numero', $package->caja_numero ?? '') }}" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="1">
                                </div>
                                <div>
                                    <label for="total_cajas" class="block text-sm font-medium mb-1.5">Total de cajas</label>
                                    <input id="total_cajas" name="total_cajas" type="number" min="1" value="{{ old('total_cajas', $package->total_cajas ?? '') }}" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="5">
                                </div>
                            </div>

                            <div class="pt-5 mt-3 border-t-2 border-slate-200">
                                <button id="actualizar-guia" type="submit" class="block w-fit mx-auto cursor-pointer bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold text-base shadow-sm hover:bg-blue-700 active:bg-blue-800">
                                    Actualizar Guia
                                </button>
                            </div>
                        </form>
                    </div>

                    @if ($package->photos->isNotEmpty())
                        <div class="min-w-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-6 lg:col-span-8">
                            @php
                                $photos = collect($package->photos->all());
                                $firstPhoto = $photos->first();
                            @endphp

                            @if ($firstPhoto)
                                <div id="photo-stage" class="relative aspect-4/3 w-full animate-pulse overflow-hidden rounded-xl border border-slate-200 bg-slate-200">
                                    <img id="package-photo" src="{{ $firstPhoto->display_url }}" alt="Foto de paquete" class="h-full w-full cursor-zoom-in object-contain transition-transform duration-200">

                                    <div class="absolute left-3 top-3 flex gap-2">
                                        <button type="button" id="rotate-photo" class="cursor-pointer rounded-full bg-slate-700 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-slate-800">Rotar</button>
                                        <a id="open-photo" href="{{ $firstPhoto->display_url }}" target="_blank" rel="noopener" class="cursor-pointer rounded-full bg-slate-700 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-slate-800">Abrir</a>
                                    </div>

                                    @if ($photos->count() > 1)
                                        <button type="button" id="prev-photo" class="absolute left-3 top-1/2 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-blue-600/90 text-xl font-bold text-white shadow-sm hover:bg-blue-700" aria-label="Anterior">&lsaquo;</button>
                                        <button type="button" id="next-photo" class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-blue-600/90 text-xl font-bold text-white shadow-sm hover:bg-blue-700" aria-label="Siguiente">&rsaquo;</button>
                                    @endif
                                </div>

                                @if ($photos->count() > 1)
                                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                                        @foreach ($photos as $i => $photo)
                                            <button type="button" data-thumb="{{ $i }}" class="aspect-square h-16 w-16 shrink-0 cursor-pointer overflow-hidden rounded-lg border-2 border-transparent bg-slate-100 focus:outline-none">
                                                <img src="{{ $photo->display_url }}" alt="Miniatura {{ $i + 1 }}" loading="lazy" decoding="async" class="h-full w-full object-cover">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                <div id="photo-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/90 p-4" role="dialog" aria-modal="true">
                                    <button type="button" id="lightbox-close" class="absolute right-4 top-4 cursor-pointer rounded-full bg-blue-600 px-4 py-1.5 text-lg font-bold text-white hover:bg-blue-700" aria-label="Cerrar">&times;</button>
                                    @if ($photos->count() > 1)
                                        <button type="button" id="lightbox-prev" class="absolute left-4 top-1/2 flex h-12 w-12 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-blue-600 text-2xl font-bold text-white hover:bg-blue-700" aria-label="Anterior">&lsaquo;</button>
                                        <button type="button" id="lightbox-next" class="absolute right-4 top-1/2 flex h-12 w-12 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-blue-600 text-2xl font-bold text-white hover:bg-blue-700" aria-label="Siguiente">&rsaquo;</button>
                                    @endif
                                    <img id="lightbox-img" src="" alt="Foto ampliada" class="max-h-full max-w-full cursor-zoom-in rounded-lg object-contain transition-transform duration-200">
                                </div>

                                <script>
                                    (() => {
                                        const photos = @json($photos->map(fn($photo) => $photo->display_url)->values()->all());
                                        const image = document.getElementById('package-photo');
                                        const stage = document.getElementById('photo-stage');
                                        const openLink = document.getElementById('open-photo');
                                        const thumbs = document.querySelectorAll('[data-thumb]');
                                        const box = document.getElementById('photo-lightbox');
                                        const boxImg = document.getElementById('lightbox-img');
                                        let index = 0;
                                        let rotation = 0;
                                        let zoomed = false;

                                        const clearLoading = () => stage.classList.remove('animate-pulse', 'bg-slate-200');
                                        if (image.complete) clearLoading(); else image.addEventListener('load', clearLoading);

                                        const render = () => {
                                            rotation = 0;
                                            zoomed = false;
                                            image.style.transform = '';
                                            image.src = photos[index];
                                            openLink.href = photos[index];
                                            thumbs.forEach((t, i) => {
                                                const active = i === index;
                                                t.classList.toggle('border-blue-600', active);
                                                t.classList.toggle('border-transparent', !active);
                                            });
                                            if (!box.classList.contains('hidden')) {
                                                boxImg.src = photos[index];
                                                boxImg.style.transform = '';
                                            }
                                            new Image().src = photos[(index + 1) % photos.length];
                                        };

                                        const go = (step) => {
                                            if (photos.length < 2) return;
                                            index = (index + step + photos.length) % photos.length;
                                            render();
                                        };

                                        const openBox = () => {
                                            boxImg.src = photos[index];
                                            boxImg.style.transform = '';
                                            zoomed = false;
                                            box.classList.remove('hidden');
                                            box.classList.add('flex');
                                        };
                                        const closeBox = () => { box.classList.add('hidden'); box.classList.remove('flex'); };

                                        document.getElementById('prev-photo')?.addEventListener('click', () => go(-1));
                                        document.getElementById('next-photo')?.addEventListener('click', () => go(1));
                                        document.getElementById('lightbox-prev')?.addEventListener('click', () => go(-1));
                                        document.getElementById('lightbox-next')?.addEventListener('click', () => go(1));
                                        document.getElementById('lightbox-close').addEventListener('click', closeBox);
                                        thumbs.forEach((t) => t.addEventListener('click', () => { index = Number(t.dataset.thumb); render(); }));

                                        document.getElementById('rotate-photo').addEventListener('click', () => {
                                            rotation = (rotation + 90) % 360;
                                            image.style.transform = `rotate(${rotation}deg)`;
                                        });

                                        image.addEventListener('click', openBox);
                                        boxImg.addEventListener('click', () => {
                                            zoomed = !zoomed;
                                            boxImg.style.transform = zoomed ? 'scale(2)' : '';
                                            boxImg.classList.toggle('cursor-zoom-in', !zoomed);
                                            boxImg.classList.toggle('cursor-zoom-out', zoomed);
                                        });
                                        box.addEventListener('click', (e) => { if (e.target === box) closeBox(); });

                                        document.addEventListener('keydown', (e) => {
                                            if (['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;
                                            if (e.key === 'ArrowLeft') go(-1);
                                            if (e.key === 'ArrowRight') go(1);
                                            if (e.key === 'Escape') closeBox();
                                        });

                                        let startX = null;
                                        stage.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
                                        stage.addEventListener('touchend', (e) => {
                                            if (startX === null) return;
                                            const diff = e.changedTouches[0].clientX - startX;
                                            if (Math.abs(diff) > 50) go(diff > 0 ? -1 : 1);
                                            startX = null;
                                        });

                                        render();
                                    })();
                                </script>
                            @endif
                        </div>
                    @else
                        <div class="flex aspect-4/3 min-w-0 items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white p-4 text-center text-sm font-medium text-slate-500 shadow-sm md:p-6 lg:col-span-8">
                            Este paquete no tiene fotos disponibles.
                        </div>
                @endif
                </div>

            @else
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-center text-sm font-medium text-emerald-700">
                    No hay paquetes pendientes por documentar.
                </div>
            @endif
        </div>
    </div>

    <script>
        const editingGuide = document.getElementById('edicion-guia-principal');
        const guideSearch = document.getElementById('documentation-guide-scanner-input');

        window.addEventListener('load', () => {
            (editingGuide ?? guideSearch)?.focus();
        });

        const documentationForm = document.getElementById('documentation-form');

        // Enter solo envía desde "Total de cajas" o sobre el botón.
        documentationForm?.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter') return;
            if (event.target.id === 'total_cajas' || event.target.tagName === 'BUTTON') return;

            event.preventDefault();
        });

        const clientSearch = document.getElementById('client_search');
        const clientId = document.getElementById('client_id');
        const partnerId = document.getElementById('partner_id');
        const partnerSelector = document.getElementById('partner_selector');
        const clientsList = document.getElementById('clients_list');
        const clientOptions = Array.from(document.querySelectorAll('#clients_data button'));

        if (clientSearch && clientId && partnerId && partnerSelector && clientsList) {
            const hideClients = () => clientsList.classList.add('hidden');

            const selectClient = (option) => {
                clientSearch.value = option.dataset.label;
                clientId.value = option.dataset.id;
                partnerId.value = option.dataset.partnerId ?? '';
                partnerSelector.value = option.dataset.partnerId ?? '';
                partnerSelector.disabled = Boolean(option.dataset.partnerId);
                hideClients();
            };

            const renderClients = () => {
                const searchValue = clientSearch.value.trim().toLowerCase();
                clientsList.innerHTML = '';

                clientOptions
                    .filter((option) => !searchValue || option.dataset.search.includes(searchValue))
                    .forEach((option) => {
                        const item = option.cloneNode(true);
                        item.className = 'block w-full border-b border-slate-100 px-3 py-2 text-left text-sm text-slate-700 last:border-0 hover:bg-blue-50';
                        item.addEventListener('click', () => selectClient(option));
                        clientsList.appendChild(item);
                    });

                clientsList.classList.toggle('hidden', clientsList.children.length === 0);
            };

            clientSearch.addEventListener('focus', renderClients);
            clientSearch.addEventListener('input', () => {
                clientId.value = '';
                partnerId.value = '';
                partnerSelector.value = '';
                partnerSelector.disabled = false;
                renderClients();
            });

            partnerSelector.addEventListener('change', () => {
                partnerId.value = partnerSelector.value;
            });

            document.addEventListener('click', (event) => {
                if (!clientSearch.contains(event.target) && !clientsList.contains(event.target)) {
                    hideClients();
                }
            });
        }
    </script>
</body>
</html>
