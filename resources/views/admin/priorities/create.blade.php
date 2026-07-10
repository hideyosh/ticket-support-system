@extends('adminlte::page')

@section('title', 'Tambah Prioritas')

@section('content_header')
    <h1>Tambah Prioritas</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.priorities.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @include('admin.priorities._form')
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.priorities.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
