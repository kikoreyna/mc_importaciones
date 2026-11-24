<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $partner->exists ? 'Editar socio' : 'Nuevo socio' }}</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
	<div class="w-100 px-3 py-4">
		@include('components.navbar')
	</div>

	<div class="w-100 content-medium mx-auto px-3 pb-5">
		<div class="mb-5">
			<p class="small text-uppercase letter-spaced text-primary fw-semibold">ADMINISTRACIÓN</p>
			<h1 class="fs-4 fw-bold mt-2">{{ $partner->exists ? 'Editar socio' : 'Nuevo socio' }}</h1>
		</div>

		@include('components.flash-messages')

		<form action="{{ $partner->exists ? route('partners.update', $partner) : route('partners.store') }}" method="POST" class="stack-gap-4 rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm">
			@csrf
			@if ($partner->exists)
				@method('PUT')
			@endif

			<div>
				<label for="nombre" class="mb-2 d-block small fw-medium">Nombre del socio</label>
				<input id="nombre" name="nombre" required value="{{ old('nombre', $partner->nombre) }}" class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2">
			</div>
			<div>
				<label for="alias" class="mb-2 d-block small fw-medium">Alias del socio</label>
				<input id="alias" name="alias" value="{{ old('alias', $partner->alias) }}" class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2">
			</div>
			<div class="d-flex gap-2 pt-2">
				<a href="{{ route('partners.index') }}" class="flex-fill rounded-4 border border-secondary-subtle px-3 py-2 text-center small fw-semibold">Cancelar</a>
				<button class="btn btn-primary flex-fill rounded-4 bg-primary px-3 py-2 small fw-semibold text-white hover-bg-primary">Guardar</button>
			</div>
		</form>
	</div>
</body>
</html>
