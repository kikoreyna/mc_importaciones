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
<body class="bg-light text-dark" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <div class="mb-5">
            <p class="small fw-semibold text-uppercase letter-spaced text-primary">Documentación</p>
            <h1 class="mt-1 fs-4 fw-bold tracking-tight text-dark fs-3">Edición de guía</h1>
        </div>

        @if (session('success'))
            <div class="bg-success-subtle border border-secondary-subtle text-success px-3 py-2 rounded-4 mb-4 small fw-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-danger-subtle border border-secondary-subtle text-danger px-3 py-2 rounded-4 mb-4 small fw-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="row g-3 row-cols-1 g-4">
            @if ($package)
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        @if ($posicion > 1)
                            <a id="previous-package" href="{{ route('documentacion.index', ['posicion' => $posicion - 1]) }}" class="rounded-4 border border-secondary bg-transparent px-3 py-2 small fw-semibold text-secondary">&lsaquo; Anterior</a>
                        @else
                            <span class="cursor-not-allowed rounded-4 border border-secondary-subtle px-3 py-2 small fw-semibold text-secondary">&lsaquo; Anterior</span>
                        @endif

                        @if ($posicion < $total)
                            <a id="next-package" href="{{ route('documentacion.index', ['posicion' => $posicion + 1]) }}" class="rounded-4 border border-secondary bg-transparent px-3 py-2 small fw-semibold text-secondary">Siguiente &rsaquo;</a>
                        @else
                            <span class="cursor-not-allowed rounded-4 border border-secondary-subtle px-3 py-2 small fw-semibold text-secondary">Siguiente &rsaquo;</span>
                        @endif
                    </div>
                    <span id="package-position" @class(['w-fit rounded-pill px-3 py-2 small fw-semibold', 'bg-warning-subtle text-warning-emphasis' => ! $package->documented_at]) @if ($package->documented_at) style="background-color: #dcfce7; color: #166534" @endif>{{ $package->documented_at ? 'Documentada' : $posicion . '/' . $total . ' sin documentar' }}</span>
                </div>

                <div class="row g-3 row-cols-1 g-4 row-cols-lg-12">
                    <div class="min-w-0 bg-white rounded-4 border border-secondary-subtle shadow-sm p-3 p-md-5 col-lg-4">
                        <div class="mb-3 pt-1 border-bottom border-secondary-subtle pb-2">
                            <p class="small fw-semibold text-uppercase letter-spaced text-secondary">Edición</p>
                        </div>

                        <form id="documentation-form" action="{{ route('documentacion.store') }}" method="POST" class="stack-gap-4">
                            @csrf
                            <input type="hidden" name="guia_principal" value="{{ $package->guia_principal }}">
                            <input type="hidden" name="posicion" value="{{ $posicion }}">

                            <div>
                                <label class="d-block small fw-medium mb-2">Guía principal</label>
                                <div id="edicion-guia-principal" tabindex="0" class="w-100 border border-secondary-subtle bg-light rounded-4 px-2 py-2 fw-medium">
                                    {{ $package->guia_principal }}
                                </div>
                            </div>

                            <div>
                                <label for="guia_secundaria" class="d-block small fw-medium mb-2">Guía secundaria</label>
                                <input id="guia_secundaria" name="guia_secundaria" type="text" value="{{ old('guia_secundaria', $package->guia_secundaria ?? '') }}" class="form-control w-100 border border-secondary-subtle rounded-4 px-2 py-2" placeholder="Opcional">
                            </div>

                            <div>
                                <label for="client_search" class="d-block small fw-medium mb-2">Cliente</label>
                                @php
                                    $selectedClientId = old('client_id', $package->client_id ?? '');
                                    $selectedClient = $clients->firstWhere('id', $selectedClientId);
                                    $selectedClientLabel = $selectedClient
                                        ? $selectedClient->nombre . ($selectedClient->alias ? ' | ' . $selectedClient->alias : '')
                                        : old('client_search', '');
                                @endphp
                                <div class="position-relative">
                                    <input id="client_search" name="client_search" type="text" value="{{ $selectedClientLabel }}" class="form-control w-100 border border-secondary-subtle rounded-4 px-2 py-2" placeholder="Escribe nombre, alias o código" autocomplete="off">
                                    <div id="clients_list" class="position-absolute z-modal-layer mt-1 d-none max-h-56 w-100 overflow-y-auto rounded-4 border border-secondary-subtle bg-white shadow"></div>
                                </div>
                                <input id="client_id" name="client_id" type="hidden" value="{{ $selectedClientId }}">
                                <div id="clients_data" class="d-none">
                                    @foreach ($clients as $client)
                                        <button type="button" data-id="{{ $client->id }}" data-partner-id="{{ $client->partner_id ?? '' }}" data-label="{{ $client->nombre }}{{ $client->alias ? ' | ' . $client->alias : '' }}" data-search="{{ strtolower($client->nombre . ' ' . ($client->alias ?? '')) }}">{{ $client->nombre }}{{ $client->alias ? ' | ' . $client->alias : '' }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label for="partner_id" class="d-block small fw-medium mb-2">Socio</label>
                                <select id="partner_selector" name="partner_selector" {{ $selectedClient?->partner_id ? 'disabled' : '' }} class="form-select w-100 border border-secondary-subtle rounded-4 px-2 py-2">
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
                                <label for="transportadora" class="d-block small fw-medium mb-2">Transportadora</label>
                                @php
                                    $selectedTransportadora = old('transportadora', $package->transportadora ?? '');
                                @endphp
                                <select id="transportadora" name="transportadora" class="form-select w-100 border border-secondary-subtle rounded-4 px-2 py-2">
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
                                <label for="guia_master" class="d-block small fw-medium mb-2">Guía máster</label>
                                <input id="guia_master" name="guia_master" type="text" value="{{ old('guia_master', $package->guia_master ?? '') }}" class="form-control w-100 border border-secondary-subtle rounded-4 px-2 py-2" placeholder="Opcional">
                            </div>

                            <div class="row g-3 row-cols-2 g-3">
                                <div>
                                    <label for="caja_numero" class="d-block small fw-medium mb-2">Caja #</label>
                                    <input id="caja_numero" name="caja_numero" type="number" min="1" value="{{ old('caja_numero', $package->caja_numero ?? '') }}" class="form-control w-100 border border-secondary-subtle rounded-4 px-2 py-2" placeholder="1">
                                </div>
                                <div>
                                    <label for="total_cajas" class="d-block small fw-medium mb-2">Total de cajas</label>
                                    <input id="total_cajas" name="total_cajas" type="number" min="1" value="{{ old('total_cajas', $package->total_cajas ?? '') }}" class="form-control w-100 border border-secondary-subtle rounded-4 px-2 py-2" placeholder="5">
                                </div>
                            </div>

                            <div class="pt-4 mt-2 border-top border-2 border-secondary-subtle">
                                <button id="actualizar-guia" type="submit" class="btn btn-primary d-block w-fit mx-auto cursor-pointer bg-primary text-white px-4 py-2 rounded-4 fw-semibold shadow-sm hover-bg-primary">
                                    Actualizar Guia
                                </button>
                            </div>
                        </form>
                    </div>

                    @if ($package->photos->isNotEmpty())
                        <div class="min-w-0 bg-white rounded-4 border border-secondary-subtle shadow-sm p-3 p-md-5 col-lg-8">
                            @php
                                $photos = collect($package->photos->all());
                                $firstPhoto = $photos->first();
                            @endphp

                            @if ($firstPhoto)
                                <div id="photo-stage" class="position-relative aspect-4-3 w-100 placeholder-glow overflow-hidden rounded-4 border border-secondary-subtle bg-secondary-subtle">
                                    <img id="package-photo" src="{{ $firstPhoto->display_url }}" alt="Foto de paquete" class="h-100 w-100 cursor-zoom-in object-fit-contain transition-soft duration-200">

                                    <div class="position-absolute left-3 top-3 d-flex gap-2">
                                        <button type="button" id="rotate-photo" class="btn btn-secondary cursor-pointer rounded-pill bg-secondary px-2 py-1 small fw-semibold text-white shadow-sm">Rotar</button>
                                        <a id="open-photo" href="{{ $firstPhoto->display_url }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm cursor-pointer rounded-pill bg-secondary px-2 py-1 small fw-semibold text-white shadow-sm">Abrir</a>
                                    </div>

                                    @if ($photos->count() > 1)
                                        <button type="button" id="prev-photo" class="btn btn-primary position-absolute left-3 top-1/2 d-flex h-10 w-10 translate-middle-y cursor-pointer align-items-center justify-content-center rounded-pill bg-primary fs-5 fw-bold text-white shadow-sm hover-bg-primary" aria-label="Anterior">&lsaquo;</button>
                                        <button type="button" id="next-photo" class="btn btn-primary position-absolute right-3 top-1/2 d-flex h-10 w-10 translate-middle-y cursor-pointer align-items-center justify-content-center rounded-pill bg-primary fs-5 fw-bold text-white shadow-sm hover-bg-primary" aria-label="Siguiente">&rsaquo;</button>
                                    @endif
                                </div>

                                @if ($photos->count() > 1)
                                    <div class="mt-2 d-flex gap-2 overflow-x-auto pb-1">
                                        @foreach ($photos as $i => $photo)
                                            <button type="button" data-thumb="{{ $i }}" class="btn btn-light aspect-square h-16 w-16 flex-shrink-0 cursor-pointer overflow-hidden rounded-3 border-2 border-transparent bg-body-tertiary p-0">
                                                <img src="{{ $photo->display_url }}" alt="Miniatura {{ $i + 1 }}" loading="lazy" decoding="async" class="h-100 w-100 object-fit-cover">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                <div id="photo-lightbox" class="position-fixed inset-0 z-modal-layer d-none align-items-center justify-content-center bg-dark bg-opacity-75 p-3" role="dialog" aria-modal="true">
                                    <button type="button" id="lightbox-close" class="btn btn-primary position-absolute right-4 top-4 cursor-pointer rounded-pill bg-primary px-3 py-2 fs-5 fw-bold text-white hover-bg-primary" aria-label="Cerrar">&times;</button>
                                    @if ($photos->count() > 1)
                                        <button type="button" id="lightbox-prev" class="btn btn-primary position-absolute left-4 top-1/2 d-flex h-12 w-12 translate-middle-y cursor-pointer align-items-center justify-content-center rounded-pill bg-primary fs-4 fw-bold text-white hover-bg-primary" aria-label="Anterior">&lsaquo;</button>
                                        <button type="button" id="lightbox-next" class="btn btn-primary position-absolute right-4 top-1/2 d-flex h-12 w-12 translate-middle-y cursor-pointer align-items-center justify-content-center rounded-pill bg-primary fs-4 fw-bold text-white hover-bg-primary" aria-label="Siguiente">&rsaquo;</button>
                                    @endif
                                    <img id="lightbox-img" src="" alt="Foto ampliada" class="mh-100 mw-100 cursor-zoom-in rounded-3 object-fit-contain transition-soft duration-200">
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

                                        const clearLoading = () => stage.classList.remove('placeholder-glow', 'bg-secondary-subtle');
                                        if (image.complete) clearLoading(); else image.addEventListener('load', clearLoading);

                                        const render = () => {
                                            rotation = 0;
                                            zoomed = false;
                                            image.style.transform = '';
                                            image.src = photos[index];
                                            openLink.href = photos[index];
                                            thumbs.forEach((t, i) => {
                                                const active = i === index;
                                                t.classList.toggle('border-primary', active);
                                                t.classList.toggle('border-transparent', !active);
                                            });
                                            if (!box.classList.contains('d-none')) {
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
                                            box.classList.remove('d-none');
                                            box.classList.add('d-flex');
                                        };
                                        const closeBox = () => { box.classList.add('d-none'); box.classList.remove('d-flex'); };

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
                        <div class="d-flex aspect-4-3 min-w-0 align-items-center justify-content-center rounded-4 border border-dashed border-secondary-subtle bg-white p-3 text-center small fw-medium text-secondary shadow-sm p-md-5 col-lg-8">
                            Este paquete no tiene fotos disponibles.
                        </div>
                @endif
                </div>

            @else
                <div class="rounded-4 border border-secondary-subtle bg-success-subtle p-4 text-center small fw-medium text-success">
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
            const hideClients = () => clientsList.classList.add('d-none');

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
                        item.className = 'd-block w-100 border-bottom border-light px-2 py-2 text-start small text-body hover-bg-primary';
                        item.addEventListener('click', () => selectClient(option));
                        clientsList.appendChild(item);
                    });

                clientsList.classList.toggle('d-none', clientsList.children.length === 0);
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
