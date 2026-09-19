<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escaneo de guía</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="max-w-md mx-auto px-4 py-6 md:max-w-4xl md:px-6">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">USA</p>
            <h1 class="text-2xl font-bold mt-2 md:text-3xl">Recepción de guía</h1>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl mb-5 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('packages.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 space-y-4 md:p-6">
            @csrf

            <div>
                <label for="guia_principal" class="block text-sm font-medium mb-1.5">Guía principal</label>
                <input id="guia_principal" name="guia_principal" type="text" required class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Escanee o ingrese la guía principal">
            </div>

            <div>
                <label for="guia_secundaria" class="block text-sm font-medium mb-1.5">Guía secundaria</label>
                <input id="guia_secundaria" name="guia_secundaria" type="text" class="w-full border border-slate-300 rounded-xl px-3 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Opcional">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Tomar fotos</label>
                <label for="photos" class="flex items-center justify-center h-16 rounded-xl border-2 border-dashed border-blue-300 bg-blue-50 text-blue-700 font-medium cursor-pointer active:scale-[0.99]">
                    <span>Tomar fotos</span>
                </label>
                <input id="photos" name="photos[]" type="file" multiple accept="image/*" capture="environment" class="hidden">
                <p class="text-xs text-slate-500 mt-2">Usa la cámara del móvil para capturar imágenes directamente.</p>
                <div id="preview" class="mt-3 grid grid-cols-3 gap-2"></div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white px-5 py-3.5 rounded-xl font-semibold hover:bg-blue-700 active:bg-blue-800">
                Guardar guía
            </button>
        </form>

        <div class="mt-8">
            <h2 class="text-lg font-semibold mb-3">Paquetes registrados</h2>

            @if ($packages->isEmpty())
                <p class="text-slate-600 text-sm">No hay paquetes registrados aún.</p>
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
    </div>

    <script>
        const guiaPrincipal = document.getElementById('guia_principal');
        const guiaSecundaria = document.getElementById('guia_secundaria');
        const input = document.getElementById('photos');
        const preview = document.getElementById('preview');
        const photoLabel = document.querySelector('label[for="photos"]');
        const submitButton = document.querySelector('button[type="submit"]');

        window.addEventListener('load', () => {
            guiaPrincipal.focus();
        });

        const moveToPhotoCapture = () => {
            const value = guiaPrincipal.value.trim();
            if (value.length > 0) {
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

        input.addEventListener('change', function () {
            preview.innerHTML = '';

            [...this.files].forEach(file => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function (event) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.className = 'w-full h-20 object-cover rounded-lg border border-slate-200';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });

            setTimeout(() => {
                submitButton.scrollIntoView({ behavior: 'smooth', block: 'center' });
                submitButton.focus();
            }, 300);
        });
    </script>
</body>
</html>
