@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Detail Activity Log</h3>
                    <p class="text-muted small mb-0">Log #{{ $activityLog->id }}</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.activity-logs.index') }}">Activity Logs</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-primary-subtle text-primary-emphasis fs-6 px-3 py-2">
                    <i class="bi bi-clock-history me-1"></i>
                    {{ $activityLog->action }}
                </span>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali ke daftar
                </a>
            </div>

            <div class="row g-3">

                {{-- Info Waktu & Field --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                            <h3 class="card-title mb-0 fw-semibold">
                                <i class="bi bi-info-circle me-1"></i> Informasi Log
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-muted small mb-1">Waktu</div>
                                    <div class="fw-semibold">{{ $activityLog->created_at->format('d M Y, H:i:s') }}</div>
                                    <div class="text-muted small">{{ $activityLog->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small mb-1">Aksi</div>
                                    <div class="fw-semibold">{{ $activityLog->action }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small mb-1">Field yang Diubah</div>
                                    <div class="fw-semibold">
                                        {{ $activityLog->field ?? '— (aksi ini tidak mengubah field spesifik)' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pelaku --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                            <h3 class="card-title mb-0 fw-semibold">
                                <i class="bi bi-person-circle me-1"></i> Pelaku
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($activityLog->user)
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary-subtle text-primary-emphasis rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        style="width:48px;height:48px;font-size:1.1rem;">
                                        {{ strtoupper(substr($activityLog->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $activityLog->user->name }}</div>
                                        <div class="text-muted small">{{ $activityLog->user->email }}</div>
                                        @if ($activityLog->user->role)
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis mt-1">
                                                {{ $activityLog->user->role->role_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <p class="text-muted mb-0">User sudah tidak ada / terhapus dari sistem.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Ticket Terkait --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                            <h3 class="card-title mb-0 fw-semibold">
                                <i class="bi bi-ticket-detailed me-1"></i> Ticket Terkait
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($activityLog->ticket)
                                <div class="fw-semibold mb-1">
                                    #{{ $activityLog->ticket->id }} — {{ $activityLog->ticket->title }}
                                </div>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if ($activityLog->ticket->status)
                                        <span class="badge bg-info-subtle text-info-emphasis">
                                            {{ ucfirst($activityLog->ticket->status) }}
                                        </span>
                                    @endif
                                    @if ($activityLog->ticket->priority)
                                        <span class="badge bg-warning-subtle text-warning-emphasis">
                                            {{ $activityLog->ticket->priority->priority_name }}
                                        </span>
                                    @endif
                                    @if ($activityLog->ticket->category)
                                        <span class="badge bg-light text-dark border">
                                            {{ $activityLog->ticket->category->category_name }}
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('supervisor.tickets.show', $activityLog->ticket) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="bi bi-box-arrow-up-right"></i> Buka Ticket
                                </a>
                            @else
                                <p class="text-muted mb-0">Ticket sudah dihapus dari sistem.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Detail Perubahan --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                            <h3 class="card-title mb-0 fw-semibold">
                                <i class="bi bi-arrow-left-right me-1"></i> Detail Perubahan Nilai
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($activityLog->field)
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="text-muted small mb-1">Nilai Lama</div>
                                        <div class="p-3 bg-danger-subtle text-danger-emphasis rounded-3 font-monospace">
                                            {{ $activityLog->old_value ?? '(kosong)' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted small mb-1">Nilai Baru</div>
                                        <div class="p-3 bg-success-subtle text-success-emphasis rounded-3 font-monospace">
                                            {{ $activityLog->new_value ?? '(kosong)' }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted mb-0">
                                    Aksi ini tidak menyimpan perubahan nilai spesifik (misal: penambahan komentar atau lampiran).
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
