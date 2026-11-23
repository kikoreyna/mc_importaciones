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

        <form
            id="package-form"
            action="{{ route('packages.store') }}"
            data-register-url="{{ route('packages.store') }}"
            data-photo-upload-url="{{ route('packages.photos.scan.store', ['package' => '__PACKAGE_ID__']) }}"
            data-guide-upload-url="{{ route('packages.guides.scan.store', ['package' => '__PACKAGE_ID__']) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-6"
        >
            @csrf
            <input id="guide-type" type="hidden" name="guide_type" disabled>
            <input id="guide-action" type="hidden" name="guide_action" disabled>

            <p id="lookup-error" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800" role="alert"></p>
            <p id="guide-scan-prompt" class="hidden rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800" role="status"></p>

            <div class="space-y-4">
                <p class="border-b border-slate-100 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">Datos de la guía</p>

                <x-camera-scanner
                    id="usa-guide-scanner"
                    name="guia_principal"
                    label="Guía principal"
                    placeholder="Escanee o ingrese la guía principal"
                />

                <div class="grid gap-4 sm:grid-cols-2">
                    <div id="secondary-guide-field">
                        <label for="guia_secundaria" class="mb-1.5 block text-sm font-medium text-slate-700">Guía secundaria</label>
                        <input id="guia_secundaria" name="guia_secundaria" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-3 text-base focus:border-transparent focus:ring-2 focus:ring-blue-500" placeholder="Opcional">
                    </div>
                    <div id="master-guide-field">
                        <label for="guia_master" class="mb-1.5 block text-sm font-medium text-slate-700">Guía máster</label>
                        <input id="guia_master" name="guia_master" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-3 text-base focus:border-transparent focus:ring-2 focus:ring-blue-500" placeholder="Opcional">
                    </div>
                </div>
            </div>

            <div id="photos-section">
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

    <div id="duplicate-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4" role="dialog" aria-modal="true" aria-labelledby="duplicate-modal-title">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <h2 id="duplicate-modal-title" class="text-lg font-bold text-slate-900">Guía ya registrada</h2>
            <p id="duplicate-modal-message" class="mt-2 text-sm text-slate-600"></p>
            <p id="duplicate-guide-details" class="mt-2 text-xs text-slate-500"></p>
            <p class="mt-3 text-sm font-medium text-slate-800">¿Qué desea agregar a esta guía?</p>
            <div class="mt-5 grid gap-2">
                <button id="duplicate-add-photos" type="button" class="cursor-pointer rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Agregar fotos
                </button>
                <button id="duplicate-add-secondary" type="button" class="cursor-pointer rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                    Agregar guía secundaria
                </button>
                <button id="duplicate-add-master" type="button" class="cursor-pointer rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                    Agregar guía máster
                </button>
                <button id="duplicate-cancel" type="button" class="mt-1 cursor-pointer rounded-lg px-4 py-2 text-sm font-semibold text-slate-500 hover:bg-slate-50">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <div id="correct-guide-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/60 p-4" role="dialog" aria-modal="true" aria-labelledby="correct-guide-title">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <h2 id="correct-guide-title" class="text-lg font-bold text-slate-900">Confirmar corrección</h2>
            <p id="correct-guide-message" class="mt-3 text-sm text-slate-600"></p>
            <div class="mt-6 flex justify-end gap-3">
                <button id="correct-guide-cancel" type="button" class="cursor-pointer rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Volver
                </button>
                <button id="correct-guide-confirm" type="button" class="cursor-pointer rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
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
                img.className = 'aspect-square w-full object-cover rounded-lg border border-slate-200';
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
            duplicateModal.classList.remove('hidden');
            duplicateModal.classList.add('flex');
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

            lookupError.classList.add('hidden');
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
                lookupError.classList.remove('hidden');
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
            duplicateModal.classList.add('hidden');
            duplicateModal.classList.remove('flex');
            correctionModal.classList.add('hidden');
            correctionModal.classList.remove('flex');
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
            photosSection.classList.remove('hidden');
            secondaryGuideContainer.classList.remove('hidden', 'rounded-xl', 'border-2', 'border-blue-300', 'bg-blue-50', 'p-3');
            masterGuideContainer.classList.remove('hidden', 'rounded-xl', 'border-2', 'border-blue-300', 'bg-blue-50', 'p-3');
            guideScanPrompt.classList.add('hidden');
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

            secondaryGuideContainer.classList.toggle('hidden', !isSecondary);
            masterGuideContainer.classList.toggle('hidden', isSecondary);
            const activeGuideContainer = isSecondary ? secondaryGuideContainer : masterGuideContainer;
            activeGuideContainer.classList.add('rounded-xl', 'border-2', 'border-blue-300', 'bg-blue-50', 'p-3');
            form.action = form.dataset.guideUploadUrl.replace('__PACKAGE_ID__', matchingPackage.id);
            guiaPrincipal.readOnly = true;
            guiaSecundaria.disabled = !isSecondary;
            guiaMaster.disabled = isSecondary;
            guideField.required = true;
            guideTypeInput.value = type;
            guideTypeInput.disabled = false;
            guideActionInput.value = guideAction;
            guideActionInput.disabled = false;
            photosSection.classList.add('hidden');
            addGuideMode = true;
            addPhotosMode = false;
            submitButton.textContent = isSecondary ? 'Guardar guía secundaria' : 'Guardar guía máster';
            guideScanPrompt.textContent = `Escanee la guía ${isSecondary ? 'secundaria' : 'máster'} y después guárdela.`;
            guideScanPrompt.classList.remove('hidden');
            duplicateModal.classList.add('hidden');
            duplicateModal.classList.remove('flex');
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
                    lookupError.classList.remove('hidden');
                }
                return;
            }

            if (addGuideMode) {
                if (!guideField || !guideField.value.trim()) {
                    event.preventDefault();
                    lookupError.textContent = 'Escanee la guía antes de guardarla.';
                    lookupError.classList.remove('hidden');
                } else if (guideAction === 'correct') {
                    event.preventDefault();
                    const guideLabel = guideTypeInput.value === 'secondary' ? 'secundaria' : 'máster';
                    correctionMessage.textContent = `Se reemplazará la guía ${guideLabel} "${previousGuideValue}" por "${guideField.value.trim()}". ¿Confirma el cambio?`;
                    correctionModal.classList.remove('hidden');
                    correctionModal.classList.add('flex');
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
            duplicateModal.classList.add('hidden');
            duplicateModal.classList.remove('flex');
            photoCapturePending = true;
            photoLabel.click();
        });

        document.getElementById('duplicate-add-secondary').addEventListener('click', () => prepareGuideAddition('secondary'));
        document.getElementById('duplicate-add-master').addEventListener('click', () => prepareGuideAddition('master'));
        document.getElementById('duplicate-cancel').addEventListener('click', resetDuplicateFlow);
        document.getElementById('correct-guide-cancel').addEventListener('click', () => {
            correctionModal.classList.add('hidden');
            correctionModal.classList.remove('flex');
            guideField?.focus();
        });
        document.getElementById('correct-guide-confirm').addEventListener('click', () => {
            correctionModal.classList.add('hidden');
            correctionModal.classList.remove('flex');
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
