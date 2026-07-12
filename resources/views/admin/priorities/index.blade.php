@extends('layouts.app')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="mb-0">Data Prioritas</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Prioritas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list me-1"></i>
                        Daftar Prioritas
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.priorities.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Prioritas
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible m-3">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Prioritas</th>
                                <th style="width: 180px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($priorities as $priority)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $priority->priority_name }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.priorities.show', $priority->id) }}"
                                            class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.priorities.edit', $priority->id) }}"
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('admin.priorities.destroy', $priority->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus prioritas ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada data prioritas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($priorities, 'hasPages') && $priorities->hasPages())
                    <div class="card-footer">
                        {{ $priorities->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
