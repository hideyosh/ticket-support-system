@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Hello, {{ Auth::user()->name }}</h3>
                    <p class="text-muted small mb-0">Ringkasan performa tim hari ini</p>
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
                            <h3>{{ $data['teamTickets'] }}</h3>
                            <p>Total Tiket Tim</p>
                        </div>
                        <i class="bi bi-people small-box-icon" aria-hidden="true"></i>
                        <a href="{{ route('supervisor.tickets.index') }}" class="small-box-footer link-light">
                            Lihat semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="small-box text-bg-info rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $data['openTickets'] }}</h3>
                            <p>Tiket Baru (Open)</p>
                        </div>
                        <i class="bi bi-ticket-perforated small-box-icon" aria-hidden="true"></i>
                        <a href="{{ route('supervisor.tickets.index') }}" class="small-box-footer link-light">
                            Lihat semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="small-box text-bg-danger rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $data['overdueTickets'] }}</h3>
                            <p>Overdue</p>
                        </div>
                        <i class="bi bi-clock-history small-box-icon" aria-hidden="true"></i>
                        <a href="{{ route('supervisor.tickets.index') }}" class="small-box-footer link-light">
                            Lihat semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="small-box text-bg-warning rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $data['escalatedTickets'] }}</h3>
                            <p>Eskalasi</p>
                        </div>
                        <i class="bi bi-exclamation-triangle small-box-icon" aria-hidden="true"></i>
                        <a href="{{ route('supervisor.tickets.index') }}" class="small-box-footer link-light">
                            Lihat semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Row: Statistik Tim --}}
            <div class="row g-3">
                {{-- Tabel 1: Distribusi Tiket per Tim --}}
                <div class="col-xl-7 col-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2">
                            <h5 class="fw-semibold mb-0">
                                <i class="bi bi-diagram-3 me-2 text-primary"></i>Distribusi Tiket per Tim
                            </h5>
                            <span class="text-muted small">Perbandingan volume dan penyelesaian tiket per tim</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Nama Tim</th>
                                            <th class="text-center">Tiket Aktif</th>
                                            <th class="text-center">Eskalasi</th>
                                            <th class="text-center">Selesai</th>
                                            <th class="text-center">Total</th>
                                            <th class="pe-4" style="width: 150px;">Rasio Selesai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data['teamStats'] as $stats)
                                            @php
                                                $percentage = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0;
                                            @endphp
                                            <tr>
                                                <td class="ps-4 fw-semibold">
                                                    <a href="{{ route('supervisor.teams.show', $stats['team_id']) }}" class="text-decoration-none text-dark">
                                                        {{ $stats['team_name'] }}
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge text-bg-light border text-secondary">{{ $stats['open_active'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge text-bg-danger-subtle text-danger">{{ $stats['escalated'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge text-bg-success-subtle text-success">{{ $stats['completed'] }}</span>
                                                </td>
                                                <td class="text-center fw-bold">{{ $stats['total'] }}</td>
                                                <td class="pe-4">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress w-100" style="height: 6px;">
                                                            <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                                        </div>
                                                        <span class="small fw-semibold">{{ $percentage }}%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Belum ada data tim.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel 2: Rata-rata Waktu Resolusi per Tim --}}
                <div class="col-xl-5 col-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2">
                            <h5 class="fw-semibold mb-0">
                                <i class="bi bi-stopwatch me-2 text-warning"></i>Perbandingan Resolusi per Tim
                            </h5>
                            <span class="text-muted small">Rata-rata waktu yang dibutuhkan untuk merampungkan tiket</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Nama Tim</th>
                                            <th class="text-center">Tiket Selesai</th>
                                            <th class="pe-4 text-end">Rerata Kecepatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data['teamStats'] as $stats)
                                            <tr>
                                                <td class="ps-4 fw-semibold">{{ $stats['team_name'] }}</td>
                                                <td class="text-center fw-bold text-success">{{ $stats['completed'] }}</td>
                                                <td class="pe-4 text-end">
                                                    @if ($stats['avg_resolution'] !== null)
                                                        @php
                                                            $badgeColor = 'bg-success-subtle text-success';
                                                            if ($stats['avg_resolution'] > 48) {
                                                                $badgeColor = 'bg-danger-subtle text-danger';
                                                            } elseif ($stats['avg_resolution'] > 24) {
                                                                $badgeColor = 'bg-warning-subtle text-warning';
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $badgeColor }} px-3 py-2 font-monospace fs-7">
                                                            {{ $stats['avg_resolution'] }} Jam
                                                        </span>
                                                    @else
                                                        <span class="text-muted fst-italic small">N/A (Belum selesai)</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">Belum ada data tim.</td>
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
    </div>
@endsection
