@extends('layouts.app')

@section('content')

    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Tiket Ditugaskan ke Saya</h3>
                    <p class="text-muted small mb-0">Daftar seluruh tiket support yang ditugaskan kepada Anda</p>
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

            {{-- Flash Messages --}}
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

            {{-- Filter Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                    <h3 class="card-title fw-semibold mb-0">
                        <i class="bi bi-funnel me-1"></i> Filter Tiket
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body px-3 pb-3 pt-2">
                    <form method="GET" action="{{ route('agent.tickets.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label form-label-sm mb-1">Cari</label>
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="No. tiket atau judul..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                @foreach (['open', 'assigned', 'in_progress', 'waiting_for_customer', 'resolved', 'closed', 'reopened', 'escalated'] as $s)
                                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm mb-1">Prioritas</label>
                            <select name="priority_id" class="form-select form-select-sm">
                                <option value="">Semua Prioritas</option>
                                @foreach ($priorities as $p)
                                    <option value="{{ $p->id }}"
                                        {{ request('priority_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->priority_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm mb-1">Kategori</label>
                            <select name="category_id" class="form-select form-select-sm">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}"
                                        {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-sm me-1">
                                <i class="bi bi-search"></i> Terapkan
                            </button>
                            <a href="{{ route('agent.tickets.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel Tiket --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h3 class="card-title mb-0 fw-semibold">
                            Daftar Tiket Ditugaskan
                        </h3>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">No. Tiket</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th>Dibuat oleh</th>
                                    <th class="text-nowrap">Tenggat</th>
                                    <th style="width: 100px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td class="text-nowrap">
                                            <a href="{{ route('agent.tickets.show', $ticket) }}"
                                                class="fw-semibold text-decoration-none font-monospace">
                                                {{ $ticket->ticket_number }}
                                            </a>
                                        </td>
                                        <td>{{ Str::limit($ticket->title, 45) }}</td>

                                        <td>{{ $ticket->category->category_name ?? '-' }}</td>

                                        <td>
                                            @php
                                                $priorityColors = [
                                                    'Low' => 'success',
                                                    'Medium' => 'info',
                                                    'High' => 'warning',
                                                    'Critical' => 'danger',
                                                ];
                                                $pName = $ticket->priority->priority_name ?? '-';
                                                $pColor = $priorityColors[$pName] ?? 'secondary';
                                            @endphp
                                            <span class="badge text-bg-{{ $pColor }}">{{ $pName }}</span>
                                        </td>

                                        <td>
                                            @php
                                                $statusColors = [
                                                    'open' => 'secondary',
                                                    'assigned' => 'info',
                                                    'in_progress' => 'warning',
                                                    'waiting_for_customer' => 'secondary',
                                                    'resolved' => 'success',
                                                    'closed' => 'dark',
                                                    'reopened' => 'danger',
                                                    'escalated' => 'danger',
                                                ];
                                                $sColor = $statusColors[$ticket->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge text-bg-{{ $sColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                        </td>

                                        <td>{{ $ticket->creator->name ?? '-' }}</td>

                                        <td class="text-nowrap">
                                            @if ($ticket->due_date)
                                                @if ($ticket->due_date < now() && !in_array($ticket->status, ['resolved', 'closed']))
                                                    <span class="text-danger fw-semibold">
                                                        {{ $ticket->due_date->format('d/m/Y H:i') }}
                                                        <i class="bi bi-exclamation-circle-fill"></i>
                                                    </span>
                                                @else
                                                    {{ $ticket->due_date->format('d/m/Y H:i') }}
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td class="text-center text-nowrap">
                                            <a href="{{ route('agent.tickets.show', $ticket) }}"
                                                class="btn btn-info btn-sm" title="Detail">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                            Tidak ada tiket ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    {{ $tickets->links() }}
                </div>
            </div>

        </div>
    </div>

@endsection
