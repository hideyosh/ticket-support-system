@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Hello, {{ Auth::user()->name }}</h3>
                    <p class="text-muted small mb-0">Ringkasan performa tiket hari ini</p>
                </div>
                <span class="badge rounded-pill bg-light text-muted px-3 py-2">
                    {{ now()->format('l, d F Y H:i') }}
                </span>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid dashboard-shell">

            {{-- KPI Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="small-box text-bg-primary rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $totalTickets }}</h3>
                            <p>Total tiket</p>
                        </div>
                        <i class="bi bi-ticket-perforated small-box-icon" aria-hidden="true"></i>
                        <a href="#" class="small-box-footer link-light">
                            Lihat semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="small-box text-bg-danger rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $overdueTickets }}</h3>
                            <p>Overdue</p>
                        </div>
                        <i class="bi bi-clock-history small-box-icon" aria-hidden="true"></i>
                        <a href="#" class="small-box-footer link-light">
                            Lihat semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="small-box text-bg-warning rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $unassignedTickets }}</h3>
                            <p>Unassigned</p>
                        </div>
                        <i class="bi bi-person-x small-box-icon" aria-hidden="true"></i>
                        <a href="#" class="small-box-footer link-light">
                            Lihat semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="small-box text-bg-success rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $thisWeekTickets }}</h3>
                            <p>Tiket minggu ini</p>
                        </div>
                        <i class="bi bi-calendar-week small-box-icon" aria-hidden="true"></i>
                        <a href="#" class="small-box-footer link-light">
                            Lihat semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Secondary stat: avg resolution time --}}
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-3 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-stopwatch fs-4 text-primary"></i>
                                    <span class="fw-semibold">Rata-rata waktu penyelesaian</span>
                                </div>
                                <span class="fs-5 fw-bold">
                                    {{ $avgResolutionTime !== null ? number_format($avgResolutionTime, 1) . ' jam' : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-6 col-12">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                            <h3 class="card-title fw-semibold mb-0">Tiket berdasarkan status</h3>
                        </div>
                        <div class="card-body px-3 pb-3 pt-2">
                            @forelse ($ticketsByStatus as $item)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span
                                            class="text-capitalize small">{{ str_replace('_', ' ', $item->status) }}</span>
                                        <span class="small fw-bold">{{ $item->total }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar
                                    @switch($item->status)
                                        @case('open') bg-primary @break
                                        @case('assigned') bg-info @break
                                        @case('in_progress') bg-warning @break
                                        @case('resolved') bg-success @break
                                        @case('closed') bg-secondary @break
                                        @case('escalated') bg-danger @break
                                        @default bg-dark
                                    @endswitch"
                                            style="width: {{ $totalTickets > 0 ? ($item->total / $totalTickets) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Belum ada data.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-12">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                            <h3 class="card-title fw-semibold mb-0">Tiket berdasarkan prioritas</h3>
                        </div>
                        <div class="card-body px-3 pb-3 pt-2">
                            @forelse ($ticketsByPriority as $item)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">
                                            {{ $item->priority->priority_name ?? 'Tanpa prioritas' }}
                                        </span>
                                        <span class="small fw-bold">{{ $item->total }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar
                                    @switch($item->priority->priority_name ?? null)
                                        @case('Low') bg-success @break
                                        @case('Medium') bg-info @break
                                        @case('High') bg-warning @break
                                        @case('Critical') bg-danger @break
                                        @default bg-secondary
                                    @endswitch"
                                            style="width: {{ $totalTickets > 0 ? ($item->total / $totalTickets) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Belum ada data.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Row --}}
            <div class="row g-3">
                <div class="col-xl-6 col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                            <h3 class="card-title fw-semibold mb-0">Top 5 agent</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th class="text-end">Resolved</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topAgents as $index => $agent)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $agent->name }}</td>
                                            <td class="text-end">
                                                <span class="badge text-bg-success">
                                                    {{ $agent->resolved_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted small py-3">
                                                Belum ada data agent.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                            <h3 class="card-title fw-semibold mb-0">Tiket berdasarkan kategori</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ticketsByCategory as $item)
                                        <tr>
                                            <td>{{ $item->category->category_name ?? 'Tanpa kategori' }}</td>
                                            <td class="text-end">
                                                @if ($item->total > 0)
                                                    <span class="badge text-bg-primary">
                                                        {{ $item->total }}
                                                    </span>
                                                @else
                                                    <span class="badge text-bg-secondary">
                                                        0
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted small py-3">
                                                Belum ada data kategori.
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
    </div>
@endsection
