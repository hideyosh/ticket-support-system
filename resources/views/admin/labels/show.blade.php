@extends('layouts.app')
@section('content')

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3 class="mb-0">Detail Label</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.labels.index') }}">Label</a></li>
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
                    Informasi Label
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Nama Label</th>
                        <td>{{ $label->label_name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card card-info card-outline mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-ticket-alt me-1"></i>
                    5 Tiket Terbaru dengan Label Ini
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
                        @forelse($label->tickets()->latest()->take(5)->get() as $t)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $t->title }}</td>
                                <td>{{ $t->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada tiket dengan label ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.labels.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
