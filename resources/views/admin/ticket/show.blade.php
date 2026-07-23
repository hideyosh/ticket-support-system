@extends('layouts.app')

@section('content')

    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h3 class="mb-1 fw-bold">Detail Tiket</h3>
                    <p class="text-muted small mb-0">Pantau informasi tiket dan kelola penugasan agent.</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route(auth()->user()->dashboardRoute()) }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tickets.index') }}">Tiket</a></li>
                    <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            {{-- Session Flash Alerts --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                {{-- Kolom Kiri: Informasi Tiket, Komentar, Lampiran, & Riwayat --}}
                <div class="col-lg-8">

                    {{-- Detail Utama Tiket --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-2">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h3 class="card-title fw-semibold mb-0">
                                    <i class="bi bi-ticket-detailed-fill me-2"></i> Informasi Tiket
                                </h3>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>
                                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-arrow-left me-1"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge rounded-pill text-bg-primary">{{ $ticket->ticket_number }}</span>
                                <span class="badge rounded-pill {{ $ticket->getStatusBadgeClass() }} }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                                <span class="badge text-bg-{{ $ticket->priority ? 'warning' : 'secondary' }}">
                                    {{ $ticket->priority->priority_name ?? '-' }}
                                </span>
                            </div>

                            <h2 class="h4 fw-bold mb-3">{{ $ticket->title }}</h2>
                            <p class="text-muted mb-4">{{ $ticket->description }}</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="text-muted small mb-1">Kategori</div>
                                        <div class="fw-semibold">{{ $ticket->category->category_name ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="text-muted small mb-1">Prioritas</div>
                                        <div class="fw-semibold">{{ $ticket->priority->priority_name ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="text-muted small mb-1">Dibuat oleh</div>
                                        <div class="fw-semibold">{{ $ticket->creator->name ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="text-muted small mb-1">Ditugaskan ke</div>
                                        <div class="fw-semibold">{{ $ticket->assignedAgent->name ?? 'Belum ditugaskan' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="text-muted small mb-1">Tenggat</div>
                                        <div class="fw-semibold">
                                            @if ($ticket->due_date)
                                                {{ $ticket->due_date->format('d M Y, H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="text-muted small mb-1">Label</div>
                                        <div class="fw-semibold">
                                            @forelse ($ticket->labels as $label)
                                                <span
                                                    class="badge text-bg-light text-dark me-1 mb-1">{{ $label->label_name }}</span>
                                            @empty
                                                <span class="text-muted">Tidak ada label</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section Lampiran Utama Tiket --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-2">
                            <h3 class="card-title fw-semibold mb-0">
                                <i class="bi bi-paperclip me-2"></i> Lampiran Awal
                            </h3>
                        </div>
                        <div class="card-body p-3">
                            @if ($ticket->attachments && $ticket->attachments->count() > 0)
                                <div class="row g-2">
                                    @foreach ($ticket->attachments as $attachment)
                                        <div class="col-md-6">
                                            <div
                                                class="border rounded-3 p-2 d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center text-truncate me-2">
                                                    <i class="bi bi-file-earmark-arrow-down text-primary fs-4 me-2"></i>
                                                    <div class="text-truncate">
                                                        <div class="fw-semibold small text-truncate"
                                                            title="{{ $attachment->original_name }}">
                                                            {{ $attachment->original_name }}
                                                        </div>
                                                        <span
                                                            class="text-muted extra-small">{{ number_format($attachment->file_size / 1024, 1) }}
                                                            KB</span>
                                                    </div>
                                                </div>
                                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary rounded-pill">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted small mb-0">Tidak ada lampiran pada pembuatan tiket awal.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Section Percakapan / Diskusi Komentar --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-2">
                            <h3 class="card-title fw-semibold mb-0">
                                <i class="bi bi-chat-left-text me-2"></i> Diskusi & Balasan
                            </h3>
                        </div>
                        <div class="card-body p-3">

                            {{-- Form Tambah Komentar/Balasan --}}
                            <form action="{{ route('admin.comments.store', $ticket) }}" method="POST"
                                enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="comment" class="form-label fw-semibold">Tambah Balasan</label>
                                    <textarea name="body" id="body" rows="3" class="form-control @error('comment') is-invalid @enderror"
                                        placeholder="Tulis tanggapan atau instruksi untuk tiket ini..." required>{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col-md-7">
                                        <label for="attachments" class="form-label small text-muted mb-1">Tambah Lampiran
                                            (Opsional)</label>
                                        <input type="file" name="attachments[]" id="attachments"
                                            class="form-control form-control-sm" multiple>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-check mt-md-4">
                                            <input class="form-check-input" type="checkbox" name="type"
                                                value="internal_note" id="is_internal">
                                            <label class="form-check-label small fw-semibold text-warning-emphasis"
                                                for="is_internal">
                                                <i class="bi bi-lock-fill me-1"></i> Catatan Internal (Hanya Tim)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-send-fill me-1"></i> Kirim Balasan
                                    </button>
                                </div>
                            </form>

                            <hr class="my-4 text-muted opacity-25">

                            {{-- Timeline Daftar Komentar --}}
                            <div class="comments-timeline">
                                @forelse ($ticket->comments as $comment)
                                    <div
                                        class="card border-0 {{ $comment->type === 'internal_note' ? 'bg-warning-subtle border-start border-warning border-4' : 'bg-light' }} rounded-3 mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="fw-bold text-dark">
                                                        {{ $comment->user->name ?? 'Pengguna' }}</div>
                                                    <span class="badge rounded-pill bg-secondary text-capitalize"
                                                        style="font-size: 0.7rem;">
                                                        {{ $comment->user->role->role_name ?? 'User' }}
                                                    </span>
                                                    @if ($comment->type === 'internal_note')
                                                        <span class="badge bg-warning text-dark"><i
                                                                class="bi bi-lock-fill me-1"></i> Internal Note</span>
                                                    @endif
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <small
                                                        class="text-muted fs-7">{{ $comment->created_at->diffForHumans() }}</small>

                                                    {{-- Otorisasi Hapus Komentar Menggunakan Policy --}}
                                                    @can('delete', $comment)
                                                        <button type="button"
                                                            class="btn btn-link text-danger p-0 ms-2 text-decoration-none"
                                                            title="Hapus Komentar" data-bs-toggle="modal"
                                                            data-bs-target="#deleteCommentModal{{ $comment->id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>

                                                        {{-- Modal Konfirmasi Hapus --}}
                                                        <div class="modal fade" id="deleteCommentModal{{ $comment->id }}"
                                                            tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                                <div class="modal-content">
                                                                    <div class="modal-header border-0 pb-0">
                                                                        <h5 class="modal-title fw-bold fs-6">Hapus Komentar?
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body text-muted small py-2">
                                                                        Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin
                                                                        ingin menghapus komentar ini?
                                                                    </div>
                                                                    <div class="modal-footer border-0 pt-0">
                                                                        <button type="button" class="btn btn-light btn-sm"
                                                                            data-bs-dismiss="modal">Batal</button>
                                                                        <form
                                                                            action="{{ route('admin.comments.destroy', [$ticket, $comment]) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-danger btn-sm">Hapus</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endcan
                                                </div>
                                            </div>

                                            <div class="text-dark mb-2" style="white-space: pre-line;">
                                                {{ $comment->body }}</div>

                                            {{-- Lampiran Pada Komentar --}}
                                            @if ($comment->attachments && $comment->attachments->count() > 0)
                                                <div class="mt-3 pt-2 border-top border-secondary-subtle">
                                                    <div class="small fw-semibold text-muted mb-2"><i
                                                            class="bi bi-paperclip me-1"></i> Lampiran Balasan:</div>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach ($comment->attachments as $attachment)
                                                            <a href="{{ Storage::url($attachment->file_path) }}"
                                                                target="_blank"
                                                                class="btn btn-sm btn-white border rounded-pill d-inline-flex align-items-center text-truncate"
                                                                style="max-width: 250px;">
                                                                <i class="bi bi-file-earmark me-1 text-primary"></i>
                                                                <span
                                                                    class="text-truncate small">{{ $attachment->original_name }}</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-chat-square-dots fs-2 d-block mb-2 text-secondary"></i>
                                        Belum ada diskusi atau tanggapan pada tiket ini.
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>

                    {{-- Section Riwayat Aktivitas Tiket (Activity Log) --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-2">
                            <h3 class="card-title fw-semibold mb-0">
                                <i class="bi bi-clock-history me-2"></i> Riwayat Aktivitas Tiket
                            </h3>
                        </div>
                        <div class="card-body p-3">
                            @if ($ticket->logs && $ticket->logs->count() > 0)
                                <ul class="list-group list-group-flush small">
                                    @foreach ($ticket->logs as $log)
                                        <li class="list-group-item bg-transparent px-0 py-2 border-bottom-subtle">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong class="text-dark">{{ $log->user->name ?? 'Sistem' }}</strong>
                                                    <span class="text-muted ms-1">{{ $log->action }}</span>
                                                </div>
                                                <span
                                                    class="text-muted extra-small">{{ $log->created_at->format('d M Y, H:i') }}</span>
                                            </div>
                                            @if ($log->description)
                                                <div class="text-muted extra-small mt-1">{{ $log->description }}</div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted small mb-0">Belum ada riwayat riwayat aktivitas yang tercatat.</p>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Kolom Kanan: Pengaturan Agent & Status --}}
                <div class="col-lg-4">

                    {{-- Assign Agent --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-2">
                            <h3 class="card-title fw-semibold mb-0">
                                <i class="bi bi-person-workspace me-2"></i> Assign Agent
                            </h3>
                        </div>
                        <div class="card-body p-3">
                            <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <label for="assigned_to" class="form-label fw-semibold">Pilih agent</label>
                                <select name="assigned_to" id="assigned_to" class="form-select">
                                    <option value="">Belum ditugaskan</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}"
                                            {{ old('assigned_to', $ticket->assigned_to) == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="d-grid mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-person-check-fill me-1"></i> Simpan Assign
                                    </button>
                                </div>
                            </form>

                            @if ($ticket->assignedAgent)
                                <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST"
                                    class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="assigned_to" value="">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-person-x-fill me-1"></i> Batalkan Assign
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Ubah Status --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 px-3 pt-3 pb-2">
                            <h3 class="card-title fw-semibold mb-0">
                                <i class="bi bi-arrow-left-right me-2"></i> Ubah Status
                            </h3>
                        </div>
                        <div class="card-body p-3">
                            <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <label for="status" class="form-label fw-semibold">Status tiket</label>
                                <select name="status" id="status" class="form-select">
                                    @foreach ($allowedStatuses as $status)
                                        <option value="{{ $status }}"
                                            {{ $ticket->status === $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="d-grid mt-3">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-arrow-repeat me-1"></i> Perbarui Status
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
