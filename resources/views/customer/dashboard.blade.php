@extends('layouts.app')

@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Halo, {{ Auth::user()->name }}</h3>
                    <p class="text-muted small mb-0">Ringkasan tiket Anda</p>
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
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="small-box text-bg-primary rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $data['myTicketsCount'] }}</h3>
                            <p>Tiket saya</p>
                        </div>
                        <i class="bi bi-person small-box-icon" aria-hidden="true"></i>
                        <a href="{{ route('customer.tickets.index') }}" class="small-box-footer link-light">
                            Lihat tiket <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="small-box text-bg-warning rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $data['openTicketsCount'] }}</h3>
                            <p>Tiket terbuka</p>
                        </div>
                        <i class="bi bi-clock small-box-icon" aria-hidden="true"></i>
                        <a href="{{ route('customer.tickets.index') }}" class="small-box-footer link-light">
                            Lihat tiket <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="small-box text-bg-success rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $data['resolvedTicketsCount'] }}</h3>
                            <p>Tiket terselesaikan</p>
                        </div>
                        <i class="bi bi-check-circle small-box-icon" aria-hidden="true"></i>
                        <a href="{{ route('customer.tickets.index') }}" class="small-box-footer link-light">
                            Lihat tiket <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recently updated tickets --}}
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="card-title fw-semibold mb-0">Tiket terakhir dibuat</h3>
                                <a href="{{ route('customer.tickets.index') }}"
                                    class="small text-decoration-none d-inline-flex align-items-center gap-1">
                                    Lihat semua tiket <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Judul</th>
                                        <th style="width: 140px;">Status</th>
                                        <th style="width: 120px;">Prioritas</th>
                                        <th style="width: 160px;">Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data['recentTickets'] as $index => $ticket)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="text-truncate" style="max-width: 300px;">{{ $ticket->title }}</td>
                                            <td>
                                                @if ($ticket->status === 'open')
                                                    <span class="badge text-bg-info">Open</span>
                                                @elseif ($ticket->status === 'assigned')
                                                    <span class="badge text-bg-primary">Assigned</span>
                                                @elseif ($ticket->status === 'in_progress')
                                                    <span class="badge text-bg-warning">In Progress</span>
                                                @elseif ($ticket->status === 'waiting_for_customer')
                                                    <span class="badge text-bg-light text-dark border">Waiting
                                                        for Customer</span>
                                                @elseif ($ticket->status === 'resolved')
                                                    <span class="badge text-bg-success">Resolved</span>
                                                @elseif ($ticket->status === 'closed')
                                                    <span class="badge text-bg-secondary">Closed</span>
                                                @elseif ($ticket->status === 'reopened')
                                                    <span class="badge text-bg-danger">Reopened</span>
                                                @elseif ($ticket->status === 'escalated')
                                                    <span class="badge text-bg-danger">Escalated</span>
                                                @else
                                                    <span
                                                        class="badge text-bg-secondary">{{ ucfirst($ticket->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-capitalize">{{ $ticket->priority->priority_name ?? '-' }}</td>
                                            <td class="text-muted small">{{ $ticket->updated_at->diffForHumans() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted small py-3">
                                                Belum ada tiket.
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
