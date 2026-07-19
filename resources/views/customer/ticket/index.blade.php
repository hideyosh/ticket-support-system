@extends('layouts.app')

@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Tiket Saya</h3>
                    <p class="text-muted small mb-0">Lihat dan pantau tiket support Anda.</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route(auth()->user()->dashboardRoute()) }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tiket</li>
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

            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h3 class="card-title mb-0 fw-semibold">Filter Tiket</h3>
                        <a href="{{ route('customer.tickets.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg"></i> Buat Tiket
                        </a>
                    </div>
                </div>
                <div class="card-body px-3 pb-3 pt-2">
                    <form method="GET" action="{{ route('customer.tickets.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label form-label-sm mb-1">Cari</label>
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="No. tiket atau judul..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label form-label-sm mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                @foreach (['open', 'assigned', 'in_progress', 'waiting_for_customer', 'resolved', 'closed', 'reopened', 'escalated'] as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label form-label-sm mb-1">Prioritas</label>
                            <select name="priority_id" class="form-select form-select-sm">
                                <option value="">Semua Prioritas</option>
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->id }}" {{ request('priority_id') == $priority->id ? 'selected' : '' }}>
                                        {{ $priority->priority_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No. Tiket</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th>Tenggat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('customer.tickets.show', $ticket) }}" class="text-decoration-none">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($ticket->title, 60) }}</td>
                                    <td>{{ $ticket->category->category_name ?? '-' }}</td>
                                    <td>{{ $ticket->priority->priority_name ?? '-' }}</td>
                                    <td>
                                        <span class="badge text-bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($ticket->due_date)
                                            {{ $ticket->due_date->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('customer.tickets.show', $ticket) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        Tidak ada tiket ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
