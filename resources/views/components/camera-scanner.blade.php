@props([
    'name' => 'codigo',
    'label' => 'Código',
    'placeholder' => '',
    'id' => 'camera-scanner',
    'value' => '',
])

@php
    $inputId = $id . '-input';
    $scanButtonId = $id . '-scan';
    $stopButtonId = $id . '-stop';
    $readerId = $id . '-reader';
@endphp

<div>
    <label for="{{ $inputId }}" class="mb-1.5 block text-sm font-medium">{{ $label }}</label>

    <div class="my-2 flex flex-col gap-2 sm:flex-row">
        <input
            type="text"
            id="{{ $inputId }}"
            name="{{ $name }}"
            class="w-full flex-1 rounded-xl border border-slate-300 px-3 py-3 text-base focus:border-transparent focus:ring-2 focus:ring-blue-500"
            autocomplete="off"
            placeholder="{{ $placeholder }}"
            value="{{ $value }}"
            required
        >

        <button type="button" id="{{ $scanButtonId }}" class="h-12.5 w-full rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto sm:shrink-0 xl:hidden">
            Escanear
        </button>
    </div>

    <div id="{{ $readerId }}" class="mt-3 max-w-100 overflow-hidden rounded-xl border border-slate-200 bg-slate-50" style="display:none;"></div>

    <button
        type="button"
        id="{{ $stopButtonId }}"
        class="mt-2 rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
        style="display:none;"
    >
        Detener cámara
    </button>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const botonEscanear = document.getElementById(@json($scanButtonId));
    const botonDetener = document.getElementById(@json($stopButtonId));
    const lector = document.getElementById(@json($readerId));
    const campoCodigo = document.getElementById(@json($inputId));

    let scanner = null;

    botonEscanear.addEventListener('click', async () => {
        lector.style.display = 'block';
        botonEscanear.style.display = 'none';
        botonDetener.style.display = 'inline-block';

        scanner = new Html5Qrcode(@json($readerId));

        try {
            await scanner.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 150 }
                },
                async codigo => {
                    campoCodigo.value = codigo;
                    await detenerScanner();
                },
                () => {}
            );
        } catch (error) {
            alert('No se pudo acceder a la cámara.');
            await detenerScanner();
        }
    });

    botonDetener.addEventListener('click', detenerScanner);

    async function detenerScanner() {
        if (scanner) {
            try {
                await scanner.stop();
                scanner.clear();
            } catch (error) {
                console.error(error);
            }

            scanner = null;
        }

        lector.style.display = 'none';
        botonEscanear.style.removeProperty('display');
        botonDetener.style.display = 'none';
    }
});
</script>