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
    <label for="{{ $inputId }}">{{ $label }}</label>

    <div style="display:flex; gap:8px; margin:8px 0;">
        <input
            type="text"
            id="{{ $inputId }}"
            name="{{ $name }}"
            class="form-control"
            autocomplete="off"
            placeholder="{{ $placeholder }}"
            value="{{ $value }}"
            required
        >

        <button type="button" id="{{ $scanButtonId }}" class="btn btn-primary">
            Escanear
        </button>
    </div>

    <div id="{{ $readerId }}" style="max-width:400px; display:none;"></div>

    <button
        type="button"
        id="{{ $stopButtonId }}"
        class="btn btn-danger"
        style="display:none; margin-top:8px;"
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
        botonEscanear.style.display = 'inline-block';
        botonDetener.style.display = 'none';
    }
});
</script>