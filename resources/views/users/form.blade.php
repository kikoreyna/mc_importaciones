<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $user->exists ? 'Editar usuario' : 'Nuevo usuario' }}</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
	<div class="max-w-6xl mx-auto px-4 py-6">
		@include('components.navbar')
	</div>

	<div class="max-w-xl mx-auto px-4 pb-6">
		<div class="mb-6">
			<p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">ADMINISTRACIÓN</p>
			<h1 class="text-2xl font-bold mt-2">{{ $user->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>
		</div>

		@include('components.flash-messages')

		<form action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" method="POST" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
			@csrf
			@if ($user->exists)
				@method('PUT')
			@endif

			<div>
				<label for="name" class="mb-1.5 block text-sm font-medium">Nombre</label>
				<input id="name" name="name" required value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-slate-300 px-3 py-3">
			</div>
			<div>
				<label for="email" class="mb-1.5 block text-sm font-medium">Correo electrónico</label>
				<input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-slate-300 px-3 py-3">
			</div>
			<div>
				<label for="role" class="mb-1.5 block text-sm font-medium">Rol</label>
				<select id="role" name="role" required class="w-full rounded-xl border border-slate-300 px-3 py-3">
					@foreach (['administrador' => 'Administrador', 'supervisor' => 'Supervisor', 'documentador' => 'Documentador', 'bodega_usa' => 'Bodega USA', 'bodega_mex' => 'Bodega MEX'] as $role => $label)
						<option value="{{ $role }}" @selected(old('role', $user->role ?: 'documentador') === $role)>{{ $label }}</option>
					@endforeach
				</select>
			</div>
			<div>
				<label for="password" class="mb-1.5 block text-sm font-medium">Contraseña {{ $user->exists ? '(opcional)' : '' }}</label>
				<input id="password" name="password" type="password" {{ $user->exists ? '' : 'required' }} class="w-full rounded-xl border border-slate-300 px-3 py-3">
			</div>
			<div>
				<label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Confirmar contraseña</label>
				<input id="password_confirmation" name="password_confirmation" type="password" {{ $user->exists ? '' : 'required' }} class="w-full rounded-xl border border-slate-300 px-3 py-3">
			</div>
			<div class="flex gap-3 pt-2">
				<a href="{{ route('users.index') }}" class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-semibold">Cancelar</a>
				<button class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Guardar</button>
			</div>
		</form>
	</div>
</body>
</html>
