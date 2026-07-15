@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Data Label</h3>
                    <p class="text-muted small mb-0">Kelola label untuk mengelompokkan tiket</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Label</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 px-3 pt-3 pb-1">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h3 class="card-title mb-0 fw-semibold">
                            <i class="bi bi-bookmark me-1"></i>
                            Daftar Label
                        </h3>
                        <a href="{{ route('admin.labels.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Label
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

                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Label</th>
                                <th style="width: 180px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($labels as $label)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $label->label_name }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.labels.show', $label->id) }}"
                                            class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.labels.edit', $label->id) }}"
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('admin.labels.destroy', $label->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus label ini?');">
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
                                    <td colspan="3" class="text-center">Belum ada data label.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($labels, 'hasPages') && $labels->hasPages())
                    <div class="card-footer">
                        {{ $labels->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
