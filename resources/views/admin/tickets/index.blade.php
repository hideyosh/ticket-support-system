@extends('adminlte::page')

@section('title', 'Manajemen Tiket')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Manajemen Tiket</h1>
        <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Tiket</a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Dibuat oleh</th>
                        <th>Ditugaskan ke</th>
                        <th>Tenggat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td><a href="{{ route('admin.tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                        <td>{{ Str::limit($ticket->title, 40) }}</td>
                        <td>{{ $ticket->category->name ?? '-' }}</td>
                        <td>{{ $ticket->priority->priority_name ?? '-' }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'open' => 'secondary',
                                    'assigned' => 'info',
                                    'in_progress' => 'primary',
                                    'waiting_for_customer' => 'warning',
                                    'resolved' => 'success',
                                    'closed' => 'dark',
                                    'reopened' => 'danger',
                                    'escalated' => 'danger',
                                ];
                                $color = $statusColors[$ticket->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                        </td>
                        <td>{{ $ticket->creator->name ?? '-' }}</td>
                        <td>{{ $ticket->assignedAgent->name ?? '<span class="text-muted">Belum ditugaskan</span>' }}</td>
                        <td>
                            @if($ticket->due_date)
                                @if($ticket->due_date < now() && !in_array($ticket->status, ['resolved', 'closed']))
                                    <span class="text-danger font-weight-bold">{{ $ticket->due_date->format('d/m/Y') }} <i class="fas fa-exclamation-circle"></i></span>
                                @else
                                    {{ $ticket->due_date->format('d/m/Y') }}
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus tiket ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">Tidak ada tiket ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $tickets->links() }}
        </div>
    </div>
@stop
