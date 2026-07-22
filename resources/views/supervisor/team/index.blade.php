@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Tim Saya</h3>
                    <p class="text-muted small mb-0">Tim yang kamu supervisi</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tim</li>
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
                        <a href="{{ route('supervisor.teams.create') }}" class="btn btn-primary btn-sm text-nowrap">
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
                                <th class="text-center">Total Tiket Dikerjakan</th>
                                <th style="width: 160px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($teams as $index => $team)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $team->team_name }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill text-bg-success">
                                            {{ $team->completed_ticket_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('supervisor.teams.show', $team->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Kamu belum ditugaskan sebagai supervisor tim manapun.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
