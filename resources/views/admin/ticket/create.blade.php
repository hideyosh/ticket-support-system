@extends('layouts.app')

@section('content')

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="mb-0">Buat Tiket Baru</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboardRoute() }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Tiket</a></li>
                        <li class="breadcrumb-item active">Buat</li>
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
                        <i class="bi bi-ticket-perforated me-1"></i> Form Buat Tiket Baru
                    </h3>
                </div>

                <form action="{{ route('admin.tickets.store') }}" method="POST">
                    @csrf

                    <div class="card-body">

                        {{-- Validasi Error --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Terdapat kesalahan:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Catatan: Nomor tiket di-generate otomatis --}}
                        <div class="alert alert-info d-flex align-items-center gap-2 py-2">
                            <i class="bi bi-info-circle-fill"></i>
                            <small>Nomor tiket akan di-generate otomatis oleh sistem (format:
                                <code>TCK-2026-000001</code>).</small>
                        </div>

                        <div class="row g-3">
                            {{-- Judul --}}
                            <div class="col-12">
                                <label for="title" class="form-label fw-semibold">
                                    Judul Tiket <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                    placeholder="Ringkasan singkat masalah..." autofocus>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">
                                    Deskripsi <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" id="description" rows="6"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Jelaskan masalah secara detail, termasuk langkah-langkah reproduksi jika ada...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Kategori --}}
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-semibold">
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select name="category_id" id="category_id"
                                    class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                <select name="priority_id" id="priority_id"
                                    class="form-select @error('priority_id') is-invalid @enderror">
                                    <option value="">-- Pilih Prioritas --</option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}"
                                            {{ old('priority_id') == $priority->id ? 'selected' : '' }}>
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
                                <div class="dropdown">
                                    <button type="button"
                                        class="btn btn-outline-white w-100 text-start d-flex justify-content-between align-items-center px-3 py-2 rounded-3 shadow-sm border border-black"
                                        id="labelDropdownToggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                        aria-expanded="false">
                                        <span id="labelDropdownText" class="small text-muted">Pilih label</span>
                                        <i class="bi bi-chevron-down ms-2 text-secondary"></i>
                                    </button>

                                    <div class="dropdown-menu w-100 p-2 shadow border-0 rounded-4"
                                        style="max-height: 240px; overflow-y: auto; min-width: 100%;">
                                        <div class="px-2 py-1 small text-muted fw-semibold">Pilih satu atau lebih label
                                        </div>
                                        @forelse($labels as $label)
                                            <div class="label-option d-flex align-items-center gap-2 px-2 py-2 rounded-3">
                                                <input class="form-check-input m-0 flex-shrink-0" type="checkbox"
                                                    name="labels[]" id="label_{{ $label->id }}"
                                                    value="{{ $label->id }}"
                                                    {{ in_array($label->id, old('labels', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small w-100 mb-0"
                                                    for="label_{{ $label->id }}">
                                                    {{ $label->label_name }}
                                                </label>
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-0 px-2 py-2">
                                                <i class="bi bi-info-circle me-1"></i> Belum ada label. Tambahkan label di
                                                menu <a href="{{ route('admin.labels.index') }}">Label</a>.
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                                @error('labels')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Tiket
                        </button>
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary ms-1">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('labelDropdownToggle');
        const text = document.getElementById('labelDropdownText');
        const checkboxes = Array.from(document.querySelectorAll('input[name="labels[]"]'));

        if (!toggle || !text || checkboxes.length === 0) {
            return;
        }

        const updateLabelText = () => {
            const selected = checkboxes.filter((checkbox) => checkbox.checked);

            if (selected.length === 0) {
                text.textContent = 'Pilih label';
                text.className = 'small text-muted';
            } else if (selected.length === 1) {
                const label = document.querySelector(`label[for="${selected[0].id}"]`);
                text.textContent = label ? label.textContent.trim() : '1 label dipilih';
                text.className = 'small text-dark fw-semibold';
            } else {
                text.textContent = `${selected.length} label dipilih`;
                text.className = 'small text-dark fw-semibold';
            }
        };

        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateLabelText));
        updateLabelText();
    });
</script>
