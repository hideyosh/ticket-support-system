@extends('adminlte::page')

@section('title', 'Manajemen Label')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Manajemen Label</h1>
        <a href="{{ route('admin.labels.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Label</a>
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
                        <th>Nama Label</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($labels as $label)
                    <tr>
                        <td>{{ $label->id }}</td>
                        <td>{{ $label->label_name }}</td>
                        <td>
                            <a href="{{ route('admin.labels.edit', $label) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.labels.destroy', $label) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus label ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $labels->links() }}
        </div>
    </div>
@stop
