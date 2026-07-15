@extends('layouts.app')

@section('content')

<div class="app-content-header mb-3">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h3 class="mb-1 fw-bold">Detail Tiket</h3>
                <p class="text-muted small mb-0">Pantau informasi tiket dan kelola penugasan agent.</p>
            </div>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route(auth()->user()->dashboardRoute()) }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Tiket</a></li>
                <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 px-3 pt-3 pb-2">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h3 class="card-title fw-semibold mb-0">
                                <i class="bi bi-ticket-detailed-fill me-2"></i> Informasi Tiket
                            </h3>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge text-bg-primary">{{ $ticket->ticket_number }}</span>
                            <span class="badge text-bg-{{ $statusColorMap[$ticket->status] ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                            <span class="badge text-bg-{{ $ticket->priority ? 'warning' : 'secondary' }}">
                                {{ $ticket->priority->priority_name ?? '-' }}
                            </span>
                        </div>

                        <h2 class="h4 fw-bold mb-3">{{ $ticket->title }}</h2>
                        <p class="text-muted mb-4">{{ $ticket->description }}</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="text-muted small mb-1">Kategori</div>
                                    <div class="fw-semibold">{{ $ticket->category->category_name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="text-muted small mb-1">Prioritas</div>
                                    <div class="fw-semibold">{{ $ticket->priority->priority_name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="text-muted small mb-1">Dibuat oleh</div>
                                    <div class="fw-semibold">{{ $ticket->creator->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="text-muted small mb-1">Ditugaskan ke</div>
                                    <div class="fw-semibold">{{ $ticket->assignedAgent->name ?? 'Belum ditugaskan' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="text-muted small mb-1">Tenggat</div>
                                    <div class="fw-semibold">
                                        @if ($ticket->due_date)
                                            {{ $ticket->due_date->format('d M Y, H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="text-muted small mb-1">Label</div>
                                    <div class="fw-semibold">
                                        @forelse ($ticket->labels as $label)
                                            <span class="badge text-bg-light text-dark me-1 mb-1">{{ $label->label_name }}</span>
                                        @empty
                                            <span class="text-muted">Tidak ada label</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-transparent border-0 px-3 pt-3 pb-2">
                        <h3 class="card-title fw-semibold mb-0">
                            <i class="bi bi-person-workspace me-2"></i> Assign Agent
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <label for="assigned_to" class="form-label fw-semibold">Pilih agent</label>
                            <select name="assigned_to" id="assigned_to" class="form-select">
                                <option value="">Belum ditugaskan</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('assigned_to', $ticket->assigned_to) == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-check-fill me-1"></i> Simpan Assign
                                </button>
                            </div>
                        </form>

                        @if ($ticket->assignedAgent)
                            <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="assigned_to" value="">
                                <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-person-x-fill me-1"></i> Batalkan Assign
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 px-3 pt-3 pb-2">
                        <h3 class="card-title fw-semibold mb-0">
                            <i class="bi bi-arrow-left-right me-2"></i> Ubah Status
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <label for="status" class="form-label fw-semibold">Status tiket</label>
                            <select name="status" id="status" class="form-select">
                                @foreach ($allowedStatuses as $status)
                                    <option value="{{ $status }}" {{ $ticket->status === $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-repeat me-1"></i> Perbarui Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
