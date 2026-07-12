<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

    {{-- Brand --}}
    <div class="sidebar-brand">
        {{-- <a href="{{ route('admin.dashboard') }}" class="brand-link"> --}}
        <a href="#" class="brand-link">
            <span class="brand-text fw-semibold">My Helpdesk</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                {{-- Dashboard --}}
                <li class="nav-item">
                    {{-- <a href="{{ route('admin.dashboard') }}" --}}
                    <a href="{{ route(auth()->user()->dashboardRoute()) }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- ── MANAJEMEN TIKET ── --}}
                <li class="nav-header">MANAJEMEN TIKET</li>
                <li class="nav-item {{ request()->routeIs('admin.tickets.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-ticket-perforated"></i>
                        <p>
                            Manajemen Tiket
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- <a href="{{ route('admin.tickets.index') }}" --}}
                            <a href="#"
                                class="nav-link {{ request()->routeIs('admin.tickets.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Tiket</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ── DATA MASTER ── --}}
                <li class="nav-header">DATA MASTER</li>
                <li
                    class="nav-item {{ request()->routeIs('admin.categories.*', 'admin.labels.*', 'admin.priorities.*', 'admin.sla-rules.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('admin.categories.*', 'admin.labels.*', 'admin.priorities.*', 'admin.sla-rules.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-database"></i>
                        <p>
                            Data Master
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.categories.index') }}"
                                class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Kategori</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.labels.index') }}"
                                class="nav-link {{ request()->routeIs('admin.labels.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Label</p>
                                <a />
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.priorities.index') }}"
                                class="nav-link {{ request()->routeIs('admin.priorities.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Prioritas</p>
                                <a />
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.sla-rules.index') }}"
                                class="nav-link {{ request()->routeIs('admin.sla-rules.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Aturan SLA</p>
                                <a />
                        </li>
                    </ul>
                </li>

                {{-- ── MANAJEMEN PENGGUNA ── --}}
                <li class="nav-header">MANAJEMEN USE</li>
                <li
                    class="nav-item {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.teams.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.teams.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>
                            Manajemen User
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Pengguna</p>
                                <a />
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}"
                                class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Role</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- <a href="{{ route('admin.teams.index') }}" --}}
                            <a href="#"
                                class="nav-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Tim</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ── SISTEM ── --}}
                <li class="nav-header">SISTEM</li>
                <li
                    class="nav-item {{ request()->routeIs('admin.activity-logs.*', 'admin.settings.*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->routeIs('admin.activity-logs.*', 'admin.settings.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-gear-wide-connected"></i>
                        <p>
                            Sistem
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            {{-- <a href="{{ route('admin.activity-logs.index') }}" --}}
                            <a href="#"
                                class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Activity logs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- <a href="{{ route('admin.settings.index') }}" --}}
                            <a href="#"
                                class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Pengaturan</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>

</aside>
