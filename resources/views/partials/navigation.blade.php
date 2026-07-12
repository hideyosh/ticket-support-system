<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">

        {{-- Sidebar Toggle --}}
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list fs-5"></i>
                </a>
            </li>
        </ul>

        {{-- Right Side --}}
        <ul class="navbar-nav ms-auto">

            {{-- Notifikasi Overdue --}}
            {{-- <li class="nav-item dropdown">
                <a class="nav-link" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="badge bg-danger navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
                    <div class="dropdown-header fw-semibold">Notifikasi</div>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-clock-history text-danger me-2"></i>
                        <span class="small">3 tiket overdue</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-person-x text-warning me-2"></i>
                        <span class="small">5 tiket unassigned</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('admin.tickets.index') }}" class="dropdown-item text-center small">
                        Lihat semua
                    </a>
                </div>
            </li> --}}

            {{-- User Dropdown --}}
            <li class="nav-item dropdown ms-2">
                <a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                         style="width: 30px; height: 30px; font-size: 13px;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="d-none d-md-inline small">{{ Auth::user()->name }}</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <div class="dropdown-header">
                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                            <div class="text-muted small">{{ Auth::user()->email }}</div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-2"></i> Profil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</nav>
