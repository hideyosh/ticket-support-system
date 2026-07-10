@extends('adminlte::page')

@section('title', 'Dashboard Admin')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

    {{-- ===== ROW 1: Info Box Utama ===== --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-ticket-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Tiket</span>
                    <span class="info-box-number">{{ number_format($totalTickets) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tiket Overdue</span>
                    <span class="info-box-number">{{ number_format($overdueTickets) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-slash"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Belum Di-assign</span>
                    <span class="info-box-number">{{ number_format($unassignedTickets) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-week"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tiket Minggu Ini</span>
                    <span class="info-box-number">{{ number_format($ticketsThisWeek) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2: Avg Resolution Time ===== --}}
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rata-rata Waktu Resolusi</span>
                    <span class="info-box-number">
                        @if($avgResolutionTime !== null)
                            {{ $avgResolutionTime }} jam
                        @else
                            <span class="text-muted" style="font-size:14px;">Belum ada tiket resolved</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 3: Tabel-tabel Statistik ===== --}}
    <div class="row">

        {{-- Tickets by Status --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Tiket per Status</h3>
                </div>
                <div class="card-body p-0">
                    @php
                        $statusColors = [
                            'open'                 => 'secondary',
                            'assigned'             => 'info',
                            'in_progress'          => 'primary',
                            'waiting_for_customer' => 'warning',
                            'resolved'             => 'success',
                            'closed'               => 'dark',
                            'reopened'             => 'danger',
                            'escalated'            => 'danger',
                        ];
                    @endphp
                    <table class="table table-sm table-hover mb-0">
                        <tbody>
                            @forelse($ticketsByStatus as $status => $count)
                            <tr>
                                <td>
                                    <span class="badge badge-{{ $statusColors[$status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                                <td class="text-right font-weight-bold">{{ number_format($count) }}</td>
                                <td style="width:35%">
                                    @php $pct = $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0; @endphp
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-{{ $statusColors[$status] ?? 'secondary' }}" style="width:{{ $pct }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tickets by Priority --}}
        <div class="col-md-4">
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-flag mr-1"></i> Tiket per Prioritas</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <tbody>
                            @forelse($ticketsByPriority as $priority => $count)
                            <tr>
                                <td>{{ $priority }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($count) }}</td>
                                <td style="width:35%">
                                    @php $pct = $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0; @endphp
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-warning" style="width:{{ $pct }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tickets by Category --}}
        <div class="col-md-4">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-folder mr-1"></i> Tiket per Kategori</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <tbody>
                            @forelse($ticketsByCategory as $category => $count)
                            <tr>
                                <td>{{ $category }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($count) }}</td>
                                <td style="width:35%">
                                    @php $pct = $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0; @endphp
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-info" style="width:{{ $pct }}%"></div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- end row 3 --}}

    {{-- ===== ROW 4: Top 5 Agents ===== --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-success shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-trophy mr-1"></i> Top 5 Agent (Tiket Resolved)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Agent</th>
                                <th class="text-right">Tiket Diselesaikan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topAgents as $i => $agent)
                            <tr>
                                <td>
                                    @if($i === 0)
                                        <i class="fas fa-medal text-warning"></i>
                                    @elseif($i === 1)
                                        <i class="fas fa-medal text-secondary"></i>
                                    @elseif($i === 2)
                                        <i class="fas fa-medal" style="color:#cd7f32;"></i>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </td>
                                <td>{{ $agent->name }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($agent->resolved_count) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Belum ada agent yang menyelesaikan tiket.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@stop
