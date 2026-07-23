@extends('layouts.app')

@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Buat Tiket Baru</h3>
                    <p class="text-muted small mb-0">Kirim permintaan support. Tiket tidak dapat diedit setelah dibuat.</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route(auth()->user()->dashboardRoute()) }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.tickets.index') }}">Tiket</a></li>
                    <li class="breadcrumb-item active">Buat</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    {{-- PENTING: Tambahkan enctype="multipart/form-data" untuk upload file --}}
                    <form action="{{ route('customer.tickets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Judul --}}
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                class="form-control @error('title') is-invalid @enderror">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kategori & Prioritas --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Pilih kategori</option>
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
                            <div class="col-md-6">
                                <label class="form-label">Prioritas</label>
                                <select name="priority_id" class="form-select @error('priority_id') is-invalid @enderror">
                                    <option value="">Pilih prioritas</option>
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
                        </div>

                        {{-- Label (Dropdown Custom) --}}
                        <div class="mb-3 mt-3">
                            <label class="form-label">Label (opsional)</label>
                            <div class="dropdown">
                                <button type="button"
                                    class="btn btn-outline-secondary w-100 d-flex justify-content-between align-items-center"
                                    id="labelDropdownToggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                    aria-expanded="false">
                                    <span id="labelDropdownText" class="small text-muted">Pilih label</span>
                                    <i class="bi bi-chevron-down ms-2 text-secondary"></i>
                                </button>

                                <div class="dropdown-menu w-100 p-2 shadow border-0 rounded-4"
                                    style="max-height: 240px; overflow-y: auto; min-width: 100%;">
                                    <div class="px-2 py-1 small text-muted fw-semibold">Pilih satu atau lebih label</div>
                                    @forelse($labels as $label)
                                        <div class="label-option d-flex align-items-center gap-2 px-2 py-2 rounded-3">
                                            <input class="form-check-input m-0 flex-shrink-0" type="checkbox"
                                                name="labels[]" id="label_{{ $label->id }}" value="{{ $label->id }}"
                                                {{ in_array($label->id, old('labels', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small w-100 mb-0"
                                                for="label_{{ $label->id }}">
                                                {{ $label->label_name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-muted small mb-0 px-2 py-2">
                                            <i class="bi bi-info-circle me-1"></i> Belum ada label.
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                            @error('labels')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SECTION ATTACHMENT (LAMPIRAN) --}}
                        <div class="mb-4">
                            <label class="form-label">Lampiran (opsional)</label>
                            <div class="input-group">
                                <input type="file" name="attachments[]" id="attachmentInput" multiple
                                    class="form-control @error('attachments.*') is-invalid @enderror">
                                <label class="input-group-text bg-light" for="attachmentInput">
                                    <i class="bi bi-paperclip"></i>
                                </label>
                            </div>
                            <div class="form-text text-muted small">
                                Format yang diizinkan: PNG, JPG, JPEG, PDF, DOC, DOCX, XLS, XLSX (Maks. 2MB per file). 
                            </div>
                            @error('attachments.*')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            {{-- Preview Daftar File yang Dipilih --}}
                            <div id="filePreviewList" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Kirim Tiket</button>
                            <a href="{{ route('customer.tickets.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic Label Dropdown
            const toggle = document.getElementById('labelDropdownToggle');
            const text = document.getElementById('labelDropdownText');
            const checkboxes = Array.from(document.querySelectorAll('input[name="labels[]"]'));

            if (toggle && text && checkboxes.length > 0) {
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
            }

            // Preview attachment
            const attachmentInput = document.getElementById('attachmentInput');
            const filePreviewList = document.getElementById('filePreviewList');

            if (attachmentInput && filePreviewList) {
                attachmentInput.addEventListener('change', function() {
                    filePreviewList.innerHTML = '';
                    const files = Array.from(this.files);

                    files.forEach(file => {
                        const badge = document.createElement('span');
                        badge.className =
                            'badge bg-light text-dark border d-inline-flex align-items-center gap-1 p-2 rounded-3';
                        badge.innerHTML =
                            `<i class="bi bi-file-earmark-text text-primary"></i> ${file.name} <small class="text-muted">(${ (file.size / 1024).toFixed(1) } KB)</small>`;
                        filePreviewList.appendChild(badge);
                    });
                });
            }
        });
    </script>
@endsection
