@extends('layouts.app')
@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3 class="mb-0">Edit Team</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}">Team</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-edit me-1"></i> Form Edit Team</h3>
            </div>
            <form action="{{ route('admin.teams.update', $team->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="card-body">
<div class="mb-3">
    <label for="team_name" class="form-label">Nama Team</label>
    <input type="text" name="team_name" id="team_name" class="form-control @error('team_name') is-invalid @enderror" value="{{ old('team_name', $team->team_name) }}" placeholder="Masukkan nama team" required>
    @error('team_name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="supervisor_id" class="form-label">Supervisor</label>
    <select name="supervisor_id" id="supervisor_id" class="form-select @error('supervisor_id') is-invalid @enderror">
        <option value="">— Tidak Ada —</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ old('supervisor_id', $team->supervisor_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
        @endforeach
    </select>
    @error('supervisor_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
