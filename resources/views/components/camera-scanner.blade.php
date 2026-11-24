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

    <div id="{{ $readerId }}" class="camera-scanner-reader mt-2 overflow-hidden rounded-4 border border-secondary-subtle" style="display:none;"></div>
    <p class="mt-2 small text-body-secondary">Centra todo el código en la imagen. Si cuesta leerlo, usa la linterna o ajusta el zoom cuando estén disponibles.</p>

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
    let codigoCandidato = null;
    let lecturasCandidato = 0;

    botonEscanear.addEventListener('click', async () => {
        if (!window.isSecureContext) {
            alert('Para usar la cámara, abre el sitio mediante una conexión HTTPS.');
            return;
        }

        if (!navigator.mediaDevices?.getUserMedia) {
            alert('Este navegador no permite acceder a la cámara. Usa una versión actualizada de Chrome o Safari.');
            return;
        }

        codigoCandidato = null;
        lecturasCandidato = 0;
        lector.style.display = 'block';
        botonEscanear.style.display = 'none';
        botonDetener.style.display = 'inline-block';

        try {
            scanner = new Html5Qrcode(@json($readerId), {
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.QR_CODE,
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.CODE_93,
                    Html5QrcodeSupportedFormats.DATA_MATRIX,
                    Html5QrcodeSupportedFormats.ITF,
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.PDF_417,
                ],
            });

            await scanner.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        const width = Math.floor(Math.min(viewfinderWidth * 0.9, 600));
                        const height = Math.floor(Math.min(viewfinderHeight * 0.7, width * 0.75));

                        return { width, height };
                    },
                    showTorchButtonIfSupported: true,
                    showZoomSliderIfSupported: true,
                },
                async codigo => {
                    if (codigo !== codigoCandidato) {
                        codigoCandidato = codigo;
                        lecturasCandidato = 1;
                        return;
                    }

                    lecturasCandidato++;
                    if (lecturasCandidato < 2) {
                        return;
                    }

                    campoCodigo.value = codigo;
                    await detenerScanner();
                    campoCodigo.dispatchEvent(new Event('change', { bubbles: true }));
                },
                () => {}
            );

            try {
                await scanner.applyVideoConstraints({
                    width: { ideal: 1920 },
                    height: { ideal: 1080 },
                });
            } catch (error) {
                console.warn('La cámara no permitió aumentar la resolución:', error);
            }

            const zoom = scanner.getRunningTrackCapabilities().zoom;
            if (zoom && typeof zoom.min === 'number' && typeof zoom.max === 'number' && zoom.max > zoom.min) {
                try {
                    await scanner.applyVideoConstraints({
                        advanced: [{ zoom: Math.min(Math.max(2, zoom.min), zoom.max) }],
                    });
                } catch (error) {
                    console.warn('La cámara no permitió aplicar el zoom inicial:', error);
                }
            }
        } catch (error) {
            console.error('Error al iniciar el escáner:', error);
            const errorName = error?.name;
            const message = errorName === 'NotAllowedError' || errorName === 'PermissionDeniedError'
                ? 'El navegador denegó el permiso de cámara. Habilítalo en los permisos del sitio y vuelve a intentar.'
                : errorName === 'NotFoundError' || errorName === 'DevicesNotFoundError'
                    ? 'No se encontró una cámara disponible en este dispositivo.'
                    : errorName === 'NotReadableError' || errorName === 'TrackStartError'
                        ? 'La cámara está ocupada por otra aplicación. Ciérrala e intenta nuevamente.'
                        : `No se pudo iniciar la cámara${error?.message ? `: ${error.message}` : '.'}`;
            alert(message);
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
        codigoCandidato = null;
        lecturasCandidato = 0;
    }
});
</script>