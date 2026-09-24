<nav class="relative z-30 mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm" aria-label="Navegación principal">
    <div class="flex min-h-14 items-center justify-between gap-3 p-2 sm:p-3 lg:hidden">
        <span class="text-xs font-bold uppercase tracking-[0.16em] text-blue-700 lg:hidden">MC Importaciones</span>
        <button type="button" id="navbar-toggle" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden" aria-controls="navbar-menu" aria-expanded="false">
            <span class="sr-only">Abrir navegación</span>
            <span aria-hidden="true" class="text-xl leading-none">☰</span>
        </button>
    </div>

    <div id="navbar-menu" class="hidden flex-col gap-1 border-t border-slate-100 p-2 lg:flex lg:flex-row lg:flex-wrap lg:items-center lg:gap-2 lg:border-t-0 lg:p-3">
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
        <a href="{{ route('reports.packages') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Reportería
        </a>
        <a href="{{ route('reports.comparison') }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs('reports.comparison') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            Comparativo USA-MEX
        </a>
        @auth
            <span class="ml-auto rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600">{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Salir</button>
            </form>
        @endauth
    </div>
</nav>

<script>
    (() => {
        const toggle = document.getElementById('navbar-toggle');
        const menu = document.getElementById('navbar-menu');

        if (!toggle || !menu) return;

        toggle.addEventListener('click', () => {
            const isOpen = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!isOpen));
            menu.classList.toggle('hidden', isOpen);
            menu.classList.toggle('flex', !isOpen);
        });
    })();
</script>
