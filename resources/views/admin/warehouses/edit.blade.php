@extends('layouts.admin')

@section('title', 'Edit Warehouse')

@section('content')
    <div class="page-header">
        <h1>
            <small>Celesty Storage Network</small>
            Edit Warehouse: {{ $warehouse->name }}
        </h1>
        <div>
            <a href="{{ route('admin.warehouses.index') }}" style="text-decoration: none;"><span class="btn-secondary">← Back to Warehouses</span></a>
        </div>
    </div>

    <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST" class="form-panel">
        @csrf
        @method('PUT')

        @include('admin.warehouses._form', ['mode' => 'edit'])
    </form>
@endsection
