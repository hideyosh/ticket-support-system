@extends('layouts.app')
@section('content')

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3 class="mb-0">Edit SLA Rule</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sla-rules.index') }}">SLA Rules</a></li>
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
                <h3 class="card-title">
                    <i class="fas fa-edit me-1"></i>
                    Form Edit SLA Rule
                </h3>
            </div>

            <form action="{{ route('admin.sla-rules.update', $slaRule->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="mb-3">
                        <label for="priority_id" class="form-label">Prioritas</label>
                        <select name="priority_id" id="priority_id" class="form-control @error('priority_id') is-invalid @enderror" required>
                            <option value="">Pilih Prioritas</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->id }}" {{ old('priority_id', $slaRule->priority_id) == $priority->id ? 'selected' : '' }}>
                                    {{ $priority->priority_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="response_time" class="form-label">Response Time (Jam)</label>
                        <input type="number" name="response_time" class="form-control @error('response_time') is-invalid @enderror" id="response_time" value="{{ old('response_time', $slaRule->response_time) }}" min="1" required>
                        @error('response_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="resolution_time" class="form-label">Resolution Time (Jam)</label>
                        <input type="number" name="resolution_time" class="form-control @error('resolution_time') is-invalid @enderror" id="resolution_time" value="{{ old('resolution_time', $slaRule->resolution_time) }}" min="1" required>
                        @error('resolution_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('admin.sla-rules.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
