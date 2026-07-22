@extends('layouts.app')

@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Hello, {{ Auth::user()->name }}</h3>
                    <p class="text-muted small mb-0">Ringkasan performa dan tiket penugasan Anda hari ini</p>
                </div>
                <span class="badge rounded-pill bg-light text-muted px-3 py-2">
                    {{ now()->format('l, d F Y H:i') }}
                </span>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid dashboard-shell">

            {{-- 1-2. KPI Cards (2 KPI sahaja: My Assigned & My Overdue) --}}
            <div class="row g-3 mb-4">
                {{-- KPI 1: My Assigned Tickets --}}
                <div class="col-md-6 col-12">
                    <div class="small-box text-bg-primary rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $data['myAssignedTickets'] }}</h3>
                            <p>Tiket Ditugaskan ke Saya</p>
                        </div>
                        <i class="bi bi-person-workspace small-box-icon" aria-hidden="true"></i>
                        <a href="{{ Route::has('agent.tickets.index') ? route('agent.tickets.index') : '#' }}" class="small-box-footer link-light">
                            Lihat semua tiket <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                {{-- KPI 2: My Overdue Tickets --}}
                <div class="col-md-6 col-12">
                    <div class="small-box text-bg-danger rounded-4 shadow-sm border-0 h-100">
                        <div class="inner">
                            <h3>{{ $data['myOverdueTickets'] }}</h3>
                            <p>Tiket Overdue Saya</p>
                        </div>
                        <i class="bi bi-clock-history small-box-icon" aria-hidden="true"></i>
                        <a href="{{ Route::has('agent.tickets.index') ? route('agent.tickets.index') : '#' }}" class="small-box-footer link-light">
                            Lihat tiket overdue <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- 3. Tiket Berdasarkan Status --}}
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                            <h3 class="card-title fw-semibold mb-0">Tiket berdasarkan status</h3>
                        </div>
                        <div class="card-body px-3 pb-3 pt-2">
                            @forelse ($data['ticketsByStatus'] as $item)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-capitalize small">{{ str_replace('_', ' ', $item->status) }}</span>
                                        <span class="small fw-bold">{{ $item->total }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar
                                    @switch($item->status)
                                        @case('open') bg-primary @break
                                        @case('assigned') bg-info @break
                                        @case('in_progress') bg-warning @break
                                        @case('waiting_for_customer') bg-secondary @break
                                        @case('resolved') bg-success @break
                                        @case('closed') bg-secondary @break
                                        @case('reopened') bg-danger @break
                                        @case('escalated') bg-danger @break
                                        @default bg-dark
                                    @endswitch"
                                            style="width: {{ $data['myAssignedTickets'] > 0 ? ($item->total / $data['myAssignedTickets']) * 100 : 0 }}%">
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

            {{-- 4. Recently Updated Tickets Table --}}
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="fw-semibold mb-0">
                                        <i class="bi bi-clock-history me-2 text-warning"></i>Tiket Terbaru Diperbarui
                                    </h5>
                                    <span class="text-muted small">Daftar 10 tiket terkini yang ditugaskan ke Anda</span>
                                </div>
                                @if (Route::has('agent.tickets.index'))
                                    <a href="{{ route('agent.tickets.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        Lihat Semua Tiket <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4 text-nowrap">No. Tiket</th>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Prioritas</th>
                                            <th>Status</th>
                                            <th>Dibuat oleh</th>
                                            <th class="text-nowrap">Terakhir Diperbarui</th>
                                            <th class="pe-4 text-center" style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data['recentlyUpdatedTickets'] as $ticket)
                                            <tr>
                                                <td class="ps-4 text-nowrap">
                                                    @if (Route::has('agent.tickets.show'))
                                                        <a href="{{ route('agent.tickets.show', $ticket) }}"
                                                            class="fw-semibold text-decoration-none font-monospace">
                                                            {{ $ticket->ticket_number }}
                                                        </a>
                                                    @else
                                                        <span class="fw-semibold font-monospace text-primary">{{ $ticket->ticket_number }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-medium text-dark">{{ Str::limit($ticket->title, 40) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge text-bg-light border text-secondary">
                                                        {{ $ticket->category->category_name ?? '-' }}
                                                    </span>
                                                </td>
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
                                                <td class="small">{{ $ticket->creator->name ?? '-' }}</td>
                                                <td class="text-nowrap small text-muted">
                                                    {{ $ticket->updated_at->diffForHumans() }}
                                                </td>
                                                <td class="pe-4 text-center">
                                                    @if (Route::has('agent.tickets.show'))
                                                        <a href="{{ route('agent.tickets.show', $ticket) }}"
                                                            class="btn btn-info btn-sm rounded-circle shadow-sm" title="Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @else
                                                        <span class="btn btn-sm btn-light disabled rounded-circle"><i class="bi bi-eye"></i></span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                                    Belum ada data tiket.
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
    </div>
@endsection
