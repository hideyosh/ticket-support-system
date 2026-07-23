@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Team</h3>
                    <p class="text-muted small mb-0">Kelola pembagian tim support</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Team</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0 fw-semibold"><i class="bi bi-people-fill me-1"></i> Daftar Team</h3>
                        <a href="{{ route('admin.teams.create') }}" class="btn btn-primary btn-sm text-nowrap">
                            <i class="fas fa-plus"></i> Tambah Team
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Team</th>
                                <th>Supervisor</th>
                                <th class="text-center">Jumlah Anggota</th>
                                <th class="text-center">Total Tiket Dikerjakan</th>
                                <th style="width: 180px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teams as $team)
                                <tr>
                                    <td class="text-center">
                                        {{ $loop->iteration + ($teams->currentPage() - 1) * $teams->perPage() }}</td>
                                    <td>{{ $team->team_name }}</td>
                                    <td>{{ $team->supervisor->name }}</td>
                                    <td class="text-center">{{ $team->agents->count() }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill text-bg-success">
                                            {{ $team->completed_tickets_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.teams.show', $team->id) }}" class="btn btn-info btn-sm"
                                            title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-warning btn-sm"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus team ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8  " class="text-center">Belum ada data team.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if (method_exists($teams, 'hasPages') && $teams->hasPages())
                    <div class="card-footer">
                        {{ $teams->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
