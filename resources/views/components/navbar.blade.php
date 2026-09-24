<nav class="relative z-30 mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm" aria-label="Navegación principal">
    <div id="navbar-mobile-header" class="flex min-h-14 items-center justify-between gap-3 p-2 sm:p-3 md:hidden">
        <span class="text-xs font-bold uppercase tracking-[0.16em] text-blue-700 md:hidden">MC Importaciones</span>
        <button type="button" id="navbar-toggle" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 md:hidden" aria-controls="navbar-menu" aria-expanded="false">
            <span class="sr-only">Abrir navegación</span>
            <span aria-hidden="true" class="text-xl leading-none">☰</span>
        </button>
    </div>

    <div id="navbar-menu" class="hidden flex-col gap-1 border-t border-slate-100 p-2 md:flex md:flex-row md:flex-wrap md:items-center md:gap-2 md:border-t-0 md:p-3">
        <div id="navbar-links" class="grid min-w-0 grid-cols-2 gap-1 sm:grid-cols-3 md:grid-cols-4 lg:flex lg:flex-1 lg:flex-wrap lg:items-center lg:gap-2">
        <a href="{{ route('packages.index') }}" class="min-w-0 rounded-lg px-2 py-2 text-center text-sm font-semibold {{ request()->routeIs('packages.index') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} lg:px-3">
            Recepción USA
        </a>
        <a href="{{ route('packages.registered') }}" class="min-w-0 rounded-lg px-2 py-2 text-center text-sm font-semibold {{ request()->routeIs('packages.registered') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} lg:px-3">
            Paquetes registrados
        </a>
        <a href="{{ route('bodega.index') }}" class="min-w-0 rounded-lg px-2 py-2 text-center text-sm font-semibold {{ request()->routeIs('bodega.index') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} lg:px-3">
            Bodega
        </a>
        <a href="{{ route('documentacion.index') }}" class="min-w-0 rounded-lg px-2 py-2 text-center text-sm font-semibold {{ request()->routeIs('documentacion.index') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} lg:px-3">
            Documentación
        </a>
        <a href="{{ route('clients.index') }}" class="min-w-0 rounded-lg px-2 py-2 text-center text-sm font-semibold {{ request()->routeIs('clients.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} lg:px-3">
            Clientes
        </a>
        <a href="{{ route('partners.index') }}" class="min-w-0 rounded-lg px-2 py-2 text-center text-sm font-semibold {{ request()->routeIs('partners.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} lg:px-3">
            Socios
        </a>
        @auth
            @if (auth()->user()->role === 'administrador')
                <a href="{{ route('users.index') }}" class="min-w-0 rounded-lg px-2 py-2 text-center text-sm font-semibold {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} lg:px-3">
                    Usuarios
                </a>
            @endif
        @endauth
        <a href="{{ route('reports.packages') }}" class="min-w-0 rounded-lg px-2 py-2 text-center text-sm font-semibold {{ request()->routeIs('reports.packages') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} lg:px-3">
            Reportería
        </a>
        <a href="{{ route('reports.comparison') }}" class="min-w-0 rounded-lg px-2 py-2 text-center text-sm font-semibold {{ request()->routeIs('reports.comparison') ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} lg:px-3">
            Comparativo USA-MEX
        </a>
        </div>
        @auth
            <div class="flex min-w-0 flex-wrap items-center gap-2 border-t border-slate-100 pt-2 md:border-t-0 md:pt-0 lg:shrink-0">
            <span class="min-w-0 rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600">{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Salir</button>
            </form>
            </div>
        @endauth
    </div>
</nav>

<style>
    @media (min-width: 768px) {
        #navbar-mobile-header,
        #navbar-toggle {
            display: none !important;
        }

        #navbar-menu {
            display: flex !important;
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
        }
    }
</style>

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
