<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paquetes registrados</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
</head>
<body class="bg-light text-dark" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <div class="mb-5 d-flex flex-wrap align-items-end justify-content-between gap-3">
            <div>
                <p class="small fw-semibold text-uppercase letter-spaced text-primary">Recepción</p>
                <h1 class="mt-1 fs-4 fw-bold tracking-tight text-dark fs-3">Paquetes registrados</h1>
                <p class="mt-1 small text-secondary">Registros de hoy · {{ $packages->count() }} {{ $packages->count() === 1 ? 'paquete' : 'paquetes' }}</p>
            </div>
            <a href="{{ route('packages.index') }}" class="btn btn-primary d-inline-flex cursor-pointer align-items-center gap-2 rounded-3 bg-primary px-3 py-2 small fw-semibold text-white shadow-sm hover-bg-primary">
                <span class="fs-5 lh-1">+</span> Registrar guía
            </a>
        </div>

        @if (session('success'))
            <div class="mb-3 rounded-4 bg-success-subtle border border-secondary-subtle p-2 small text-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-3 rounded-4 bg-danger-subtle border border-secondary-subtle p-2 small text-danger">{{ $errors->first() }}</div>
        @endif

        @if ($packages->isEmpty())
            <div class="bg-white rounded-4 border border-secondary-subtle shadow-sm p-4 small text-secondary">
                No hay paquetes registrados aún.
            </div>
        @else
            <div class="row g-3 g-3 row-cols-lg-2 row-cols-xxl-3">
                @foreach ($packages as $package)
                    <article class="rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm">
                        <dl class="row g-3 row-cols-2 column-gap-3 row-gap-2 row-cols-sm-3">
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Guía principal</dt>
                                <dd class="mt-1 text-truncate small fw-semibold text-dark">{{ $package->guia_principal }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Guía secundaria</dt>
                                <dd class="mt-1 text-truncate small fw-semibold text-dark">{{ $package->guia_secundaria ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="small fw-medium text-uppercase letter-spaced text-secondary">Color</dt>
                                <dd class="mt-1">
                                    <x-package-color :package="$package" />
                                </dd>
                            </div>
                        </dl>

                        @if ($package->photos->isNotEmpty())
                            <div class="mt-3 row g-3 row-cols-3 g-2 row-cols-sm-4">
                                @foreach ($package->photos as $photo)
                                    <button type="button" data-lightbox="{{ $photo->display_url }}" class="aspect-square cursor-pointer overflow-hidden rounded-3 border border-secondary-subtle bg-body-tertiary">
                                        <img src="{{ $photo->display_url }}" alt="Foto del paquete" loading="lazy" decoding="async" class="h-100 w-100 object-fit-cover transition-soft hover-scale">
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('packages.photos.store', $package) }}" enctype="multipart/form-data" class="mt-3 d-flex flex-wrap align-items-center gap-2 border-top border-light pt-3">
                            @csrf
                            <input type="file" name="photos[]" multiple accept="image/*" required class="form-control min-w-0 flex-fill small text-secondary">
                            <button type="submit" class="btn btn-primary cursor-pointer rounded-3 bg-primary px-3 py-2 small fw-semibold text-white shadow-sm hover-bg-primary">Subir fotos</button>
                            <a href="{{ route('packages.history', $package) }}" class="btn btn-secondary cursor-pointer rounded-3 bg-secondary px-3 py-2 small fw-semibold text-white shadow-sm">Historial</a>
                        </form>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    <div id="lightbox" class="position-fixed inset-0 z-modal-layer d-none align-items-center justify-content-center bg-dark bg-opacity-75 p-3" role="dialog" aria-modal="true">
        <button type="button" id="lightbox-close" class="btn btn-primary position-absolute right-4 top-4 cursor-pointer rounded-pill bg-white bg-opacity-75 px-2 py-1 fs-5 fw-bold text-dark" aria-label="Cerrar">&times;</button>
        <img id="lightbox-img" src="" alt="Foto ampliada" class="mh-100 mw-100 rounded-3 object-fit-contain shadow-lg">
    </div>

    <script>
        (() => {
            const box = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            const close = () => { box.classList.add('d-none'); box.classList.remove('d-flex'); img.src = ''; };
            document.querySelectorAll('[data-lightbox]').forEach((el) => el.addEventListener('click', () => {
                img.src = el.dataset.lightbox;
                box.classList.remove('d-none');
                box.classList.add('d-flex');
            }));
            box.addEventListener('click', (e) => { if (e.target !== img) close(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        })();
    </script>
</body>
</html>
