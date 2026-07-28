@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Activity Logs</h3>
                    <p class="text-muted small mb-0">Riwayat aktivitas pada seluruh tiket</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Activity Logs</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <h3 class="card-title mb-0 fw-semibold">
                            <i class="bi bi-clock-history me-1"></i>
                            Riwayat Aktivitas
                        </h3>
                    </div>

                    {{-- Filter --}}
                    <form method="GET" action="{{ route('supervisor.activity-logs.index') }}"
                        class="row g-2 align-items-end pb-2">
                        <div class="col-6 col-md-2">
                            <label for="ticket_id" class="form-label small text-muted mb-1">Ticket ID</label>
                            <input type="number" name="ticket_id" id="ticket_id" value="{{ request('ticket_id') }}"
                                placeholder="#" class="form-control form-control-sm">
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="action" class="form-label small text-muted mb-1">Aksi</label>
                            <select name="action" id="action" class="form-select form-select-sm">
                                <option value="">Semua aksi</option>
                                @foreach (['Ticket created', 'Ticket assigned', 'Priority changed', 'Status changed', 'Comment added', 'Attachment uploaded', 'Ticket resolved', 'Ticket reopened', 'Ticket escalated', 'SLA overdue detected'] as $actionOption)
                                    <option value="{{ $actionOption }}" @selected(request('action') === $actionOption)>
                                        {{ $actionOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <label for="date_from" class="form-label small text-muted mb-1">Dari tanggal</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                class="form-control form-control-sm">
                        </div>

                        <div class="col-6 col-md-2">
                            <label for="date_to" class="form-label small text-muted mb-1">Sampai tanggal</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                class="form-control form-control-sm">
                        </div>

                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            @if (request()->hasAny(['ticket_id', 'action', 'date_from', 'date_to']))
                                <a href="{{ route('supervisor.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible m-3">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 160px;">Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Ticket</th>
                                <th>Perubahan</th>
                                <th style="width: 70px;" class="text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td class="text-muted small">
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="fw-semibold">
                                        {{ $log->user->name ?? '—' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary-emphasis">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($log->ticket)
                                            <a href="{{ route('supervisor.tickets.show', $log->ticket) }}">
                                                {{ Str::limit($log->ticket->title, 30) }}
                                            </a>
                                        @else
                                            <span class="text-muted">Ticket dihapus</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        @if ($log->field)
                                            <span class="text-muted">{{ $log->field }}:</span>
                                            <span
                                                class="text-danger text-decoration-line-through">{{ $log->old_value ?? '—' }}</span>
                                            <i class="bi bi-arrow-right text-muted mx-1"></i>
                                            <span class="text-success fw-semibold">{{ $log->new_value ?? '—' }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('supervisor.activity-logs.show', $log) }}" class="btn btn-info btn-sm"
                                            title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Belum ada aktivitas yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($logs, 'hasPages') && $logs->hasPages())
                    <div class="card-footer">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
