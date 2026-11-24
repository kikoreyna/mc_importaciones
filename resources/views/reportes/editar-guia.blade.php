<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar guía</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <main class="w-100 content-medium mx-auto">
            <div class="mb-5">
                <p class="small fw-semibold text-uppercase letter-spaced text-primary">ADMINISTRACIÓN DE GUÍA</p>
                <h1 class="mt-2 fs-4 fw-bold fs-3">{{ $package->guia_principal }}</h1>
                <p class="mt-1 small text-secondary">Administra las fotografías o elimina esta guía.</p>
            </div>

            @include('components.flash-messages')

            <section class="rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm">
                <div class="mb-4 row g-3 row-cols-2 g-2">
                    <div class="rounded-4 bg-light p-2"><p class="small text-secondary">Color</p><p class="mt-1 small fw-semibold"><x-package-color :package="$package" /></p></div>
                    <div class="rounded-4 bg-light p-2"><p class="small text-secondary">Recibida USA</p><p class="mt-1 small fw-semibold">{{ $package->created_at?->format('d/m/Y H:i') }}</p></div>
                </div>

                    <div class="d-flex flex-column gap-2 flex-sm-row">
                        <a href="{{ route('documentacion.index', ['guia' => $package->id]) }}" class="btn btn-primary flex-fill cursor-pointer rounded-4 bg-primary px-3 py-2 text-center small fw-semibold text-white hover-bg-primary">Editar documentación</a>
                        <a href="{{ route('reports.comparison') }}" class="flex-fill rounded-4 border border-secondary-subtle px-3 py-2 text-center small fw-semibold text-body hover-bg-light">Volver al comparativo</a>
                    </div>

                <div class="my-5 border-top border-secondary-subtle pt-4">
                    <div class="mb-2">
                        <h2 class="fw-semibold">Fotografías de la guía</h2>
                        <p class="mt-1 small text-secondary">El administrador puede eliminar fotografías individuales.</p>
                    </div>
                    @if ($package->photos->isNotEmpty())
                        <div class="row g-3 row-cols-2 g-2 row-cols-sm-3">
                            @foreach ($package->photos as $photo)
                                <div class="overflow-hidden rounded-4 border border-secondary-subtle bg-light">
                                    <img src="{{ $photo->display_url }}" alt="Fotografía de {{ $package->guia_principal }}" loading="lazy" decoding="async" class="h-32 w-100 object-fit-cover">
                                    @if (auth()->user()->isAdministrator())
                                        <form action="{{ route('reports.comparison.photo.destroy', $photo) }}" method="POST" class="p-2" onsubmit="return confirm('¿Eliminar esta fotografía? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100 rounded-3 bg-danger px-2 py-2 small fw-semibold text-white">Eliminar foto</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="rounded-4 bg-light p-3 small text-secondary">Esta guía no tiene fotografías.</p>
                    @endif
                </div>

                @if (auth()->user()->isAdministrator())
                    <div class="d-flex flex-column gap-2 rounded-4 border border-secondary-subtle bg-danger-subtle p-3 flex-sm-row align-items-sm-center justify-content-sm-between">
                        <div>
                            <p class="small fw-semibold text-danger-emphasis">Zona de administrador</p>
                            <p class="mt-1 small text-danger">Desde aquí puedes enviar esta guía a la papelera.</p>
                        </div>
                        <form action="{{ route('reports.comparison.destroy', $package) }}" method="POST" onsubmit="return confirm('¿Enviar esta guía a la papelera? Podrás conservarla para auditoría.');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="return_to" value="{{ $returnTo }}">
                            <button type="submit" class="btn btn-danger w-100 rounded-3 bg-danger px-3 py-2 small fw-semibold text-white w-sm-auto">Enviar a papelera</button>
                        </form>
                    </div>
                @endif

            </section>
        </main>
    </div>
</body>
</html>