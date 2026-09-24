<nav class="relative z-30 mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm" aria-label="Navegación principal">
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
        @auth
            @if (auth()->user()->role === 'administrador')
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
            <span class="ml-auto rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600">{{ auth()->user()->name }} · {{ auth()->user()->roleLabel() }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Salir</button>
            </form>
        @endauth
    </div>
</nav>
