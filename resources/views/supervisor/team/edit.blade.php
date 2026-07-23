@extends('layouts.app')

@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Edit Team</h3>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route(auth()->user()->dashboardRoute()) }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('supervisor.teams.index') }}">Team</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <form action="{{ route('supervisor.teams.update', $team->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="team_name" class="form-label fw-semibold small">Nama Team</label>
                                    <input type="text" id="team_name" name="team_name"
                                        class="form-control @error('team_name') is-invalid @enderror"
                                        value="{{ old('team_name', $team->team_name) }}"
                                        placeholder="Contoh: Tim Support Tier 1" required
                                        autofocus>
                                    @error('team_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @errorEnd
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="{{ route('supervisor.teams.index') }}"
                                        class="btn btn-outline-secondary rounded-pill">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary rounded-pill">
                                        <i class="bi bi-check-lg me-1"></i> Perbarui Team
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
