@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Detail Team</h3>
                    <p class="text-muted small mb-0">Informasi lengkap tim support dan anggotanya</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}">Team</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0"><i class="bi bi-info-circle me-1 text-primary"></i> Informasi Team</h5>
                        <div>
                            <a href="{{ route('admin.teams.edit', $team->id) }}"
                                class="btn btn-warning btn-sm rounded-pill px-3 me-2">
                                <i class="bi bi-pencil-square me-1"></i> Edit Team
                            </a>
                            <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <p class="text-muted small mb-1">Nama Team</p>
                                <p class="fw-bold mb-0 text-dark fs-5">{{ $team->team_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <p class="text-muted small mb-1">Supervisor</p>
                                @if ($team->supervisor)
                                    <p class="fw-bold mb-0 text-dark fs-5">{{ $team->supervisor->name }}</p>
                                    <span class="text-muted small">{{ $team->supervisor->email }}</span>
                                @else
                                    <p class="text-muted mb-0 fst-italic">Belum memiliki supervisor</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2">
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-people me-1 text-success"></i> Daftar Agent Terdaftar
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 70px;">#</th>
                                    <th>Nama Agent</th>
                                    <th>Email</th>
                                    <th class="pe-4">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($team->agents as $index => $agent)
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    <a href="{{ route('admin.users.show', $agent->id) }}"
                                                        class="fw-semibold text-decoration-none text-dark">
                                                        {{ $agent->name }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted font-monospace small">{{ $agent->email }}</td>
                                        <td class="pe-4">
                                            <span
                                                class="badge rounded-pill text-bg-success-subtle text-success border border-success-subtle px-3">
                                                {{ ucFirst($agent->role->role_name) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="bi bi-person-x fs-1 d-block mb-2 opacity-50"></i>
                                            Belum ada agent yang bergabung dalam tim ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
