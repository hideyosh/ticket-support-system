@extends('layouts.app')
@section('content')
    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div>
                    <h3 class="mb-1 fw-bold">Detail Tiket</h3>
                    <p class="text-muted small mb-0">Pantau informasi dan perkembangan tiket kamu.</p>
                </div>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route(auth()->user()->dashboardRoute()) }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.tickets.index') }}">Tiket</a></li>
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
            <div class="row g-3">

                {{-- Kolom kiri: Informasi Tiket --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-ticket-perforated"></i>
                                    <span class="fw-semibold">Informasi Tiket</span>
                                </div>
                                <a href="{{ route('customer.tickets.index') }}"
                                    class="btn btn-outline-secondary btn-sm rounded-pill">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                            </div>

                            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                <span class="badge rounded-pill text-bg-primary">{{ $ticket->ticket_number }}</span>
                                <span class="badge rounded-pill {{ $ticket->getStatusBadgeClass() }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                                <span class="badge rounded-pill text-bg-warning text-dark">
                                    {{ $ticket->priority->priority_name ?? '-' }}
                                </span>
                            </div>

                            <h4 class="fw-bold mb-2">{{ $ticket->title }}</h4>
                            <p class="text-muted mb-4">{{ $ticket->description }}</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3">
                                        <p class="text-muted small mb-1">Kategori</p>
                                        <p class="fw-semibold mb-0">{{ $ticket->category->category_name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3">
                                        <p class="text-muted small mb-1">Prioritas</p>
                                        <p class="fw-semibold mb-0">{{ $ticket->priority->priority_name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3">
                                        <p class="text-muted small mb-1">Tenggat</p>
                                        <p class="fw-semibold mb-0">
                                            {{ $ticket->due_date ? $ticket->due_date->format('d M Y, H:i') : '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3">
                                        <p class="text-muted small mb-1">Label</p>
                                        <p class="fw-semibold mb-0">
                                            @if ($ticket->labels->isNotEmpty())
                                                @foreach ($ticket->labels as $label)
                                                    <span
                                                        class="badge text-bg-light text-dark me-1 mb-1">{{ $label->label_name }}</span>
                                                @endforeach
                                            @else
                                                Tidak ada label
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom kanan: Status & Action Tiket --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-arrow-repeat"></i>
                                <span class="fw-semibold">Status Tiket</span>
                            </div>

                            <p class="text-muted small mb-1">Status saat ini</p>
                            <div class="mb-3">
                                <span class="badge rounded-pill {{ $ticket->getStatusBadgeClass() }} px-3 py-2">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </div>

                            @php
                                $canReopen = in_array($ticket->status, ['resolved', 'closed']);
                            @endphp

                            <form action="#" method="POST">
                                @csrf
                                <button type="submit"
                                    class="btn {{ $canReopen ? 'btn-primary' : 'btn-secondary' }} w-100 rounded-pill"
                                    {{ $canReopen ? '' : 'disabled' }}>
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reopen Tiket
                                </button>
                            </form>
                            <p class="text-muted small mt-2 mb-0">
                                Tersedia hanya jika status resolved atau closed.
                            </p>
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
                        <div class="border rounded-3 p-2 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center text-truncate me-2">
                                <i class="bi bi-file-earmark-arrow-down text-primary fs-4 me-2"></i>
                                <div class="text-truncate">
                                    <div class="fw-semibold small text-truncate" title="{{ $attachment->original_name }}">{{ $attachment->original_name }}</div>
                                    <span class="text-muted extra-small">{{ number_format($attachment->file_size / 1024, 1) }} KB</span>
                                </div>
                            </div>
                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill"><i class="bi bi-download"></i></a>
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
                    <form action="{{ route('customer.comments.store', $ticket) }}" method="POST"
                        enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="body" class="form-label fw-semibold">Tambah Balasan</label>
                            <textarea name="body" id="body" rows="3" class="form-control @error('body') is-invalid @enderror"
                                placeholder="Tulis tanggapan atau instruksi untuk tiket ini..." required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-md-7">
                                <label for="attachments" class="form-label small text-muted mb-1">
                                    Tambah Lampiran (Opsional)
                                </label>
                                <input type="file" name="attachments[]" id="attachments"
                                    class="form-control form-control-sm" multiple>
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
                            {{-- Policy View: Jika internal_note & user customer, otomatis tersembunyi --}}
                            @can('view', $comment)
                                <div
                                    class="card border-0 {{ $comment->type === 'internal_note' ? 'bg-warning-subtle border-start border-warning border-4' : 'bg-light' }} rounded-3 mb-3">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="fw-bold text-dark">{{ $comment->user->name ?? 'Pengguna' }}</div>
                                                <span class="badge rounded-pill bg-secondary text-capitalize"
                                                    style="font-size: 0.7rem;">
                                                    {{ $comment->user->role->role_name ?? 'User' }}
                                                </span>
                                                @if ($comment->type === 'internal_note')
                                                    <span class="badge bg-warning text-dark"><i
                                                            class="bi bi-lock-fill me-1"></i> Catatan Internal</span>
                                                @endif
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <small
                                                    class="text-muted fs-7">{{ $comment->created_at->diffForHumans() }}</small>
                                                @can('delete', $comment)
                                                    <form action="{{ route('customer.comments.destroy', [$ticket, $comment]) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0 ms-1 border-0"
                                                            title="Hapus Komentar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>

                                        <div class="text-dark mb-2" style="white-space: pre-line;">{{ $comment->body }}</div>

                                        {{-- Lampiran Pada Komentar --}}
                                        @if ($comment->attachments && $comment->attachments->count() > 0)
                                            <div class="mt-3 pt-2 border-top border-secondary-subtle">
                                                <div class="small fw-semibold text-muted mb-2">
                                                    <i class="bi bi-paperclip me-1"></i> Lampiran Balasan:
                                                </div>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($comment->attachments as $attachment)
                                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
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
                            @endcan
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
                        <p class="text-muted small mb-0">Belum ada riwayat aktivitas yang tercatat.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
