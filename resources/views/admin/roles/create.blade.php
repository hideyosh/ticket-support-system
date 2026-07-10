@extends('adminlte::page')

@section('title', 'Tambah Role')

@section('content_header')
    <h1>Tambah Role</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @include('admin.roles._form')
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
