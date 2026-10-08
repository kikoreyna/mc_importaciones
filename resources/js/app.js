import './bootstrap';

const navbarToggle = document.querySelector('[data-navbar-toggle]');
const navbarLinks = document.getElementById('navbar-links');

if (navbarToggle && navbarLinks) {
	const closeNavbar = () => {
		navbarLinks.classList.remove('is-open');
		navbarToggle.setAttribute('aria-expanded', 'false');
		navbarToggle.setAttribute('aria-label', 'Abrir menú');
	};

	navbarToggle.addEventListener('click', () => {
		const isExpanded = navbarToggle.getAttribute('aria-expanded') === 'true';

		navbarLinks.classList.toggle('is-open', !isExpanded);
		navbarToggle.setAttribute('aria-expanded', String(!isExpanded));
		navbarToggle.setAttribute('aria-label', isExpanded ? 'Abrir menú' : 'Cerrar menú');
	});

	navbarLinks.addEventListener('click', (event) => {
		if (event.target.closest('a')) {
			closeNavbar();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && navbarToggle.getAttribute('aria-expanded') === 'true') {
			closeNavbar();
			navbarToggle.focus();
		}
	});
}

const navGroups = document.querySelectorAll('[data-nav-group]');

navGroups.forEach((group) => {
	group.addEventListener('toggle', () => {
		if (!group.open) return;
		navGroups.forEach((other) => {
			if (other !== group) other.open = false;
		});
	});
});

document.addEventListener('click', (event) => {
	navGroups.forEach((group) => {
		if (!group.contains(event.target)) group.open = false;
	});
});

document.addEventListener('keydown', (event) => {
	if (event.key === 'Escape') navGroups.forEach((group) => { group.open = false; });
});

const companyFavicon = document.createElement('link');
companyFavicon.rel = 'icon';
companyFavicon.type = 'image/png';
companyFavicon.href = `/company/favicon.png?v=${Date.now()}`;
document.head.append(companyFavicon);

const logoForm = document.querySelector('[data-company-logo-form]');

