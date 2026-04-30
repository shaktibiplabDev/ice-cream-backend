@extends('layouts.admin')

@section('title', 'Add Warehouse')

@section('content')
    <div class="page-header">
        <h1>
            <small>Celesty Storage Network</small>
            Add New Warehouse
        </h1>
        <div>
            <a href="{{ route('admin.warehouses.index') }}" class="btn-secondary">← Back to Warehouses</a>
        </div>
    </div>

    <form action="{{ route('admin.warehouses.store') }}" method="POST" class="form-panel">
        @csrf

        <div class="form-panel-head">
            <h2>Warehouse Information</h2>
        </div>

        <div class="form-panel-body">
            @include('admin.warehouses._form')

            <div class="form-actions">
                <a href="{{ route('admin.warehouses.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">💾 Save Warehouse</button>
            </div>
        </div>
    </form>
@endsection
