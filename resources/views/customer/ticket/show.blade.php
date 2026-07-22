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
                                                        class="badge text-bg-primary me-1">{{ $label->label_name }}</span>
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

                            {{-- <form action="{{ route('customer.tickets.reopen', $ticket) }}" method="POST"> --}}
                            <form action="#" method="POST">
                                @csrf
                                <button type="submit" class="btn {{ $canReopen ? 'btn-primary' : 'btn-secondary' }} w-100 rounded-pill"
                                    {{ $canReopen ? '' : 'disabled'}}>
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

            {{-- Komentar & Lampiran --}}
            <div class="row g-3 mt-0">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Komentar dan lampiran</h6>
                        </div>
                        <div class="card-body">

                            {{-- Daftar komentar yang sudah ada --}}
                            @forelse ($ticket->comments as $comment)
                                <div class="d-flex gap-3 mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-initials rounded-circle bg-primary">
                                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-semibold">{{ $comment->user->name }}</h6>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        <p class="mb-0 mt-2">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                                <hr class="my-3">
                            @empty
                                <p class="text-muted mb-0">Belum ada komentar</p>
                            @endforelse

                            {{-- Form tulis komentar --}}
                            <div class="mt-3">
                                <form action="{{ route('customer.comments.store', $ticket->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="comment" class="form-label fw-semibold small">Tulis komentar</label>
                                        <textarea id="comment" name="comment" class="form-control @error('comment') is-invalid @enderror" rows="3"
                                            placeholder="Tulis komentar kamu di sini..."></textarea>
                                        @error('comment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="attachment" class="form-label fw-semibold small">Tambah lampiran
                                            (opsional)</label>
                                        <input type="file" id="attachment" name="attachment"
                                            class="form-control @error('attachment') is-invalid @enderror">
                                        <div class="form-text">Maks. 10MB. Format: jpg, png, pdf, docx.</div>
                                        @error('attachment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-send me-1"></i>Kirim komentar
                                    </button>
                                </form>
                            </div>

                            {{-- Daftar lampiran yang sudah ada --}}
                            @if ($ticket->attachments->isNotEmpty())
                                <hr class="my-4">
                                <div>
                                    <h6 class="fw-semibold mb-3">File terlampir</h6>
                                    @foreach ($ticket->attachments as $attachment)
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-paperclip text-muted"></i>
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" download
                                                class="text-decoration-none">
                                                {{ $attachment->file_name }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat aktivitas --}}
            <div class="row g-3 mt-0">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-clock-history me-1"></i>Riwayat aktivitas
                            </h6>
                        </div>
                        <div class="card-body">
                            @forelse ($ticket->activityLogs as $activity)
                                <div class="d-flex gap-3 mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-initials rounded-circle bg-secondary">
                                                <i class="bi bi-dot"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-semibold">{{ $activity->action }}</h6>
                                        <p class="mb-0 small text-muted">{{ $activity->description ?? '-' }}</p>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <hr class="my-3">
                            @empty
                                <p class="text-muted mb-0 text-center">Belum ada aktivitas yang tercatat.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
