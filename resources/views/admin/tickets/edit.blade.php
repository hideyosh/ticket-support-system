@extends('layouts.app')

@section('content')

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3 class="mb-0">Edit Tiket — <span class="font-monospace">{{ $ticket->ticket_number }}</span></h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Tiket</a></li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">

        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-pencil-square me-1"></i> Edit Tiket
                </h3>
            </div>

            <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
                @csrf @method('PUT')

                <div class="card-body">

                    {{-- Validasi Error --}}
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Terdapat kesalahan:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Nomor Tiket (Read-only, tidak bisa diubah) --}}
                    <div class="mb-4 p-3 bg-light rounded border">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="text-muted small">Nomor Tiket</span><br>
                                <span class="fw-bold font-monospace fs-5 text-primary">{{ $ticket->ticket_number }}</span>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted small">Status Saat Ini</span><br>
                                @php
                                    $statusColors = [
                                        'open' => 'secondary', 'assigned' => 'info',
                                        'in_progress' => 'primary', 'waiting_for_customer' => 'warning',
                                        'resolved' => 'success', 'closed' => 'dark',
                                        'reopened' => 'danger', 'escalated' => 'danger',
                                    ];
                                @endphp
                                <span class="badge text-bg-{{ $statusColors[$ticket->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </div>
                            <div class="col">
                                <small class="text-muted">
                                    <i class="bi bi-lock-fill me-1"></i>
                                    Nomor tiket dan status tidak dapat diubah dari halaman ini.
                                    Gunakan panel <strong>Ubah Status</strong> di halaman detail.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Judul --}}
                        <div class="col-12">
                            <label for="title" class="form-label fw-semibold">
                                Judul Tiket <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $ticket->title) }}"
                                   placeholder="Ringkasan singkat masalah..."
                                   autofocus>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">
                                Deskripsi <span class="text-danger">*</span>
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="6"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Jelaskan masalah secara detail...">{{ old('description', $ticket->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select name="category_id"
                                    id="category_id"
                                    class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                            {{ old('category_id', $ticket->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Prioritas --}}
                        <div class="col-md-6">
                            <label for="priority_id" class="form-label fw-semibold">
                                Prioritas <span class="text-danger">*</span>
                            </label>
                            <select name="priority_id"
                                    id="priority_id"
                                    class="form-select @error('priority_id') is-invalid @enderror">
                                <option value="">-- Pilih Prioritas --</option>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority->id }}"
                                            {{ old('priority_id', $ticket->priority_id) == $priority->id ? 'selected' : '' }}>
                                        {{ $priority->priority_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Label --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Label <span class="text-muted fw-normal">(opsional)</span></label>
                            @php
                                $selectedLabelIds = old('labels', $ticket->labels->pluck('id')->toArray());
                            @endphp
                            <div class="border rounded p-3 @error('labels') border-danger @enderror"
                                 style="max-height: 130px; overflow-y: auto;">
                                @forelse($labels as $label)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="labels[]"
                                               id="label_edit_{{ $label->id }}"
                                               value="{{ $label->id }}"
                                               {{ in_array($label->id, $selectedLabelIds) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="label_edit_{{ $label->id }}">
                                            {{ $label->label_name }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted small mb-0">Belum ada label tersedia.</p>
                                @endforelse
                            </div>
                            @error('labels')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-secondary ms-1">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
