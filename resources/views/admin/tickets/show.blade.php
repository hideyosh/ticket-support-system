@extends('adminlte::page')

@section('title', 'Detail Tiket — ' . $ticket->ticket_number)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Detail Tiket — <strong>{{ $ticket->ticket_number }}</strong></h1>
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        {{-- Kolom Kiri: Info Tiket --}}
        <div class="col-md-8">
            {{-- Informasi Tiket --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ $ticket->title }}</h3>
                </div>
                <div class="card-body">
                    <p>{{ $ticket->description }}</p>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Kategori:</strong> {{ $ticket->category->name ?? '-' }}<br>
                            <strong>Prioritas:</strong> {{ $ticket->priority->priority_name ?? '-' }}<br>
                            <strong>Dibuat oleh:</strong> {{ $ticket->creator->name ?? '-' }}<br>
                            <strong>Dibuat pada:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}<br>
                        </div>
                        <div class="col-sm-6">
                            <strong>Agent:</strong> {{ $ticket->assignedAgent->name ?? '<em>Belum ditugaskan</em>' }}<br>
                            <strong>Tenggat:</strong>
                            @if($ticket->due_date)
                                {{ $ticket->due_date->format('d/m/Y H:i') }}
                            @else
                                -
                            @endif<br>
                            <strong>Diselesaikan:</strong> {{ $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : '-' }}<br>
                            <strong>Ditutup:</strong> {{ $ticket->closed_at ? $ticket->closed_at->format('d/m/Y H:i') : '-' }}<br>
                        </div>
                    </div>
                    @if($ticket->labels->count())
                        <hr>
                        <div>
                            <strong>Label:</strong>
                            @foreach($ticket->labels as $label)
                                <span class="badge badge-info">{{ $label->label_name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Komentar & Internal Note --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Komentar & Internal Note</h3>
                </div>
                <div class="card-body">
                    @forelse($ticket->comments as $comment)
                        <div class="card {{ $comment->type === 'internal_note' ? 'card-warning' : 'card-light' }} mb-2">
                            <div class="card-header py-1">
                                <strong>{{ $comment->user->name ?? 'User Dihapus' }}</strong>
                                <span class="badge {{ $comment->type === 'internal_note' ? 'badge-warning' : 'badge-secondary' }} ml-2">
                                    {{ $comment->type === 'internal_note' ? 'Internal Note' : 'Komentar Publik' }}
                                </span>
                                <span class="float-right text-muted small">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="card-body py-2">
                                {{ $comment->body }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada komentar.</p>
                    @endforelse
                </div>
                <div class="card-footer">
                    <form action="{{ route('admin.tickets.comments', $ticket) }}" method="POST">
                        @csrf
                        <input type="hidden" name="_add_comment" value="1">
                        <div class="form-group">
                            <textarea name="body" class="form-control" rows="3" placeholder="Tulis komentar..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="type" id="is_internal" value="internal_note">
                                <label class="form-check-label" for="is_internal">Internal Note (hanya dilihat tim)</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Panel Aksi --}}
        <div class="col-md-4">
            {{-- Status --}}
            <div class="card card-secondary">
                <div class="card-header"><h3 class="card-title">Ubah Status</h3></div>
                <div class="card-body">
                    @php
                        $statusColors = [
                            'open' => 'secondary', 'assigned' => 'info',
                            'in_progress' => 'primary', 'waiting_for_customer' => 'warning',
                            'resolved' => 'success', 'closed' => 'dark',
                            'reopened' => 'danger', 'escalated' => 'danger',
                        ];
                    @endphp
                    <p>Status saat ini: <span class="badge badge-{{ $statusColors[$ticket->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></p>
                    @if(count($allowedStatuses) > 0)
                        <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="form-group">
                                <select name="status" class="form-control" required>
                                    <option value="">Pilih Status Baru...</option>
                                    @foreach($allowedStatuses as $status)
                                        <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Ubah Status</button>
                        </form>
                    @else
                        <p class="text-muted">Tidak ada transisi status yang tersedia.</p>
                    @endif
                </div>
            </div>

            {{-- Assign Agent --}}
            <div class="card card-secondary">
                <div class="card-header"><h3 class="card-title">Assign Agent</h3></div>
                <div class="card-body">
                    <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="form-group">
                            <select name="assigned_to" class="form-control">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info btn-block">Simpan Assignment</button>
                    </form>
                </div>
            </div>

            {{-- Label --}}
            <div class="card card-secondary">
                <div class="card-header"><h3 class="card-title">Kelola Label</h3></div>
                <div class="card-body">
                    <form action="{{ route('admin.tickets.labels', $ticket) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="form-group">
                            @foreach($allLabels as $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="labels[]" id="label_{{ $label->id }}" value="{{ $label->id }}"
                                        {{ $ticket->labels->contains($label->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="label_{{ $label->id }}">{{ $label->label_name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-warning btn-block">Simpan Label</button>
                    </form>
                </div>
            </div>

            {{-- Edit & Hapus --}}
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.tickets.edit', $ticket) }}" class="btn btn-warning btn-block"><i class="fas fa-edit"></i> Edit Tiket</a>
                    <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin ingin menghapus tiket ini? Tindakan ini tidak bisa dibatalkan.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block"><i class="fas fa-trash"></i> Hapus Tiket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
