@extends('layouts.app')

@section('content')

    @php
        $activeFilterKeys = ['status', 'priority_id', 'category_id', 'label_id'];
        $activeFilterCount = 0;
        foreach ($activeFilterKeys as $key) {
            if (request()->filled($key)) {
                $activeFilterCount++;
            }
        }

        $currentSortBy = request('sort_by', 'created_at');
        $currentSortOrder = request('sort_order', 'desc');

        function sortCustomerUrl($column) {
            $params = request()->all();
            $params['sort_by'] = $column;
            $params['sort_order'] = (request('sort_by') === $column && request('sort_order', 'desc') === 'asc') ? 'desc' : 'asc';
            return request()->url() . '?' . http_build_query($params);
        }
    @endphp

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

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Filament Style Table Container --}}
            <div class="card border-0 shadow-sm rounded-4">
                {{-- Table Toolbar Header --}}
                <div class="card-header bg-white border-bottom-0 p-3 rounded-top-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="fw-bold mb-0 text-dark me-2 fs-5">Daftar Tiket Saya</h4>
                            <a href="{{ route('customer.tickets.create') }}" class="btn btn-primary btn-sm rounded-3 px-3">
                                <i class="bi bi-plus-lg me-1"></i> Buat Tiket
                            </a>
                        </div>

                        {{-- Search & Filter Controls --}}
                        <form method="GET" action="{{ route('customer.tickets.index') }}" id="customerFilterForm" class="d-flex align-items-center gap-2 mb-0 ms-auto">
                            <input type="hidden" name="sort_by" value="{{ $currentSortBy }}">
                            <input type="hidden" name="sort_order" value="{{ $currentSortOrder }}">

                            {{-- Search Input  --}}
                            <div class="input-group input-group-sm rounded-pill overflow-hidden border bg-light" style="width: 260px;">
                                <span class="input-group-text bg-transparent border-0 pe-1 text-muted ps-3">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none ps-1"
                                    placeholder="Search..." value="{{ request('search') }}" onchange="document.getElementById('customerFilterForm').submit()">
                            </div>

                            {{-- Filter Popover --}}
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 position-relative d-inline-flex align-items-center justify-content-center p-2"
                                        id="filamentFilterBtnCustomer" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="width: 36px; height: 36px;">
                                    <i class="bi bi-funnel fs-6"></i>
                                    @if($activeFilterCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 9px; padding: 2px 5px;">
                                            {{ $activeFilterCount }}
                                        </span>
                                    @endif
                                </button>

                                <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0 rounded-4 mt-2" style="width: 360px; max-width: 90vw; z-index: 1050;" aria-labelledby="filamentFilterBtnCustomer">
                                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                        <h6 class="fw-bold mb-0 text-dark">Filters</h6>
                                        <a href="{{ route('customer.tickets.index') }}" class="text-danger text-decoration-none small fw-semibold">Reset</a>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label form-label-sm fw-medium text-secondary mb-1">Status</label>
                                            <select name="status" class="form-select form-select-sm rounded-3">
                                                <option value="">All</option>
                                                @foreach (['open', 'assigned', 'in_progress', 'waiting_for_customer', 'resolved', 'closed', 'reopened', 'escalated'] as $s)
                                                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-6">
                                            <label class="form-label form-label-sm fw-medium text-secondary mb-1">Prioritas</label>
                                            <select name="priority_id" class="form-select form-select-sm rounded-3">
                                                <option value="">All</option>
                                                @foreach ($priorities as $p)
                                                    <option value="{{ $p->id }}" {{ request('priority_id') == $p->id ? 'selected' : '' }}>
                                                        {{ $p->priority_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-6">
                                            <label class="form-label form-label-sm fw-medium text-secondary mb-1">Kategori</label>
                                            <select name="category_id" class="form-select form-select-sm rounded-3">
                                                <option value="">All</option>
                                                @foreach ($categories as $c)
                                                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                                        {{ $c->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label form-label-sm fw-medium text-secondary mb-1">Label</label>
                                            <select name="label_id" class="form-select form-select-sm rounded-3">
                                                <option value="">All</option>
                                                @foreach ($labels as $l)
                                                    <option value="{{ $l->id }}" {{ request('label_id') == $l->id ? 'selected' : '' }}>
                                                        {{ $l->label_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>  
                                    </div>

                                    <div class="mt-3 pt-2 border-top">
                                        <button type="submit" class="btn btn-warning w-100 btn-sm fw-semibold rounded-3 text-dark py-2 shadow-sm" style="background-color: #f59e0b; border: none;">
                                            Apply filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Table Content --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-secondary small fw-semibold">
                                    <th class="text-nowrap ps-3">
                                        No. Tiket
                                    </th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>
                                        <a href="{{ sortCustomerUrl('priority_id') }}" class="text-secondary text-decoration-none d-inline-flex align-items-center gap-1">
                                            Prioritas
                                            @if($currentSortBy === 'priority_id')
                                                <i class="bi bi-chevron-{{ $currentSortOrder === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                            @else
                                                <i class="bi bi-chevron-expand opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ sortCustomerUrl('status') }}" class="text-secondary text-decoration-none d-inline-flex align-items-center gap-1">
                                            Status
                                            @if($currentSortBy === 'status')
                                                <i class="bi bi-chevron-{{ $currentSortOrder === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                            @else
                                                <i class="bi bi-chevron-expand opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a href="{{ sortCustomerUrl('due_at') }}" class="text-secondary text-decoration-none d-inline-flex align-items-center gap-1">
                                            Tenggat
                                            @if($currentSortBy === 'due_at' || $currentSortBy === 'due_date')
                                                <i class="bi bi-chevron-{{ $currentSortOrder === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                            @else
                                                <i class="bi bi-chevron-expand opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="text-nowrap">
                                        <a href="{{ sortCustomerUrl('created_at') }}" class="text-secondary text-decoration-none d-inline-flex align-items-center gap-1">
                                            Dibuat Pada
                                            @if($currentSortBy === 'created_at')
                                                <i class="bi bi-chevron-{{ $currentSortOrder === 'asc' ? 'up' : 'down' }} text-primary"></i>
                                            @else
                                                <i class="bi bi-chevron-expand opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width: 100px;" class="text-center pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td class="text-nowrap ps-3">
                                            <a href="{{ route('customer.tickets.show', $ticket) }}" class="fw-semibold text-decoration-none font-monospace">
                                                {{ $ticket->ticket_number }}
                                            </a>
                                        </td>
                                        <td>{{ Str::limit($ticket->title, 50) }}</td>
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
                                            <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            @if ($ticket->due_date)
                                                {{ $ticket->due_date->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap small text-muted">
                                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="text-center pe-3">
                                            <a href="{{ route('customer.tickets.show', $ticket) }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
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
                <div class="card-footer bg-white border-top-0 py-3 clearfix">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="small text-muted">
                            Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} results
                        </div>
                        <div>
                            {{ $tickets->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
