@php
    $unreadCount = auth()->user()->unreadNotifications->count();
    $recentNotifications = auth()->user()->notifications()->latest()->take(5)->get();
@endphp

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

            {{-- Notifikasi --}}
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    @if ($unreadCount > 0)
                        <span class="badge bg-danger navbar-badge">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end"
                    style="min-width: 320px; max-height: 420px; overflow-y: auto;">
                    <div class="dropdown-header fw-semibold d-flex justify-content-between align-items-center">
                        <span>Notifikasi</span>
                        @if ($unreadCount > 0)
                            <a href="{{ route('notifications.read-all') }}"
                                onclick="event.preventDefault(); document.getElementById('mark-all-read-form').submit();"
                                class="small text-decoration-none">
                                Tandai semua dibaca
                            </a>
                            <form id="mark-all-read-form" action="{{ route('notifications.read-all') }}" method="POST"
                                class="d-none">
                                @csrf
                            </form>
                        @endif
                    </div>
                    <div class="dropdown-divider"></div>

                    @forelse ($recentNotifications as $notification)
                        <a href="{{ route('notifications.read', $notification->id) }}"
                            class="dropdown-item {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                            <i class="bi bi-bell-fill text-primary me-2"></i>
                            <span class="small">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</span>
                            <div class="text-muted" style="font-size: 0.7rem;">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </a>
                    @empty
                        <span class="dropdown-item-text text-muted small text-center d-block py-3">
                            Belum ada notifikasi.
                        </span>
                    @endforelse

                    <div class="dropdown-divider"></div>
                    <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="dropdown-item text-center small">
                        Lihat semua
                    </a>
                </div>
            </li>

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
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person me-2"></i> Profil
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
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
