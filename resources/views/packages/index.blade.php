<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escaneo de guía</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 antialiased" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    <div class="w-full px-4 py-6">
        @include('components.navbar')
    </div>

    <div class="w-full max-w-4xl mx-auto px-4 py-6 md:px-6">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">USA</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">Recepción de guía</h1>
                <p class="mt-1 text-sm text-slate-500">Escanea la guía, toma las fotos y guarda el registro.</p>
            </div>
            <a href="{{ route('packages.registered') }}" class="inline-flex cursor-pointer items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                Ver paquetes registrados
            </a>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('packages.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-6">
            @csrf

            <div class="space-y-4">
                <p class="border-b border-slate-100 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Datos de la guía</p>

                <x-camera-scanner
                    id="usa-guide-scanner"
                    name="guia_principal"
                    label="Guía principal"
                    placeholder="Escanee o ingrese la guía principal"
                />

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="guia_secundaria" class="mb-1.5 block text-sm font-medium text-slate-700">Guía secundaria</label>
                        <input id="guia_secundaria" name="guia_secundaria" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-3 text-base focus:border-transparent focus:ring-2 focus:ring-blue-500" placeholder="Opcional">
                    </div>
                    <div>
                        <label for="guia_master" class="mb-1.5 block text-sm font-medium text-slate-700">Guía máster</label>
                        <input id="guia_master" name="guia_master" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-3 text-base focus:border-transparent focus:ring-2 focus:ring-blue-500" placeholder="Opcional">
                    </div>
                </div>
            </div>

            <div>
                <p class="mb-3 border-b border-slate-100 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Fotos</p>
                <label for="photos" class="inline-flex h-11 w-full cursor-pointer items-center justify-center rounded-xl bg-slate-700 px-6 font-semibold text-white shadow-sm hover:bg-slate-800 active:scale-[0.99] sm:w-auto">
                    <span id="photo-label">Tomar foto</span>
                </label>
                <input id="photos" name="photos[]" type="file" multiple accept="image/*" capture="environment" class="hidden">
                <p id="photo-count" class="mt-2 text-xs text-slate-500">Puedes tomar varias fotos antes de guardar la guía.</p>
                <div id="preview" class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6"></div>
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button id="save-guide" type="submit" class="w-full cursor-pointer rounded-xl bg-blue-600 px-8 py-3 font-semibold text-white shadow-sm hover:bg-blue-700 active:bg-blue-800 sm:w-auto">
                    Guardar guía
                </button>
            </div>
        </form>
    </div>

    <script>
        const guiaPrincipal = document.getElementById('usa-guide-scanner-input');
        const guiaSecundaria = document.getElementById('guia_secundaria');
        const input = document.getElementById('photos');
        const preview = document.getElementById('preview');
        const photoLabel = document.querySelector('label[for="photos"]');
        const submitButton = document.getElementById('save-guide');
        const photoLabelText = document.getElementById('photo-label');
        const photoCount = document.getElementById('photo-count');
        const selectedPhotos = new DataTransfer();
        const selectedPhotoHashes = new Set();
        let photoCapturePending = false;

        const getPhotoHash = async (file) => {
            const buffer = await file.arrayBuffer();
            const digest = await crypto.subtle.digest('SHA-256', buffer);

            return [...new Uint8Array(digest)]
                .map(byte => byte.toString(16).padStart(2, '0'))
                .join('');
        };

        const renderPhotoPreview = (files) => {
            preview.innerHTML = '';

            [...files].forEach((file, index) => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = `Foto ${index + 1}`;
                img.className = 'aspect-square w-full object-cover rounded-lg border border-slate-200';
                preview.appendChild(img);
            });
        };

        window.addEventListener('load', () => {
            guiaPrincipal.focus();
        });

        const moveToPhotoCapture = () => {
            const value = guiaPrincipal.value.trim();
            if (value.length > 0 && !photoCapturePending) {
                photoCapturePending = true;
                photoLabel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => photoLabel.click(), 200);
            }
        };

        guiaPrincipal.addEventListener('change', () => {
            if (guiaPrincipal.value.trim().length > 0) {
                moveToPhotoCapture();
            }
        });

        guiaPrincipal.addEventListener('blur', () => {
            if (guiaPrincipal.value.trim().length > 0) {
                moveToPhotoCapture();
            }
        });

        guiaPrincipal.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                moveToPhotoCapture();
                guiaSecundaria.focus();
            }
        });

        input.addEventListener('change', async function () {
            photoCapturePending = false;

            for (const file of [...this.files]) {
                if (!file.type.startsWith('image/')) continue;

                const photoHash = await getPhotoHash(file);
                if (selectedPhotoHashes.has(photoHash)) continue;

                selectedPhotoHashes.add(photoHash);
                selectedPhotos.items.add(file);
            }

            input.files = selectedPhotos.files;
            renderPhotoPreview(selectedPhotos.files);

            const count = selectedPhotos.files.length;
            photoLabelText.textContent = 'Tomar otra foto';
            photoCount.textContent = `${count} foto${count === 1 ? '' : 's'} seleccionada${count === 1 ? '' : 's'}.`;
            submitButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    </script>
</body>
</html>
