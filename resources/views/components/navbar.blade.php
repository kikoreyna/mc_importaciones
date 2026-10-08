<nav class="relative left-1/2 z-30 mb-6 w-screen -translate-x-1/2 border-y border-slate-200 bg-white shadow-sm sm:rounded-lg sm:border" aria-label="Navegación principal">
    <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 sm:px-6 lg:px-8">
        @auth
            @if (auth()->user()->isAdministrator())
            <form action="{{ route('reports.packages') }}" method="GET" role="search" class="flex min-w-0 flex-1 items-center gap-2 sm:max-w-sm xl:flex-none xl:basis-80">
                <input type="search" name="guia_principal" value="{{ request()->routeIs('reports.packages') ? request('guia_principal') : '' }}" maxlength="255" placeholder="Buscar guía" aria-label="Buscar guía" class="h-10 min-w-0 flex-1 rounded-lg border border-slate-300 bg-slate-50 px-3 text-sm placeholder:text-slate-400 focus:border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="h-10 shrink-0 cursor-pointer rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Buscar</button>
            </form>
            @endif
        @endauth
        <button type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-blue-700 bg-blue-600 px-3 text-sm font-semibold text-white hover:bg-blue-700 xl:hidden" data-navbar-toggle aria-controls="navbar-links" aria-expanded="false" aria-label="Abrir menú">
            <span class="flex flex-col gap-1" aria-hidden="true">
                <span class="h-0.5 w-5 bg-current"></span>
                <span class="h-0.5 w-5 bg-current"></span>
                <span class="h-0.5 w-5 bg-current"></span>
            </span>
            <span>Menú</span>
        </button>

        <div id="navbar-links" class="navbar-links w-full flex-col items-stretch gap-1 pt-3 xl:w-auto xl:flex-1 xl:flex-row xl:flex-wrap xl:items-center xl:justify-end xl:gap-2 xl:pt-0">
        @php
            $operativoActive = request()->routeIs('packages.index', 'packages.registered', 'bodega.index', 'documentacion.*');
            $administrativoActive = request()->routeIs('clients.*', 'partners.*', 'transportadoras.*', 'users.*', 'company-settings.*', 'reports.*');
            $groupButton = 'flex w-full cursor-pointer list-none items-center justify-between gap-2 whitespace-nowrap border-b-2 px-3 py-2 text-sm font-medium transition marker:hidden [&::-webkit-details-marker]:hidden xl:w-auto ';
            $groupOn = 'border-blue-600 bg-blue-50 text-blue-700';
            $groupOff = 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900';
            $panel = 'mt-1 flex flex-col gap-0.5 pl-3 xl:absolute xl:left-0 xl:top-full xl:z-40 xl:mt-2 xl:min-w-56 xl:rounded-xl xl:border xl:border-slate-200 xl:bg-white xl:p-1.5 xl:shadow-lg';
            $item = fn (bool $active) => 'block whitespace-nowrap rounded-lg px-3 py-2 text-sm transition ' . ($active ? 'bg-blue-50 font-semibold text-blue-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900');
        @endphp

        <details class="relative w-full xl:w-auto" data-nav-group>
            <summary class="{{ $groupButton }}{{ $operativoActive ? $groupOn : $groupOff }}">
                Operativo
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
            </summary>
            <div class="{{ $panel }}">
                <a href="{{ route('packages.index') }}" class="{{ $item(request()->routeIs('packages.index')) }}">Recepción USA</a>
                <a href="{{ route('packages.registered') }}" class="{{ $item(request()->routeIs('packages.registered')) }}">Paquetes registrados</a>
                <a href="{{ route('bodega.index') }}" class="{{ $item(request()->routeIs('bodega.index')) }}">Bodega</a>
                <a href="{{ route('documentacion.index') }}" class="{{ $item(request()->routeIs('documentacion.*')) }}">Documentación</a>
            </div>
        </details>

        <details class="relative w-full xl:w-auto" data-nav-group>
            <summary class="{{ $groupButton }}{{ $administrativoActive ? $groupOn : $groupOff }}">
                Administrativo
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
            </summary>
            <div class="{{ $panel }}">
                <a href="{{ route('clients.index') }}" class="{{ $item(request()->routeIs('clients.*')) }}">Clientes</a>
                <a href="{{ route('partners.index') }}" class="{{ $item(request()->routeIs('partners.*')) }}">Socios</a>
                <a href="{{ route('transportadoras.index') }}" class="{{ $item(request()->routeIs('transportadoras.*')) }}">Transportadoras</a>
                @auth
                    @if (auth()->user()->isAdministrator())
                        <a href="{{ route('users.index') }}" class="{{ $item(request()->routeIs('users.*')) }}">Usuarios</a>
                        <a href="{{ route('company-settings.edit') }}" class="{{ $item(request()->routeIs('company-settings.*')) }}">Datos de empresa</a>
                    @endif
                @endauth
                @auth
                    @if (auth()->user()->isAdministrator())
                        <a href="{{ route('reports.packages') }}" class="{{ $item(request()->routeIs('reports.packages')) }}">Reportería</a>
                    @endif
                    @if (auth()->user()->canAccessDashboard())
                        <a href="{{ route('reports.comparison') }}" class="{{ $item(request()->routeIs('reports.comparison')) }}">Comparativo USA-MEX</a>
                    @endif
                @endauth
            </div>
        </details>
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
