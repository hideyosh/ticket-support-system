@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Data Kategori</h3>
                    <p class="text-muted small mb-0">Organisasi tiket berdasarkan kategori</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kategori</li>
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
                            <i class="bi bi-tags me-1"></i>
                            Daftar Kategori
                        </h3>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Kategori
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
                                <th>Nama Kategori</th>
                                <th style="width: 150px;" class="text-center">Jumlah Tiket</th>
                                <th style="width: 180px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $category->category_name }}</td>
                                    <td class="text-center">
                                        @if ($category->tickets_count > 0)
                                            <span class="badge text-bg-info">
                                                {{ $category->tickets_count }} Tiket
                                            </span>
                                        @else
                                            <span class="badge text-bg-secondary">
                                                Tidak ada tiket
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.categories.show', $category->id) }}"
                                            class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
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
                                    <td colspan="4" class="text-center">Belum ada data kategori.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="card-footer">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
