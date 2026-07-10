@extends('adminlte::page')

@section('title', 'Manajemen SLA Rules')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Manajemen SLA Rules</h1>
        <a href="{{ route('admin.sla-rules.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah SLA Rule</a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Prioritas</th>
                        <th>Response Time (Jam)</th>
                        <th>Resolution Time (Jam)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slaRules as $rule)
                    <tr>
                        <td>{{ $rule->id }}</td>
                        <td>{{ $rule->priority->priority_name ?? '-' }}</td>
                        <td>{{ $rule->response_time }} jam</td>
                        <td>{{ $rule->resolution_time }} jam</td>
                        <td>
                            <a href="{{ route('admin.sla-rules.edit', $rule) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.sla-rules.destroy', $rule) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus SLA rule ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $slaRules->links() }}
        </div>
    </div>
@stop
