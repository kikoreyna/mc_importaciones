<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4 text-slate-900">
    <main class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">MC IMPORTACIONES</p>
        <h1 class="mt-2 text-2xl font-bold">Iniciar sesión</h1>
        <p class="mt-1 text-sm text-slate-500">Accede para consultar y administrar las guías.</p>

        @include('components.flash-messages')

        <form action="{{ route('login.store') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-xl border border-slate-300 px-3 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium">Contraseña</label>
                <input id="password" name="password" type="password" required class="w-full rounded-xl border border-slate-300 px-3 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-blue-600">
                Recordarme
            </label>
            <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Entrar</button>
        </form>
    </main>
</body>
</html>