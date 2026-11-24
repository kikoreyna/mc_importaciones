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
<body class="bg-light text-dark" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')
    </div>

    <div class="w-100 content-wide mx-auto px-3 py-4 px-md-5">
        <div class="mb-5 d-flex flex-wrap align-items-end justify-content-between gap-3">
            <div>
                <p class="small fw-semibold text-uppercase letter-spaced text-primary">USA</p>
                <h1 class="mt-1 fs-4 fw-bold tracking-tight text-dark fs-3">Recepción de guía</h1>
                <p class="mt-1 small text-secondary">Escanea la guía, toma las fotos y guarda el registro.</p>
            </div>
            <a href="{{ route('packages.registered') }}" class="btn btn-primary d-inline-flex cursor-pointer align-items-center rounded-3 bg-primary px-3 py-2 small fw-semibold text-white shadow-sm hover-bg-primary">
                Ver paquetes registrados
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-4 border border-secondary-subtle bg-success-subtle px-3 py-2 small fw-medium text-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-4 border border-secondary-subtle bg-danger-subtle px-3 py-2 small fw-medium text-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form
            id="package-form"
            action="{{ route('packages.store') }}"
            data-register-url="{{ route('packages.store') }}"
            data-photo-upload-url="{{ route('packages.photos.scan.store', ['package' => '__PACKAGE_ID__']) }}"
            data-guide-upload-url="{{ route('packages.guides.scan.store', ['package' => '__PACKAGE_ID__']) }}"
            method="POST"
            enctype="multipart/form-data"
            class="stack-gap-5 rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm p-md-5"
        >
            @csrf
            <input id="guide-type" type="hidden" name="guide_type" disabled>
            <input id="guide-action" type="hidden" name="guide_action" disabled>

            <p id="lookup-error" class="d-none rounded-3 border border-secondary-subtle bg-danger-subtle px-2 py-2 small text-danger" role="alert"></p>
            <p id="guide-scan-prompt" class="d-none rounded-3 border border-primary-subtle bg-primary-subtle px-2 py-2 small text-primary" role="status"></p>

            <div class="stack-gap-4">
                <p class="border-bottom border-light pb-2 small fw-semibold text-uppercase letter-spaced text-secondary">Datos de la guía</p>

                <x-camera-scanner
                    id="usa-guide-scanner"
                    name="guia_principal"
                    label="Guía principal"
                    placeholder="Escanee o ingrese la guía principal"
                />

                <div class="row g-3 g-3 row-cols-sm-2">
                    <div id="secondary-guide-field">
                        <label for="guia_secundaria" class="mb-2 d-block small fw-medium text-body">Guía secundaria</label>
                        <input id="guia_secundaria" name="guia_secundaria" type="text" class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2" placeholder="Opcional">
                    </div>
                    <div id="master-guide-field">
                        <label for="guia_master" class="mb-2 d-block small fw-medium text-body">Guía máster</label>
                        <input id="guia_master" name="guia_master" type="text" class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2" placeholder="Opcional">
                    </div>
                </div>
            </div>

            <div id="photos-section">
                <p class="mb-2 border-bottom border-light pb-2 small fw-semibold text-uppercase letter-spaced text-secondary">Fotos</p>
                <label for="photos" class="d-inline-flex h-11 w-100 cursor-pointer align-items-center justify-content-center rounded-4 bg-primary px-4 fw-semibold text-white shadow-sm active-scale w-sm-auto">
                    <span id="photo-label">Tomar foto</span>
                </label>
                <input id="photos" name="photos[]" type="file" multiple accept="image/*" capture="environment" class="d-none">
                <p id="photo-count" class="mt-2 small text-secondary">Puedes tomar varias fotos antes de guardar la guía.</p>
                <div id="preview" class="mt-2 row g-3 row-cols-3 g-2 row-cols-sm-4 row-cols-md-6"></div>
            </div>

            <div class="d-flex justify-content-end border-top border-light pt-4">
                <button id="save-guide" type="submit" class="btn btn-primary w-100 cursor-pointer rounded-4 bg-primary px-4 py-2 fw-semibold text-white shadow-sm hover-bg-primary w-sm-auto">
                    Guardar guía
                </button>
            </div>
        </form>
    </div>

    <div id="duplicate-modal" class="position-fixed inset-0 z-modal-layer d-none align-items-center justify-content-center bg-dark bg-opacity-50 p-3" role="dialog" aria-modal="true" aria-labelledby="duplicate-modal-title">
        <div class="w-100 content-narrow rounded-4 bg-white p-4 shadow-lg">
            <h2 id="duplicate-modal-title" class="fs-5 fw-bold text-dark">Guía ya registrada</h2>
            <p id="duplicate-modal-message" class="mt-2 small text-secondary"></p>
            <p id="duplicate-guide-details" class="mt-2 small text-secondary"></p>
            <p class="mt-2 small fw-medium text-dark">¿Qué desea agregar a esta guía?</p>
            <div class="mt-4 row g-3 g-2">
                <button id="duplicate-add-photos" type="button" class="btn btn-primary cursor-pointer rounded-3 bg-primary px-3 py-2 small fw-semibold text-white hover-bg-primary">
                    Agregar fotos
                </button>
                <button id="duplicate-add-secondary" type="button" class="btn btn-outline-secondary cursor-pointer rounded-3 px-3 py-2 small fw-semibold hover-bg-light">
                    Agregar guía secundaria
                </button>
                <button id="duplicate-add-master" type="button" class="btn btn-outline-secondary cursor-pointer rounded-3 px-3 py-2 small fw-semibold hover-bg-light">
                    Agregar guía máster
                </button>
                <button id="duplicate-cancel" type="button" class="btn btn-outline-secondary mt-1 cursor-pointer rounded-3 px-3 py-2 small fw-semibold hover-bg-light">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <div id="correct-guide-modal" class="position-fixed inset-0 z-modal-layer-high d-none align-items-center justify-content-center bg-dark bg-opacity-50 p-3" role="dialog" aria-modal="true" aria-labelledby="correct-guide-title">
        <div class="w-100 content-narrow rounded-4 bg-white p-4 shadow-lg">
            <h2 id="correct-guide-title" class="fs-5 fw-bold text-dark">Confirmar corrección</h2>
            <p id="correct-guide-message" class="mt-2 small text-secondary"></p>
            <div class="mt-5 d-flex justify-content-end gap-2">
                <button id="correct-guide-cancel" type="button" class="btn btn-outline-secondary cursor-pointer rounded-3 px-3 py-2 small fw-semibold hover-bg-light">
                    Volver
                </button>
                <button id="correct-guide-confirm" type="button" class="btn btn-warning cursor-pointer rounded-3 bg-warning px-3 py-2 small fw-semibold text-dark">
                    Confirmar reemplazo
                </button>
            </div>
        </div>
    </div>

    <script>
        const guiaPrincipal = document.getElementById('usa-guide-scanner-input');
        const guiaSecundaria = document.getElementById('guia_secundaria');
        const input = document.getElementById('photos');
        const preview = document.getElementById('preview');
        const form = document.getElementById('package-form');
        const photoLabel = document.querySelector('label[for="photos"]');
        const submitButton = document.getElementById('save-guide');
        const photoLabelText = document.getElementById('photo-label');
        const photoCount = document.getElementById('photo-count');
        const duplicateModal = document.getElementById('duplicate-modal');
        const duplicateMessage = document.getElementById('duplicate-modal-message');
        const duplicateGuideDetails = document.getElementById('duplicate-guide-details');
        const lookupError = document.getElementById('lookup-error');
        const guideScanPrompt = document.getElementById('guide-scan-prompt');
        const guideTypeInput = document.getElementById('guide-type');
        const guideActionInput = document.getElementById('guide-action');
        const photosSection = document.getElementById('photos-section');
        const correctionModal = document.getElementById('correct-guide-modal');
        const correctionMessage = document.getElementById('correct-guide-message');
        const selectedPhotos = new DataTransfer();
        const selectedPhotoHashes = new Set();
        let photoCapturePending = false;
        let checkedValue = null;
        let matchingPackage = null;
        let pendingLookup = null;
        let pendingLookupValue = null;
        let addPhotosMode = false;
        let addGuideMode = false;
        let guideField = null;
        let guideAction = null;
        let previousGuideValue = null;
        let confirmCorrectionSubmit = false;
        let continueSubmit = false;

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
                img.className = 'aspect-square w-100 object-fit-cover rounded-3 border border-secondary-subtle';
                preview.appendChild(img);
            });
        };

        window.addEventListener('load', () => {
            guiaPrincipal.focus();
        });

        const moveToPhotoCapture = () => {
            const value = guiaPrincipal.value.trim();
            if (value.length > 0 && !photoCapturePending && !addPhotosMode) {
                photoCapturePending = true;
                photoLabel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => photoLabel.click(), 200);
            }
        };

        const showDuplicateModal = (packageData) => {
            matchingPackage = packageData;
            duplicateMessage.textContent = `La guía ${packageData.guia_principal} ya existe.`;
            duplicateGuideDetails.textContent = `Secundaria: ${packageData.guia_secundaria || 'Sin registrar'} · Máster: ${packageData.guia_master || 'Sin registrar'}`;
            const secondaryButton = document.getElementById('duplicate-add-secondary');
            const masterButton = document.getElementById('duplicate-add-master');
            secondaryButton.disabled = false;
            masterButton.disabled = false;
            secondaryButton.textContent = packageData.guia_secundaria ? 'Corregir guía secundaria' : 'Agregar guía secundaria';
            masterButton.textContent = packageData.guia_master ? 'Corregir guía máster' : 'Agregar guía máster';
            duplicateModal.classList.remove('d-none');
            duplicateModal.classList.add('d-flex');
            document.getElementById('duplicate-add-photos').focus();
        };

        const lookupGuide = async (value, capturePhotos = false) => {
            if (!value) return null;

            if (pendingLookup && pendingLookupValue === value) {
                return pendingLookup;
            }

            if (checkedValue === value) {
                if (matchingPackage && !addPhotosMode) showDuplicateModal(matchingPackage);
                else if (!matchingPackage && capturePhotos) moveToPhotoCapture();
                return matchingPackage;
            }

            lookupError.classList.add('d-none');
            pendingLookupValue = value;
            pendingLookup = (async () => {
                const url = new URL(@json(route('packages.lookup')), window.location.origin);
                url.searchParams.set('q', value);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('No se pudo verificar si la guía ya está registrada.');

                const result = await response.json();
                checkedValue = value;
                matchingPackage = result.found ? result.package : null;

                if (matchingPackage) {
                    showDuplicateModal(matchingPackage);
                } else if (capturePhotos) {
                    moveToPhotoCapture();
                }

                return matchingPackage;
            })();

            try {
                return await pendingLookup;
            } catch (error) {
                lookupError.textContent = error.message;
                lookupError.classList.remove('d-none');
                throw error;
            } finally {
                pendingLookup = null;
                pendingLookupValue = null;
            }
        };

        const handleGuideScan = () => {
            const value = guiaPrincipal.value.trim();
            if (value) lookupGuide(value, true).catch(() => {});
        };

        const resetDuplicateFlow = () => {
            duplicateModal.classList.add('d-none');
            duplicateModal.classList.remove('d-flex');
            correctionModal.classList.add('d-none');
            correctionModal.classList.remove('d-flex');
            matchingPackage = null;
            checkedValue = null;
            guiaPrincipal.value = '';
            guiaSecundaria.value = '';
            document.getElementById('guia_master').value = '';
            input.value = '';
            while (selectedPhotos.items.length) selectedPhotos.items.remove(0);
            selectedPhotoHashes.clear();
            preview.innerHTML = '';
            photoLabelText.textContent = 'Tomar foto';
            photoCount.textContent = 'Puedes tomar varias fotos antes de guardar la guía.';
            submitButton.textContent = 'Guardar guía';
            form.action = form.dataset.registerUrl;
            guiaPrincipal.readOnly = false;
            guiaSecundaria.disabled = false;
            guiaSecundaria.required = false;
            guiaMaster.disabled = false;
            guiaMaster.required = false;
            guideTypeInput.disabled = true;
            guideTypeInput.value = '';
            guideActionInput.disabled = true;
            guideActionInput.value = '';
            guideAction = null;
            previousGuideValue = null;
            confirmCorrectionSubmit = false;
            guiaSecundaria.placeholder = 'Opcional';
            guiaMaster.placeholder = 'Opcional';
            photosSection.classList.remove('d-none');
            secondaryGuideContainer.classList.remove('d-none', 'rounded-4', 'border-2', 'border-primary-subtle', 'bg-primary-subtle', 'p-3');
            masterGuideContainer.classList.remove('d-none', 'rounded-4', 'border-2', 'border-primary-subtle', 'bg-primary-subtle', 'p-3');
            guideScanPrompt.classList.add('d-none');
            addPhotosMode = false;
            addGuideMode = false;
            guideField = null;
            photoCapturePending = false;
            guiaPrincipal.focus();
        };

        const guiaMaster = document.getElementById('guia_master');
        const secondaryGuideContainer = document.getElementById('secondary-guide-field');
        const masterGuideContainer = document.getElementById('master-guide-field');

        const prepareGuideAddition = (type) => {
            if (!matchingPackage) return;

            const isSecondary = type === 'secondary';
            guideField = isSecondary ? guiaSecundaria : guiaMaster;
            previousGuideValue = matchingPackage[isSecondary ? 'guia_secundaria' : 'guia_master'];
            guideAction = previousGuideValue ? 'correct' : 'add';
            guideField.value = '';
            guideField.placeholder = guideAction === 'correct' ? `Actual: ${previousGuideValue}` : 'Escanee la guía';

            secondaryGuideContainer.classList.toggle('d-none', !isSecondary);
            masterGuideContainer.classList.toggle('d-none', isSecondary);
            const activeGuideContainer = isSecondary ? secondaryGuideContainer : masterGuideContainer;
            activeGuideContainer.classList.add('rounded-4', 'border-2', 'border-primary-subtle', 'bg-primary-subtle', 'p-3');
            form.action = form.dataset.guideUploadUrl.replace('__PACKAGE_ID__', matchingPackage.id);
            guiaPrincipal.readOnly = true;
            guiaSecundaria.disabled = !isSecondary;
            guiaMaster.disabled = isSecondary;
            guideField.required = true;
            guideTypeInput.value = type;
            guideTypeInput.disabled = false;
            guideActionInput.value = guideAction;
            guideActionInput.disabled = false;
            photosSection.classList.add('d-none');
            addGuideMode = true;
            addPhotosMode = false;
            submitButton.textContent = isSecondary ? 'Guardar guía secundaria' : 'Guardar guía máster';
            guideScanPrompt.textContent = `Escanee la guía ${isSecondary ? 'secundaria' : 'máster'} y después guárdela.`;
            guideScanPrompt.classList.remove('d-none');
            duplicateModal.classList.add('d-none');
            duplicateModal.classList.remove('d-flex');
            guideField.focus();
            activeGuideContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        };

        guiaPrincipal.addEventListener('change', handleGuideScan);
        guiaPrincipal.addEventListener('blur', handleGuideScan);

        guiaPrincipal.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                handleGuideScan();
            }
        });

        form.addEventListener('submit', async (event) => {
            if (continueSubmit) {
                continueSubmit = false;
                return;
            }

            if (confirmCorrectionSubmit) {
                confirmCorrectionSubmit = false;
                return;
            }

            if (addPhotosMode) {
                if (!input.files.length) {
                    event.preventDefault();
                    lookupError.textContent = 'Seleccione al menos una foto para agregar.';
                    lookupError.classList.remove('d-none');
                }
                return;
            }

            if (addGuideMode) {
                if (!guideField || !guideField.value.trim()) {
                    event.preventDefault();
                    lookupError.textContent = 'Escanee la guía antes de guardarla.';
                    lookupError.classList.remove('d-none');
                } else if (guideAction === 'correct') {
                    event.preventDefault();
                    const guideLabel = guideTypeInput.value === 'secondary' ? 'secundaria' : 'máster';
                    correctionMessage.textContent = `Se reemplazará la guía ${guideLabel} "${previousGuideValue}" por "${guideField.value.trim()}". ¿Confirma el cambio?`;
                    correctionModal.classList.remove('d-none');
                    correctionModal.classList.add('d-flex');
                    document.getElementById('correct-guide-confirm').focus();
                }
                return;
            }

            event.preventDefault();
            const value = guiaPrincipal.value.trim();
            if (!value) return;

            try {
                const foundPackage = await lookupGuide(value);
                if (foundPackage) return;

                continueSubmit = true;
                form.requestSubmit(event.submitter || submitButton);
            } catch {
                // lookupGuide muestra el error al usuario; no registrar sin verificar.
            }
        });

        document.getElementById('duplicate-add-photos').addEventListener('click', () => {
            if (!matchingPackage) return;

            form.action = form.dataset.photoUploadUrl.replace('__PACKAGE_ID__', matchingPackage.id);
            guiaPrincipal.readOnly = true;
            guiaSecundaria.disabled = true;
            guiaMaster.disabled = true;
            addPhotosMode = true;
            submitButton.textContent = 'Agregar fotos';
            photoLabelText.textContent = 'Tomar fotos adicionales';
            duplicateModal.classList.add('d-none');
            duplicateModal.classList.remove('d-flex');
            photoCapturePending = true;
            photoLabel.click();
        });

        document.getElementById('duplicate-add-secondary').addEventListener('click', () => prepareGuideAddition('secondary'));
        document.getElementById('duplicate-add-master').addEventListener('click', () => prepareGuideAddition('master'));
        document.getElementById('duplicate-cancel').addEventListener('click', resetDuplicateFlow);
        document.getElementById('correct-guide-cancel').addEventListener('click', () => {
            correctionModal.classList.add('d-none');
            correctionModal.classList.remove('d-flex');
            guideField?.focus();
        });
        document.getElementById('correct-guide-confirm').addEventListener('click', () => {
            correctionModal.classList.add('d-none');
            correctionModal.classList.remove('d-flex');
            confirmCorrectionSubmit = true;
            form.requestSubmit(submitButton);
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
