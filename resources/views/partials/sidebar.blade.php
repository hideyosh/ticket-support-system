<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="brand-link">
            <span class="brand-text fw-semibold">My Helpdesk</span>
        </a>
    </div>

    @php
        $user = auth()->user();
    @endphp

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route($user->dashboardRoute()) }}"
                        class="nav-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- ══════════════ CUSTOMER ══════════════ --}}
                @if ($user->role->role_name === 'customer')
                    <li class="nav-header">TIKET</li>
                    <li class="nav-item">
                        <a href="{{ route('customer.tickets.index') }}"
                            class="nav-link {{ request()->routeIs('customer.tickets.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-ticket-perforated"></i>
                            <p>Tiket Saya</p>
                        </a>
                    </li>
                @endif

                {{-- ══════════════ AGENT ══════════════ --}}
                @if ($user->role->role_name === 'agent')
                    <li class="nav-header">TIKET</li>
                    <li class="nav-item">
                        <a href="{{ route('agent.tickets.index') }}"
                            class="nav-link {{ request()->routeIs('agent.tickets.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-ticket-perforated"></i>
                            <p>Tiket Ditugaskan ke Saya</p>
                        </a>
                    </li>
                @endif

                {{-- ══════════════ SUPERVISOR ══════════════ --}}
                @if ($user->role->role_name === 'supervisor')
                    <li class="nav-header">MANAJEMEN TIKET</li>
                    <li class="nav-item {{ request()->routeIs('supervisor.tickets.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('supervisor.tickets.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-ticket-perforated"></i>
                            <p>
                                Tiket Tim
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('supervisor.tickets.index') }}"
                                    class="nav-link {{ request()->routeIs('supervisor.tickets.index') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Semua Tiket Tim</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('supervisor.tickets.overdue') }}"
                                    class="nav-link {{ request()->routeIs('supervisor.tickets.overdue') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Tiket Overdue</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('supervisor.tickets.escalated') }}"
                                    class="nav-link {{ request()->routeIs('supervisor.tickets.escalated') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Tiket Eskalasi</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-header">LAPORAN</li>
                    <li class="nav-item">
                        <a href="{{ route('supervisor.reports.dashboard') }}"
                            class="nav-link {{ request()->routeIs('supervisor.reports.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-bar-chart"></i>
                            <p>Dashboard Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('supervisor.reports.export') }}"
                            class="nav-link {{ request()->routeIs('supervisor.reports.export') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-download"></i>
                            <p>Export Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('supervisor.logs.index') }}"
                            class="nav-link {{ request()->routeIs('supervisor.logs.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-clock-history"></i>
                            <p>Log Tim</p>
                        </a>
                    </li>
                @endif

                {{-- ══════════════ ADMIN ONLY ══════════════ --}}
                @if ($user->role->role_name === 'admin')
                    {{-- MANAJEMEN TIKET --}}
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
                                <a href="{{ route('admin.tickets.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.tickets.index') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Tiket</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- DATA MASTER --}}
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
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.priorities.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.priorities.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Prioritas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.sla-rules.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.sla-rules.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Aturan SLA</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- MANAJEMEN USER --}}
                    <li class="nav-header">MANAJEMEN USER</li>
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
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.roles.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Role</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.teams.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Tim</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- SISTEM --}}
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
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Activity logs</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Pengaturan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                    
            </ul>
        </nav>
    </div>

</aside>
