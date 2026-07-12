@extends('layouts.app')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="mb-0">Data SLA Rules</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">SLA Rules</li>
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
                        <i class="fas fa-list me-1"></i>
                        Daftar SLA Rules
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.sla-rules.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah SLA Rule
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible m-3">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Prioritas</th>
                                <th>Response Time (Jam)</th>
                                <th>Resolution Time (Jam)</th>
                                <th style="width: 150px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($slaRules as $rule)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $rule->priority->priority_name ?? '-' }}</td>
                                    <td>{{ $rule->response_time }} jam</td>
                                    <td>{{ $rule->resolution_time }} jam</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.sla-rules.edit', $rule->id) }}"
                                            class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('admin.sla-rules.destroy', $rule->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus SLA rule ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data SLA rule.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($slaRules, 'hasPages') && $slaRules->hasPages())
                    <div class="card-footer">
                        {{ $slaRules->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
