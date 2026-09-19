<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.2em] text-violet-600 font-semibold">DOCUMENTACIÓN</p>
            <h1 class="text-2xl font-bold mt-2">Edición de guía</h1>
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
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-6">
                <form action="{{ route('documentacion.index') }}" method="GET" class="space-y-4">
                    @csrf
                    <div>
                        <label for="guia_principal" class="block text-sm font-medium mb-1.5">Buscar guía recibida</label>
                        <input id="guia_principal" name="guia_principal" type="text" value="{{ old('guia_principal', $guiaPrincipal ?? '') }}" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-violet-500 focus:border-transparent" placeholder="Escanee la guía">
                    </div>

                    <button type="submit" class="w-full bg-violet-600 text-white px-5 py-3.5 rounded-xl font-semibold hover:bg-violet-700 active:bg-violet-800">
                        Buscar guía
                    </button>
                </form>

                @if ($packagesRecibidos->isNotEmpty())
                    <div class="mt-5 border border-slate-200 rounded-xl p-3 bg-slate-50">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 font-semibold">Pendientes</p>
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                {{ $packagesRecibidos->count() }} por revisar
                            </span>
                        </div>
                        <div class="space-y-2 max-h-52 overflow-y-auto">
                            @foreach ($packagesRecibidos as $index => $receivedPackage)
                                <a href="{{ route('documentacion.index', ['guia_principal' => $receivedPackage->guia_principal]) }}" class="block rounded-lg border {{ $index === 0 ? 'border-amber-400 bg-amber-50 shadow-sm' : 'border-slate-200 bg-white' }} px-3 py-2 text-sm hover:bg-violet-50 transition">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="font-medium">{{ $receivedPackage->guia_principal }}</div>
                                        @if ($index === 0)
                                            <span class="rounded-full bg-amber-200 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-800">Prioridad</span>
                                        @endif
                                    </div>
                                    <div class="text-slate-500 text-xs">{{ $receivedPackage->guia_secundaria ?? 'Sin guía secundaria' }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mt-5 border border-emerald-200 rounded-xl p-3 bg-emerald-50 text-sm text-emerald-700 font-medium">
                        No hay más guías pendientes por revisar.
                    </div>
                @endif
            </div>

            @if ($package)
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-6">
                        <div class="mb-4 pt-1 border-b border-slate-200 pb-3">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Edición</p>
                        </div>

                        <form action="{{ route('documentacion.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="guia_principal" value="{{ $package->guia_principal }}">

                            <div>
                                <label class="block text-sm font-medium mb-1.5">Guía principal</label>
                                <div class="w-full border border-slate-200 bg-slate-50 rounded-xl px-3 py-3 text-base font-medium">
                                    {{ $package->guia_principal }}
                                </div>
                            </div>

                            <div>
                                <label for="client_search" class="block text-sm font-medium mb-1.5">Cliente</label>
                                @php
                                    $selectedClientId = old('client_id', $package->client_id ?? '');
                                    $selectedClient = $clients->firstWhere('id', $selectedClientId);
                                @endphp
                                <input id="client_search" name="client_search" type="text" list="clients_list" value="{{ $selectedClient?->nombre ?? old('client_search', '') }}" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-violet-500 focus:border-transparent" placeholder="Escribe para buscar un cliente" autocomplete="off">
                                <input id="client_id" name="client_id" type="hidden" value="{{ $selectedClientId }}">
                                <datalist id="clients_list">
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->nombre }}" data-id="{{ $client->id }}">{{ $client->codigo }}</option>
                                    @endforeach
                                </datalist>
                            </div>

                            <div>
                                <label for="partner_id" class="block text-sm font-medium mb-1.5">Socio</label>
                                <select id="partner_id" name="partner_id" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                                    <option value="">Selecciona un socio</option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}" {{ old('partner_id', $package->partner_id) == $partner->id ? 'selected' : '' }}>
                                            {{ $partner->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="transportadora" class="block text-sm font-medium mb-1.5">Transportadora</label>
                                @php
                                    $selectedTransportadora = old('transportadora', $package->transportadora ?? '');
                                @endphp
                                <select id="transportadora" name="transportadora" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                                    <option value="">Selecciona una transportadora</option>
                                    @if ($selectedTransportadora && ! in_array($selectedTransportadora, $transportadoras, true))
                                        <option value="{{ $selectedTransportadora }}" selected>{{ $selectedTransportadora }} (actual)</option>
                                    @endif
                                    @foreach ($transportadoras as $transportadora)
                                        <option value="{{ $transportadora }}" {{ $selectedTransportadora === $transportadora ? 'selected' : '' }}>
                                            {{ $transportadora }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="caja_numero" class="block text-sm font-medium mb-1.5">Caja #</label>
                                    <input id="caja_numero" name="caja_numero" type="number" min="1" value="{{ old('caja_numero', $package->caja_numero ?? '') }}" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-violet-500 focus:border-transparent" placeholder="1">
                                </div>
                                <div>
                                    <label for="total_cajas" class="block text-sm font-medium mb-1.5">Total de cajas</label>
                                    <input id="total_cajas" name="total_cajas" type="number" min="1" value="{{ old('total_cajas', $package->total_cajas ?? '') }}" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-violet-500 focus:border-transparent" placeholder="5">
                                </div>
                            </div>

                            <div class="pt-5 mt-3 border-t-2 border-slate-200">
                                <button id="actualizar-guia" type="submit" class="block w-fit mx-auto cursor-pointer bg-blue-600 text-white px-5 py-4 rounded-xl font-bold text-base shadow-md hover:bg-blue-700 active:bg-blue-800">
                                    Actualizar Guia
                                </button>
                            </div>
                        </form>
                    </div>

                    @if ($package->photos->isNotEmpty())
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-6">
                            @php
                                $photos = collect($package->photos->all());
                                $firstPhoto = $photos->first();
                            @endphp

                            @if ($firstPhoto)
                                <div class="relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                    <img id="package-photo" src="{{ $firstPhoto->display_url }}" alt="Foto de paquete" class="w-full h-[520px] md:h-[560px] object-cover">

                                    @if ($photos->count() > 1)
                                        <div class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-gradient-to-t from-slate-900/70 via-slate-900/20 to-transparent p-3">
                                            <button type="button" id="prev-photo" class="rounded-full bg-white/90 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-white">Anterior</button>
                                            <span id="photo-counter" class="text-xs font-semibold text-white">1 / {{ $photos->count() }}</span>
                                            <button type="button" id="next-photo" class="rounded-full bg-white/90 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-white">Siguiente</button>
                                        </div>
                                    @endif
                                </div>

                                <script>
                                    const packagePhotos = @json($photos->map(fn($photo) => ['url' => $photo->display_url])->all());
                                    const photoCounter = document.getElementById('photo-counter');
                                    const photoImage = document.getElementById('package-photo');
                                    const prevButton = document.getElementById('prev-photo');
                                    const nextButton = document.getElementById('next-photo');

                                    if (packagePhotos.length > 1 && photoCounter && photoImage && prevButton && nextButton) {
                                        let currentIndex = 0;

                                        const updatePhoto = () => {
                                            const photo = packagePhotos[currentIndex];
                                            photoImage.src = photo.url;
                                            photoCounter.textContent = `${currentIndex + 1} / ${packagePhotos.length}`;
                                        };

                                        prevButton.addEventListener('click', () => {
                                            currentIndex = (currentIndex - 1 + packagePhotos.length) % packagePhotos.length;
                                            updatePhoto();
                                        });

                                        nextButton.addEventListener('click', () => {
                                            currentIndex = (currentIndex + 1) % packagePhotos.length;
                                            updatePhoto();
                                        });
                                    }
                                </script>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        const clientSearch = document.getElementById('client_search');
        const clientId = document.getElementById('client_id');
        const clientOptions = Array.from(document.querySelectorAll('#clients_list option'));

        if (clientSearch && clientId) {
            clientSearch.addEventListener('input', () => {
                const selectedOption = clientOptions.find((option) => option.value === clientSearch.value);
                clientId.value = selectedOption?.dataset.id ?? '';
            });
        }
    </script>
</body>
</html>
