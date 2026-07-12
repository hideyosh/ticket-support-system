@extends('layouts.app')
@section('content')

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3 class="mb-0">Tambah Prioritas</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.priorities.index') }}">Prioritas</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
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
                    <i class="fas fa-plus me-1"></i>
                    Form Tambah Prioritas
                </h3>
            </div>

            <form action="{{ route('admin.priorities.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="mb-3">
                        <label for="priority_name" class="form-label">Nama Prioritas</label>
                        <input type="text"
                               name="priority_name"
                               id="priority_name"
                               class="form-control @error('priority_name') is-invalid @enderror"
                               value="{{ old('priority_name') }}"
                               placeholder="Masukkan nama prioritas"
                               required
                               autofocus>
                        @error('priority_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.priorities.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
