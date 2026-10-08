<nav class="relative left-1/2 z-30 mb-6 w-screen -translate-x-1/2 border-y border-slate-200 bg-white shadow-sm sm:rounded-lg sm:border" aria-label="Navegación principal">
    <div class="flex flex-wrap items-center justify-end gap-2 px-4 py-3 sm:px-6 lg:px-8">
        <button type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-blue-700 bg-blue-600 px-3 text-sm font-semibold text-white hover:bg-blue-700 xl:hidden" data-navbar-toggle aria-controls="navbar-links" aria-expanded="false" aria-label="Abrir menú">
            <span class="flex flex-col gap-1" aria-hidden="true">
                <span class="h-0.5 w-5 bg-current"></span>
                <span class="h-0.5 w-5 bg-current"></span>
                <span class="h-0.5 w-5 bg-current"></span>
            </span>
            <span>Menú</span>
        </button>

        <div id="navbar-links" class="navbar-links w-full flex-col items-stretch gap-1 pt-3 xl:w-auto xl:flex-row xl:flex-wrap xl:items-center xl:justify-end xl:gap-2 xl:pt-0">
        <a href="{{ route('packages.index') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('packages.index') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
            Recepción USA
        </a>
        <a href="{{ route('packages.registered') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('packages.registered') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
            Paquetes registrados
        </a>
        <a href="{{ route('bodega.index') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('bodega.index') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
            Bodega
        </a>
        <a href="{{ route('documentacion.index') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('documentacion.index') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
            Documentación
        </a>
        <a href="{{ route('clients.index') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('clients.*') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
            Clientes
        </a>
        <a href="{{ route('partners.index') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('partners.*') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
            Socios
        </a>
        <a href="{{ route('transportadoras.index') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('transportadoras.*') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
            Transportadoras
        </a>
        @auth
            @if (auth()->user()->isAdministrator())
                <a href="{{ route('users.index') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('users.*') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                    Usuarios
                </a>
                <a href="{{ route('company-settings.edit') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('company-settings.*') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                    Datos de empresa
                </a>
            @endif
        @endauth
        <a href="{{ route('reports.packages') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('reports.packages') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
            Reportería
        </a>
        @auth
            @if (auth()->user()->canAccessDashboard())
                <a href="{{ route('reports.comparison') }}" class="w-full whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition xl:w-auto {{ request()->routeIs('reports.comparison') ? 'border-blue-600 text-blue-700 bg-blue-50' : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900' }}">
                    Comparativo USA-MEX
                </a>
            @endif
        @endauth
        @auth
            <div class="mt-2 flex items-center gap-3 border-t border-slate-200 pt-2 xl:ml-4 xl:mt-0 xl:border-l xl:border-t-0 xl:pl-4 xl:pt-0">
            <span class="flex items-center gap-2 text-sm text-slate-700">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-xs font-bold uppercase text-white">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <span class="leading-tight"><span class="block font-semibold">{{ auth()->user()->name }}</span><span class="block text-xs text-slate-500">{{ auth()->user()->roleLabel() }}</span></span>
            </span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="cursor-pointer rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Salir</button>
            </form>
            </div>
        @endauth
        </div>
    </div>
</nav>
