<nav class="navbar navbar-expand-xl bg-white rounded border shadow-sm mb-3" aria-label="Navegación principal">
    <div class="container-fluid g-2">
        @auth
            @if (auth()->user()->canSearchGuides())
                <form action="{{ route('packages.search') }}" method="GET" role="search" class="d-flex flex-grow-1 gap-2 order-1 order-xl-0" style="max-width: 24rem">
                    <input type="search" name="q" value="{{ request()->routeIs('packages.search') ? request('q') : '' }}" maxlength="255" placeholder="Buscar guía" aria-label="Buscar guía" class="form-control">
                    <button type="submit" class="btn btn-primary flex-shrink-0">Buscar</button>
                </form>
            @endif
        @endauth

        <button class="navbar-toggler order-0 order-xl-1 ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-links" aria-controls="navbar-links" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-2" id="navbar-links">
            @auth
                @php
                    $operativoActive = request()->routeIs('packages.index', 'packages.registered', 'bodega.index', 'documentacion.*');
                    $administrativoActive = request()->routeIs('clients.*', 'partners.*', 'transportadoras.*', 'users.*', 'company-settings.*', 'reports.*', 'activity-logs.*');
                    $user = auth()->user();
                @endphp

                <ul class="navbar-nav ms-auto align-items-xl-center gap-xl-1">
                    @if ($user->canAccessUsaWarehouse() || $user->canAccessMexWarehouse() || $user->canAccessDocumentation())
                        <li class="nav-item dropdown">
                            <button @class(['nav-link dropdown-toggle', 'active' => $operativoActive]) type="button" data-bs-toggle="dropdown" aria-expanded="false">Operativo</button>
                            <ul class="dropdown-menu">
                                @if ($user->canAccessUsaWarehouse())
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('packages.index')]) href="{{ route('packages.index') }}">Recepción USA</a></li>
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('packages.registered')]) href="{{ route('packages.registered') }}">Paquetes registrados</a></li>
                                @endif
                                @if ($user->canAccessMexWarehouse())
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('bodega.index')]) href="{{ route('bodega.index') }}">Bodega</a></li>
                                @endif
                                @if ($user->canAccessDocumentation())
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('documentacion.*')]) href="{{ route('documentacion.index') }}">Documentación</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if ($user->canAccessAdministration() || $user->canAccessPackageReports() || $user->canAccessDashboard())
                        <li class="nav-item dropdown">
                            <button @class(['nav-link dropdown-toggle', 'active' => $administrativoActive]) type="button" data-bs-toggle="dropdown" aria-expanded="false">Administración y reportes</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if ($user->canAccessAdministration())
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('clients.*')]) href="{{ route('clients.index') }}">Clientes</a></li>
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('partners.*')]) href="{{ route('partners.index') }}">Socios</a></li>
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('transportadoras.*')]) href="{{ route('transportadoras.index') }}">Transportadoras</a></li>
                                @endif
                                @if ($user->isAdministrator())
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('users.*')]) href="{{ route('users.index') }}">Usuarios</a></li>
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('activity-logs.*')]) href="{{ route('activity-logs.index') }}">Historial de cambios</a></li>
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('company-settings.*')]) href="{{ route('company-settings.edit') }}">Datos de empresa</a></li>
                                @endif
                                @if ($user->canAccessPackageReports())
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('reports.packages')]) href="{{ route('reports.packages') }}">Reportería</a></li>
                                @endif
                                @if ($user->canAccessDashboard())
                                    <li><a @class(['dropdown-item', 'active' => request()->routeIs('reports.comparison')]) href="{{ route('reports.comparison') }}">Comparativo USA-MEX</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <li class="nav-item d-xl-flex align-items-xl-center g-2 border-top pt-2 mt-2 border-xl-0 pt-xl-0 mt-xl-0 ms-xl-3">
                        <span class="d-flex align-items-center gap-2 text-body-secondary small">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold" style="width: 2rem; height: 2rem">{{ mb_substr($user->name, 0, 1) }}</span>
                            <span class="lh-sm"><span class="d-block fw-semibold text-body">{{ $user->name }}</span><span class="d-block">{{ $user->roleLabel() }}</span></span>
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="mt-2 mt-xl-0">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Salir</button>
                        </form>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>
