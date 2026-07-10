@extends('adminlte::page')

@section('title', 'Buat Tiket')

@section('content_header')
    <h1>Buat Tiket Baru</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.tickets.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @include('admin.tickets._form')
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
