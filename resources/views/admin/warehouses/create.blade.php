@extends('layouts.admin')

@section('title', 'Add Warehouse')

@section('content')
    <div class="page-header">
        <h1>
            <small>Celesty Storage Network</small>
            Add New Warehouse
        </h1>
        <div>
            <a href="{{ route('admin.warehouses.index') }}" style="text-decoration: none;"><span class="btn-secondary">← Back to Warehouses</span></a>
        </div>
    </div>

    <form action="{{ route('admin.warehouses.store') }}" method="POST" class="form-panel">
        @csrf

        @include('admin.warehouses._form', ['mode' => 'create'])
    </form>
@endsection
