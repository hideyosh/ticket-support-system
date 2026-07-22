@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">{{ $team->team_name }}</h3>
                    <p class="text-muted small mb-0">Detail tim dan anggotanya</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('supervisor.teams.index') }}">Team</a></li>
                    <li class="breadcrumb-item active">{{ $team->team_name }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- ============ SECTION: INFORMASI TIM ============ --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-info-circle me-1"></i> Informasi Tim</h6>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3">
                                <p class="text-muted small mb-1">Nama Team</p>
                                <p class="fw-semibold mb-0">{{ $team->team_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3">
                                <p class="text-muted small mb-1">Supervisor</p>
                                <p class="fw-semibold mb-0">{{ $team->supervisor->name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3">
                                <p class="text-muted small mb-1">Dibuat</p>
                                <p class="fw-semibold mb-0">{{ $team->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ SECTION: DAFTAR ANGGOTA ============ --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-people me-1"></i> Daftar Anggota
                        <span class="text-muted fw-normal">({{ $agents->count() }})</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th class="text-center">Tiket Aktif</th>
                                <th class="text-center">Tiket Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agents as $index => $agent)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                                style="width:32px;height:32px;">
                                                <i class="bi bi-person text-muted small"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $agent->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $agent->email }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill text-bg-primary">
                                            {{ $agent->active_ticket_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill text-bg-success">
                                            {{ $agent->completed_ticket_count }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada anggota di tim ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ============ SECTION: TAMBAH ANGGOTA ============ --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-person-plus me-1"></i> Tambah Anggota</h6>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    @if ($availableAgents->isEmpty())
                        <p class="text-muted small mb-0">
                            Tidak ada agent yang tersedia untuk ditambahkan saat ini (semua agent sudah tergabung di tim
                            lain).
                        </p>
                    @else
                        <form action="{{ route('supervisor.teams.members.store', $team->id) }}" method="POST">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-md-8">
                                    <label for="agent_id" class="form-label small fw-semibold">Pilih Agent</label>
                                    <select id="agent_id" name="agent_id"
                                        class="form-select @error('agent_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih agent --</option>
                                        @foreach ($availableAgents as $agent)
                                            <option value="{{ $agent->id }}" @selected(old('agent_id') == $agent->id)>
                                                {{ $agent->name }} ({{ $agent->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('agent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                        <i class="bi bi-plus-lg me-1"></i> Tambahkan
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
