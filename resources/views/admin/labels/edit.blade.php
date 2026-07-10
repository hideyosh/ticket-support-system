@extends('adminlte::page')

@section('title', 'Edit Label')

@section('content_header')
    <h1>Edit Label</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.labels.update', $label) }}" method="POST">
            @csrf @method('PUT')
            <div class="card-body">
                @include('admin.labels._form', ['label' => $label])
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.labels.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
