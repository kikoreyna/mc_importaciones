<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de la empresa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="w-full px-4 py-6">
        @include('components.navbar')

        <main class="mx-auto w-full max-w-5xl pb-8">
            <header class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-700">ADMINISTRACIÓN</p>
                <h1 class="mt-2 text-2xl font-bold md:text-3xl">Datos de la empresa</h1>
                <p class="mt-1 text-sm text-slate-600">Identidad, contacto y marca de la aplicación.</p>
            </header>

            @include('components.flash-messages')

            <form action="{{ route('company-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5" data-company-logo-form>
                @csrf
                @method('PUT')

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm md:p-6" aria-labelledby="company-details-heading">
                    <h2 id="company-details-heading" class="text-lg font-semibold">Información general</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-medium">Nombre comercial</label>
                            <input id="name" name="name" required maxlength="255" value="{{ old('name', $company->name) }}" class="w-full rounded-lg border border-slate-300 px-3 py-3">
                            @error('name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="legal_name" class="mb-1.5 block text-sm font-medium">Razón social</label>
                            <input id="legal_name" name="legal_name" maxlength="255" value="{{ old('legal_name', $company->legal_name) }}" class="w-full rounded-lg border border-slate-300 px-3 py-3">
                            @error('legal_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="tax_id" class="mb-1.5 block text-sm font-medium">RFC / Identificación fiscal</label>
                            <input id="tax_id" name="tax_id" maxlength="80" value="{{ old('tax_id', $company->tax_id) }}" class="w-full rounded-lg border border-slate-300 px-3 py-3">
                            @error('tax_id')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="website" class="mb-1.5 block text-sm font-medium">Sitio web</label>
                            <input id="website" name="website" type="url" maxlength="255" value="{{ old('website', $company->website) }}" placeholder="https://ejemplo.com" class="w-full rounded-lg border border-slate-300 px-3 py-3">
                            @error('website')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium">Correo electrónico</label>
                            <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $company->email) }}" class="w-full rounded-lg border border-slate-300 px-3 py-3">
                            @error('email')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-medium">Teléfono</label>
                            <input id="phone" name="phone" type="tel" maxlength="50" value="{{ old('phone', $company->phone) }}" class="w-full rounded-lg border border-slate-300 px-3 py-3">
                            @error('phone')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="mb-1.5 block text-sm font-medium">Dirección</label>
                            <textarea id="address" name="address" rows="3" maxlength="2000" class="w-full rounded-lg border border-slate-300 px-3 py-3">{{ old('address', $company->address) }}</textarea>
                            @error('address')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm md:p-6" aria-labelledby="company-brand-heading">
                    <h2 id="company-brand-heading" class="text-lg font-semibold">Logo y favicon</h2>
                    <p class="mt-1 text-sm text-slate-600">Carga PNG, JPG o WebP. Se convertirá a PNG y se preparará una versión de 64 × 64 px para el favicon.</p>

                    <div class="mt-5 grid gap-6 md:grid-cols-[minmax(0,1fr)_240px]">
                        <div>
                            <label for="logo_source" class="mb-1.5 block text-sm font-medium">Imagen de marca</label>
                            <input id="logo_source" type="file" accept="image/png,image/jpeg,image/webp" data-logo-source class="block min-h-11 w-full rounded-lg border border-slate-300 text-sm file:mr-4 file:min-h-11 file:border-0 file:bg-slate-100 file:px-4 file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                            <label class="mt-4 flex items-start gap-3 text-sm text-slate-700">
                                <input type="checkbox" checked data-remove-background class="mt-0.5 size-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                                <span>Quitar fondo sólido conectado a los bordes</span>
                            </label>
                            <p class="mt-2 text-xs leading-5 text-slate-500">Funciona mejor con fondos uniformes. Los PNG con transparencia la conservan; fondos complejos pueden requerir un logo ya recortado.</p>
                            <p class="mt-3 min-h-5 text-sm text-slate-500" role="status" aria-live="polite" data-logo-status></p>
                            @error('logo_png')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                            @error('favicon_png')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                            <input type="file" name="logo_png" accept="image/png" data-logo-output class="hidden">
                            <input type="file" name="favicon_png" accept="image/png" data-favicon-output class="hidden">
                        </div>

                        <div class="grid grid-cols-2 gap-3 md:grid-cols-1">
                            <div>
                                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Logo PNG</p>
                                <div class="checkerboard flex aspect-square items-center justify-center rounded-lg border border-slate-200 p-4">
                                    @if ($company->logo_path)
                                        <img src="{{ route('company-settings.logo') }}" alt="Logo actual de la empresa" class="max-h-full max-w-full object-contain" data-logo-current>
                                    @else
                                        <span class="text-center text-xs text-slate-500" data-logo-current>Sin logo</span>
                                    @endif
                                    <img alt="Vista previa del logo PNG" class="hidden max-h-full max-w-full object-contain" data-logo-preview>
                                </div>
                            </div>
                            <div>
                                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Favicon 64 × 64</p>
                                <div class="checkerboard flex aspect-square items-center justify-center rounded-lg border border-slate-200 p-4">
                                    @if ($company->favicon_path)
                                        <img src="{{ route('company-settings.favicon') }}" alt="Favicon actual de la empresa" class="size-16 object-contain" data-favicon-current>
                                    @else
                                        <span class="text-center text-xs text-slate-500" data-favicon-current>Sin favicon</span>
                                    @endif
                                    <img alt="Vista previa del favicon" class="hidden size-16 object-contain" data-favicon-preview>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="flex justify-end">
                    <button type="submit" class="min-h-11 w-full rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800 sm:w-auto">Guardar datos</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>