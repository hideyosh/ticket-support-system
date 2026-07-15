@extends('layouts.app')
@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3 class="mb-0">Detail Team</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.teams.index') }}">Team</a></li>
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
                <h3 class="card-title"><i class="fas fa-info-circle me-1"></i> Informasi Team</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Nama Team</dt>
                    <dd class="col-sm-9">{{ $team->team_name }}</dd>

                    <dt class="col-sm-3">Supervisor</dt>
                    <dd class="col-sm-9">
                        @if($team->supervisor)
                            {{ $team->supervisor->name }} ({{ $team->supervisor->email }})
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>
                </dl>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-pencil-alt"></i> Edit
                </a>
                <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
