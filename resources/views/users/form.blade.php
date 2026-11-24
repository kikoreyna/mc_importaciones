<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $user->exists ? 'Editar usuario' : 'Nuevo usuario' }}</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary text-dark">
	<div class="w-100 px-3 py-4">
		@include('components.navbar')
	</div>

	<div class="w-100 content-medium mx-auto px-3 pb-5">
		<div class="mb-5">
			<p class="small text-uppercase letter-spaced text-primary fw-semibold">ADMINISTRACIÓN</p>
			<h1 class="fs-4 fw-bold mt-2">{{ $user->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>
		</div>

		@include('components.flash-messages')

		<form action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" method="POST" class="stack-gap-4 rounded-4 border border-secondary-subtle bg-white p-4 shadow-sm">
			@csrf
			@if ($user->exists)
				@method('PUT')
			@endif

			<div>
				<label for="name" class="mb-2 d-block small fw-medium">Nombre</label>
				<input id="name" name="name" required value="{{ old('name', $user->name) }}" class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2">
			</div>
			<div>
				<label for="email" class="mb-2 d-block small fw-medium">Correo electrónico</label>
				<input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}" class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2">
			</div>
			<div>
				<label for="role" class="mb-2 d-block small fw-medium">Rol</label>
				<select id="role" name="role" required class="form-select w-100 rounded-4 border border-secondary-subtle px-2 py-2">
					@foreach (['administrador' => 'Administrador', 'supervisor' => 'Supervisor', 'documentador' => 'Documentador', 'bodega_usa' => 'Bodega USA', 'bodega_mex' => 'Bodega MEX'] as $role => $label)
						<option value="{{ $role }}" @selected(old('role', $user->role ?: 'documentador') === $role)>{{ $label }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label for="password" class="mb-2 d-block small fw-medium">Contraseña {{ $user->exists ? '(opcional)' : '' }}</label>
				<input id="password" name="password" type="password" {{ $user->exists ? '' : 'required' }} class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2">
			</div>
			<div>
				<label for="password_confirmation" class="mb-2 d-block small fw-medium">Confirmar contraseña</label>
				<input id="password_confirmation" name="password_confirmation" type="password" {{ $user->exists ? '' : 'required' }} class="form-control w-100 rounded-4 border border-secondary-subtle px-2 py-2">
			</div>
			<div class="d-flex gap-2 pt-2">
				<a href="{{ route('users.index') }}" class="flex-fill rounded-4 border border-secondary-subtle px-3 py-2 text-center small fw-semibold">Cancelar</a>
				<button class="btn btn-primary flex-fill rounded-4 bg-primary px-3 py-2 small fw-semibold text-white hover-bg-primary">Guardar</button>
			</div>
		</form>
	</div>
</body>
</html>
