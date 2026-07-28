@extends('layouts.app')

@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">{{ $team->team_name }}</h3>
                    <p class="text-muted small mb-0">Informasi tim dan daftar anggota</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route(auth()->user()->dashboardRoute()) }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ $team->team_name }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            {{-- ============ SECTION 1: RINGKASAN TIM ============ --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        {{-- Nama Team --}}
                        <div class="col-md-5">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-people-fill fs-4"></i>
                                </div>
                                <div>
                                    <span class="text-muted small d-block">Nama Tim</span>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $team->team_name }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1 d-none d-md-block">
                            <div class="vr h-100 opacity-10"></div>
                        </div>

                        {{-- Supervisor --}}
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-person-badge-fill fs-4"></i>
                                </div>
                                <div>
                                    <span class="text-muted small d-block">Supervisor / Penanggung Jawab</span>
                                    <h6 class="fw-semibold mb-0 text-dark">
                                        {{ $team->supervisor->name ?? 'Belum Ditentukan' }}</h6>
                                    @if (optional($team->supervisor)->email)
                                        <small class="text-muted">{{ $team->supervisor->email }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ SECTION 2: DAFTAR ANGGOTA TIM ============ --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div
                    class="card-header bg-transparent border-0 px-4 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Anggota Tim</h6>
                        <small class="text-muted">Daftar agen yang tergabung dalam tim ini</small>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-semibold">
                        {{ $agents->count() }} Anggota
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Nama Agen</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($agents as $index => $agent)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-semibold d-block text-dark">{{ $agent->name }}</span>
                                                    @if (auth()->id() === $agent->id)
                                                        <span
                                                            class="badge bg-info bg-opacity-10 text-info border border-info-subtle"
                                                            style="font-size: 0.65rem;">Anda</span>
                                                    @endif
                                            </div>
                                        </td>
                                        <td class="text-muted small">{{ $agent->email }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-people display-6 d-block text-black-50 mb-2"></i>
                                            Belum ada anggota di dalam tim ini.
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
