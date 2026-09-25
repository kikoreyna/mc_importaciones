<nav class="relative left-1/2 z-30 mb-6 w-screen -translate-x-1/2 border-y border-slate-200 bg-white shadow-sm sm:rounded-lg sm:border" aria-label="Navegación principal">
    <div class="flex flex-wrap items-center justify-end gap-2 px-4 py-3 sm:px-6 lg:px-8">
        <button type="button" class="inline-flex h-10 w-10 flex-col items-center justify-center gap-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 sm:hidden" data-navbar-toggle aria-controls="navbar-links" aria-expanded="false" aria-label="Abrir menú">
            <span class="h-0.5 w-5 bg-current"></span>
            <span class="h-0.5 w-5 bg-current"></span>
            <span class="h-0.5 w-5 bg-current"></span>
        </button>

        <div id="navbar-links" class="hidden w-full flex-col items-stretch gap-1 pt-3 sm:flex sm:w-auto sm:flex-row sm:flex-wrap sm:items-center sm:justify-end sm:gap-2 sm:pt-0">
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
        @auth
            @if (auth()->user()->isAdministrator())
                <a href="{{ route('users.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Usuarios
                </a>
            @endif
        @endauth
        <a href="{{ route('reports.packages') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('reports.packages') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Reportería
        </a>
        @auth
            @if (auth()->user()->canAccessDashboard())
                <a href="{{ route('reports.comparison') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('reports.comparison') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    Comparativo USA-MEX
                </a>
            @endif
        @endauth
        @auth
            <span class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 sm:ml-auto">{{ auth()->user()->name }} · {{ auth()->user()->roleLabel() }}</span>
            <form action="{{ route('logout') }}" method="POST" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:w-auto">Salir</button>
            </form>
        @endauth
        </div>
    </div>
</nav>
