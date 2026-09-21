<nav class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm" aria-label="Navegación principal">
    <div class="flex flex-wrap items-center gap-2 p-3">
        <a href="{{ route('packages.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('packages.index') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Recepción USA
        </a>
        <a href="{{ route('packages.registered') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('packages.registered') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Paquetes registrados
        </a>
        <a href="{{ route('bodega.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('bodega.index') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Bodega
        </a>
        <a href="{{ route('documentacion.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('documentacion.index') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Documentación
        </a>
        <a href="{{ route('clients.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('clients.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Clientes
        </a>
        <a href="{{ route('partners.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('partners.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Socios
        </a>
        <a href="{{ route('users.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Usuarios
        </a>
    </div>
</nav>
