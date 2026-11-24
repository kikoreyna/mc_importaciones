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
    <label for="{{ $inputId }}" class="mb-2 d-block small fw-medium">{{ $label }}</label>

    <div class="my-2 d-flex flex-column gap-2 flex-sm-row">
        <input
            type="text"
            id="{{ $inputId }}"
            name="{{ $name }}"
            class="form-control w-100 flex-fill rounded-4 border border-secondary-subtle px-2 py-2"
            autocomplete="off"
            placeholder="{{ $placeholder }}"
            value="{{ $value }}"
            required
        >

        <button type="button" id="{{ $scanButtonId }}" class="btn btn-primary h-12.5 w-100 rounded-4 bg-primary px-3 small fw-semibold text-white shadow-sm transition-soft hover-bg-primary w-sm-auto flex-sm-shrink-1 d-xl-none">
            Escanear
        </button>
    </div>

    <div id="{{ $readerId }}" class="mt-2 mw-100 overflow-hidden rounded-4 border border-secondary-subtle bg-light" style="display:none;"></div>

    <button
        type="button"
        id="{{ $stopButtonId }}"
        class="btn btn-danger mt-2 rounded-4 bg-danger px-3 py-2 small fw-semibold text-white shadow-sm transition-soft"
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
                    campoCodigo.dispatchEvent(new Event('change', { bubbles: true }));
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