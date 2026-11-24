<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de la empresa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
    <div class="w-100 px-3 py-4">
        @include('components.navbar')

        <main class="mx-auto w-100 content-wide pb-5">
            <header class="mb-5">
                <p class="small fw-semibold text-uppercase letter-spaced text-primary">ADMINISTRACIÓN</p>
                <h1 class="mt-2 fs-4 fw-bold fs-3">Datos de la empresa</h1>
                <p class="mt-1 small text-secondary">Identidad, contacto y marca de la aplicación.</p>
            </header>

            @include('components.flash-messages')

            <form action="{{ route('company-settings.update') }}" method="POST" enctype="multipart/form-data" class="stack-gap-5" data-company-logo-form>
                @csrf
                @method('PUT')

                <section class="rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm p-md-5" aria-labelledby="company-details-heading">
                    <h2 id="company-details-heading" class="fs-5 fw-semibold">Información general</h2>
                    <div class="mt-3 row g-3 g-3 row-cols-sm-2">
                        <div>
                            <label for="name" class="mb-2 d-block small fw-medium">Nombre comercial</label>
                            <input id="name" name="name" required maxlength="255" value="{{ old('name', $company->name) }}" class="form-control w-100 rounded-3 border border-secondary-subtle px-2 py-2">
                            @error('name')<p class="mt-1 small text-danger">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="legal_name" class="mb-2 d-block small fw-medium">Razón social</label>
                            <input id="legal_name" name="legal_name" maxlength="255" value="{{ old('legal_name', $company->legal_name) }}" class="form-control w-100 rounded-3 border border-secondary-subtle px-2 py-2">
                            @error('legal_name')<p class="mt-1 small text-danger">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="tax_id" class="mb-2 d-block small fw-medium">RFC / Identificación fiscal</label>
                            <input id="tax_id" name="tax_id" maxlength="80" value="{{ old('tax_id', $company->tax_id) }}" class="form-control w-100 rounded-3 border border-secondary-subtle px-2 py-2">
                            @error('tax_id')<p class="mt-1 small text-danger">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="website" class="mb-2 d-block small fw-medium">Sitio web</label>
                            <input id="website" name="website" type="url" maxlength="255" value="{{ old('website', $company->website) }}" placeholder="https://ejemplo.com" class="form-control w-100 rounded-3 border border-secondary-subtle px-2 py-2">
                            @error('website')<p class="mt-1 small text-danger">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="mb-2 d-block small fw-medium">Correo electrónico</label>
                            <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $company->email) }}" class="form-control w-100 rounded-3 border border-secondary-subtle px-2 py-2">
                            @error('email')<p class="mt-1 small text-danger">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="mb-2 d-block small fw-medium">Teléfono</label>
                            <input id="phone" name="phone" type="tel" maxlength="50" value="{{ old('phone', $company->phone) }}" class="form-control w-100 rounded-3 border border-secondary-subtle px-2 py-2">
                            @error('phone')<p class="mt-1 small text-danger">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-sm-2">
                            <label for="address" class="mb-2 d-block small fw-medium">Dirección</label>
                            <textarea id="address" name="address" rows="3" maxlength="2000" class="form-control w-100 rounded-3 border border-secondary-subtle px-2 py-2">{{ old('address', $company->address) }}</textarea>
                            @error('address')<p class="mt-1 small text-danger">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm p-md-5" aria-labelledby="company-brand-heading">
                    <h2 id="company-brand-heading" class="fs-5 fw-semibold">Logo y favicon</h2>
                    <p class="mt-1 small text-secondary">Carga PNG, JPG o WebP. Se convertirá a PNG y se preparará una versión de 64 × 64 px para el favicon.</p>

                    <div class="mt-4 row g-3 g-4 grid-md-form">
                        <div>
                            <label for="logo_source" class="mb-2 d-block small fw-medium">Imagen de marca</label>
                            <input id="logo_source" type="file" accept="image/png,image/jpeg,image/webp" data-logo-source class="form-control d-block min-h-11 w-100 rounded-3 border border-secondary-subtle small">
                            <label class="mt-3 d-flex align-items-start gap-2 small text-body">
                                <input type="checkbox" checked data-remove-background class="form-check-input mt-1 size-4 rounded border-secondary-subtle text-primary">
                                <span>Quitar fondo sólido conectado a los bordes</span>
                            </label>
                            <p class="mt-2 small lh-base text-secondary">Funciona mejor con fondos uniformes. Los PNG con transparencia la conservan; fondos complejos pueden requerir un logo ya recortado.</p>
                            <p class="mt-2 min-h-5 small text-secondary" role="status" aria-live="polite" data-logo-status></p>
                            @error('logo_png')<p class="mt-1 small text-danger">{{ $message }}</p>@enderror
                            @error('favicon_png')<p class="mt-1 small text-danger">{{ $message }}</p>@enderror
                            <input type="file" name="logo_png" accept="image/png" data-logo-output class="d-none">
                            <input type="file" name="favicon_png" accept="image/png" data-favicon-output class="d-none">
                        </div>

                        <div class="row g-3 row-cols-2 g-2 row-cols-md-1">
                            <div>
                                <p class="mb-2 small fw-semibold text-uppercase letter-spaced text-secondary">Logo PNG</p>
                                <div class="checkerboard d-flex aspect-square align-items-center justify-content-center rounded-3 border border-secondary-subtle p-3">
                                    @if ($company->logo_path)
                                        <img src="{{ route('company-settings.logo') }}" alt="Logo actual de la empresa" class="mh-100 mw-100 object-fit-contain" data-logo-current>
                                    @else
                                        <span class="text-center small text-secondary" data-logo-current>Sin logo</span>
                                    @endif
                                    <img alt="Vista previa del logo PNG" class="d-none mh-100 mw-100 object-fit-contain" data-logo-preview>
                                </div>
                            </div>
                            <div>
                                <p class="mb-2 small fw-semibold text-uppercase letter-spaced text-secondary">Favicon 64 × 64</p>
                                <div class="checkerboard d-flex aspect-square align-items-center justify-content-center rounded-3 border border-secondary-subtle p-3">
                                    @if ($company->favicon_path)
                                        <img src="{{ route('company-settings.favicon') }}" alt="Favicon actual de la empresa" class="size-16 object-fit-contain" data-favicon-current>
                                    @else
                                        <span class="text-center small text-secondary" data-favicon-current>Sin favicon</span>
                                    @endif
                                    <img alt="Vista previa del favicon" class="d-none size-16 object-fit-contain" data-favicon-preview>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary min-h-11 w-100 rounded-3 bg-primary px-4 py-2 small fw-semibold text-white hover-bg-primary w-sm-auto">Guardar datos</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>