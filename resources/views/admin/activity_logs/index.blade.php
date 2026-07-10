@extends('adminlte::page')

@section('title', 'Activity Logs')

@section('content_header')
    <h1>Activity Logs</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Tiket</th>
                        <th>Aksi</th>
                        <th>Field</th>
                        <th>Nilai Lama</th>
                        <th>Nilai Baru</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->user->name ?? '<span class="text-muted">-</span>' }}</td>
                        <td>
                            @if($log->ticket)
                                <a href="{{ route('admin.tickets.show', $log->ticket) }}">{{ $log->ticket->ticket_number }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td><span class="badge badge-info">{{ $log->action }}</span></td>
                        <td>{{ $log->field ?? '-' }}</td>
                        <td>{{ $log->old_value ?? '-' }}</td>
                        <td>{{ $log->new_value ?? '-' }}</td>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Tidak ada activity log.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $logs->links() }}
        </div>
    </div>
@stop
