@extends('adminlte::page')

@section('title', 'Edit Prioritas')

@section('content_header')
    <h1>Edit Prioritas</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.priorities.update', $priority) }}" method="POST">
            @csrf @method('PUT')
            <div class="card-body">
                @include('admin.priorities._form', ['priority' => $priority])
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.priorities.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