if (logoForm) {
	const sourceInput = logoForm.querySelector('[data-logo-source]');
	const logoOutput = logoForm.querySelector('[data-logo-output]');
	const faviconOutput = logoForm.querySelector('[data-favicon-output]');
	const removeBackground = logoForm.querySelector('[data-remove-background]');
	const preview = logoForm.querySelector('[data-logo-preview]');
	const faviconPreview = logoForm.querySelector('[data-favicon-preview]');
	const currentLogo = logoForm.querySelector('[data-logo-current]');
	const currentFavicon = logoForm.querySelector('[data-favicon-current]');
	const status = logoForm.querySelector('[data-logo-status]');
	let processedSource = null;
	let processingPromise = null;

	const setStatus = (message, isError = false) => {
		status.textContent = message;
		status.classList.toggle('text-red-700', isError);
		status.classList.toggle('text-slate-500', !isError);
	};

	const setFile = (input, file) => {
		const transfer = new DataTransfer();
		transfer.items.add(file);
		input.files = transfer.files;
	};

	const removeSolidEdgeBackground = (context, width, height, bounds) => {
		const image = context.getImageData(0, 0, width, height);
		const { data } = image;
		const background = [0, 0, 0];
		const corners = [
			bounds.top * width + bounds.left,
			bounds.top * width + bounds.right,
			bounds.bottom * width + bounds.left,
			bounds.bottom * width + bounds.right,
		];
		const visibleCorners = corners.filter((pixel) => data[pixel * 4 + 3] > 0);

		if (!visibleCorners.length) return;

		for (let channel = 0; channel < 3; channel += 1) {
			background[channel] = Math.round(visibleCorners.reduce((sum, pixel) => sum + data[pixel * 4 + channel], 0) / visibleCorners.length);
		}

		const queue = new Int32Array(width * height);
		const visited = new Uint8Array(width * height);
		let readIndex = 0;
		let writeIndex = 0;

		const enqueue = (pixel) => {
			if (pixel < 0 || pixel >= width * height || visited[pixel]) return;
			visited[pixel] = 1;
			const offset = pixel * 4;
			const distance = Math.hypot(data[offset] - background[0], data[offset + 1] - background[1], data[offset + 2] - background[2]);
			if (data[offset + 3] && distance < 92) queue[writeIndex++] = pixel;
		};

		for (let x = bounds.left; x <= bounds.right; x += 1) {
			enqueue(bounds.top * width + x);
			enqueue(bounds.bottom * width + x);
		}
		for (let y = bounds.top + 1; y < bounds.bottom; y += 1) {
			enqueue(y * width + bounds.left);
			enqueue(y * width + bounds.right);
		}

		while (readIndex < writeIndex) {
			const pixel = queue[readIndex++];
			const offset = pixel * 4;
			const distance = Math.hypot(data[offset] - background[0], data[offset + 1] - background[1], data[offset + 2] - background[2]);
			data[offset + 3] = distance < 48 ? 0 : Math.round(data[offset + 3] * Math.min(1, (distance - 48) / 44));
			if (pixel % width > 0) enqueue(pixel - 1);
			if (pixel % width < width - 1) enqueue(pixel + 1);
			if (pixel >= width) enqueue(pixel - width);
			if (pixel < width * (height - 1)) enqueue(pixel + width);
		}

		context.putImageData(image, 0, 0);
	};

	const canvasFile = (canvas, name) => new Promise((resolve, reject) => {
		canvas.toBlob((blob) => {
			if (blob) resolve(new File([blob], name, { type: 'image/png' }));
			else reject(new Error('No se pudo generar el PNG.'));
		}, 'image/png');
	});

	const processLogo = async (file) => {
		if (file.size > 10 * 1024 * 1024) throw new Error('La imagen debe pesar menos de 10 MB.');
		setStatus('Preparando logo y favicon...');
		const bitmap = await createImageBitmap(file);
		const canvas = document.createElement('canvas');
		canvas.width = 512;
		canvas.height = 512;
		const context = canvas.getContext('2d', { willReadFrequently: true });
		const scale = Math.min(480 / bitmap.width, 480 / bitmap.height);
		const width = Math.round(bitmap.width * scale);
		const height = Math.round(bitmap.height * scale);
		const left = Math.floor((512 - width) / 2);
		const top = Math.floor((512 - height) / 2);
		context.drawImage(bitmap, left, top, width, height);
		bitmap.close();

		if (removeBackground.checked) {
			removeSolidEdgeBackground(context, canvas.width, canvas.height, {
				left,
				top,
				right: left + width - 1,
				bottom: top + height - 1,
			});
		}

		const faviconCanvas = document.createElement('canvas');
		faviconCanvas.width = 64;
		faviconCanvas.height = 64;
		faviconCanvas.getContext('2d').drawImage(canvas, 0, 0, 64, 64);
		setFile(logoOutput, await canvasFile(canvas, 'company-logo.png'));
		setFile(faviconOutput, await canvasFile(faviconCanvas, 'company-favicon.png'));
		preview.src = canvas.toDataURL('image/png');
		faviconPreview.src = faviconCanvas.toDataURL('image/png');
		preview.classList.remove('hidden');
		faviconPreview.classList.remove('hidden');
		currentLogo?.classList.add('hidden');
		currentFavicon?.classList.add('hidden');
		setStatus('Vista previa lista. Al guardar se actualizarán el logo y el favicon.');
		processedSource = file;
	};

	const prepareSelectedLogo = () => {
		const file = sourceInput.files[0];
		processedSource = null;
		if (!file) {
			logoOutput.value = '';
			faviconOutput.value = '';
			preview.classList.add('hidden');
			faviconPreview.classList.add('hidden');
			currentLogo?.classList.remove('hidden');
			currentFavicon?.classList.remove('hidden');
			setStatus('');
			return Promise.resolve();
		}

		processingPromise = processLogo(file).catch((error) => {
			logoOutput.value = '';
			faviconOutput.value = '';
			setStatus(error.message || 'No se pudo procesar la imagen.', true);
		});
		return processingPromise;
	};

	sourceInput.addEventListener('change', prepareSelectedLogo);
	removeBackground.addEventListener('change', () => {
		if (sourceInput.files[0]) prepareSelectedLogo();
	});
	logoForm.addEventListener('submit', async (event) => {
		const file = sourceInput.files[0];
		if (!file || processedSource === file) return;
		event.preventDefault();
		await processingPromise;
		if (processedSource === file) logoForm.requestSubmit(event.submitter);
	});
}
