@extends('adminlte::page')

@section('title', 'Edit SLA Rule')

@section('content_header')
    <h1>Edit SLA Rule</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.sla-rules.update', $slaRule) }}" method="POST">
            @csrf @method('PUT')
            <div class="card-body">
                @include('admin.sla_rules._form', ['slaRule' => $slaRule])
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.sla-rules.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@stop
