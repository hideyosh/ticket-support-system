@extends('layouts.app')
@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3 class="mb-0">Team</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Team</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0"><i class="fas fa-users-cog me-1"></i> Daftar Team</h3>
                <a href="{{ route('admin.teams.create') }}" class="btn btn-primary btn-sm text-nowrap">
                    <i class="fas fa-plus"></i> Tambah Team
                </a>
            </div>
            <div class="card-body p-0">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible m-3">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        {{ session('success') }}
                    </div>
                @endif
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Nama Team</th>
                            <th>Supervisor</th>
                            <th style="width: 180px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $team)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + ($teams->currentPage() - 1) * $teams->perPage() }}</td>
                                <td>{{ $team->team_name }}</td>
                                <td>
                                    @if($team->supervisor)
                                        {{ $team->supervisor->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.teams.show', $team->id) }}" class="btn btn-info btn-sm" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus team ini?');">
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
                                <td colspan="4" class="text-center">Belum ada data team.</td>
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
