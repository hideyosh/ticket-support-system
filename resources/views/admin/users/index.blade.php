@extends('layouts.app')
@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 class="mb-0">Data User</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">User</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            @php
                $activeRoleIds = array_filter(explode(',', (string) request('role_id')));
            @endphp

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users me-1"></i>
                        Daftar User
                    </h3>
                    <div class="card-tools d-flex gap-2 align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle border" type="button" id="filterDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <i class="fas fa-filter text-muted me-1"></i> Filter
                                @if (count($activeRoleIds) > 0)
                                    <span class="badge bg-primary ms-1 rounded-pill">{{ count($activeRoleIds) }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3 shadow" aria-labelledby="filterDropdown"
                                style="min-width: 240px;">
                                <form action="{{ route('admin.users.index') }}" method="GET" id="filterForm"
                                    class="m-0">
                                    <input type="hidden" name="role_id" id="role_id_combined"
                                        value="{{ request('role_id') }}">

                                    <h6 class="dropdown-header px-0 text-dark fw-bold border-bottom pb-2 mb-2">Berdasarkan
                                        Role</h6>

                                    <div class="mb-3">
                                        @foreach ($roles as $role)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input role-filter-checkbox" type="checkbox"
                                                    id="role_{{ $role->id }}" value="{{ $role->id }}"
                                                    {{ in_array((string) $role->id, $activeRoleIds) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="font-size: 0.875rem;"
                                                    for="role_{{ $role->id }}">
                                                    {{ ucfirst($role->role_name) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="d-flex justify-content-between pt-2 border-top">
                                        <a href="{{ route('admin.users.index') }}"
                                            class="btn btn-light btn-sm text-danger">Reset</a>
                                        <button type="submit" class="btn btn-primary btn-sm px-3">Terapkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm text-nowrap">
                            <i class="fas fa-plus"></i> Tambah User
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
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th style="width: 180px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="text-center">
                                        {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->role)
                                            <span
                                                class="badge text-bg-primary">{{ ucfirst($user->role->role_name) }}</span>
                                        @else
                                            <span class="badge text-bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm"
                                            title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
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
                                    <td colspan="5" class="text-center">Belum ada data user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($users, 'hasPages') && $users->hasPages())
                    <div class="card-footer">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            var checked = document.querySelectorAll('.role-filter-checkbox:checked');
            var ids = Array.prototype.map.call(checked, function(el) {
                return el.value;
            });
            document.getElementById('role_id_combined').value = ids.join(',');
        });
    </script>
@endsection
