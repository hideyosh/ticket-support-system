@extends('adminlte::page')

@section('title', 'Edit Tiket')

@section('content_header')
    <h1>Edit Tiket — {{ $ticket->ticket_number }}</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
            @csrf @method('PUT')
            <div class="card-body">
                @include('admin.tickets._form', ['ticket' => $ticket])
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
