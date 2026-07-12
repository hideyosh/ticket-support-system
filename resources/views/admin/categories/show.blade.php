@extends('layouts.app')
@section('content')

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3 class="mb-0">Detail Kategori</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Kategori</a></li>
                    <li class="breadcrumb-item active">Detail</li>
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
                    <i class="fas fa-info-circle me-1"></i>
                    Informasi Kategori
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Nama Kategori</th>
                        <td>{{ $category->category_name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card card-info card-outline mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-ticket-alt me-1"></i>
                    5 Tiket Terbaru dengan Kategori Ini
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Judul Tiket</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($category->ticket()->latest()->take(5)->get() as $t)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $t->title }}</td>
                                <td>{{ $t->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada tiket dengan kategori ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
