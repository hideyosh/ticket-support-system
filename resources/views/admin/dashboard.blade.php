@extends('layouts.app')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Hello, {{ Auth::user()->name }}</h3>
                <span class="text-muted small">{{ now()->format('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            {{-- KPI Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="small-box text-bg-primary">
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
                    <div class="small-box text-bg-danger">
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
                    <div class="small-box text-bg-warning">
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
                    <div class="small-box text-bg-success">
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

            {{-- Charts Row --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-6 col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Tiket berdasarkan status</h3>
                        </div>
                        <div class="card-body">
                            @foreach ($ticketsByStatus as $item)
                                <div class="mb-2">
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
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Tiket berdasarkan prioritas</h3>
                        </div>
                        <div class="card-body">
                            @foreach ($ticketsByPriority as $item)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">{{ $item->priority->priority_name }}</span>
                                        <span class="small fw-bold">{{ $item->total }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar
                                    @switch($item->priority->priority_name)
                                        @case('Low') bg-success @break
                                        @case('Medium') bg-info @break
                                        @case('High') bg-warning @break
                                        @case('Critical') bg-danger @break
                                    @endswitch"
                                            style="width: {{ $totalTickets > 0 ? ($item->total / $totalTickets) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom Row --}}
            <div class="row g-3">
                <div class="col-xl-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top 5 agent</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th class="text-end">Resolved</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topAgents as $index => $agent)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $agent->name }}</td>
                                            <td class="text-end">
                                                <span class="badge text-bg-success">
                                                    {{ $agent->resolved_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tiket berdasarkan kategori</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ticketsByCategory as $item)
                                        <tr>
                                            <td>{{ $item->category_name }}</td>
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
