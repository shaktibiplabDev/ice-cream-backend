@extends('layouts.admin')

@section('title', 'Edit Warehouse')

@section('content')
    <div class="page-header">
        <h1>
            <small>Celesty Storage Network</small>
            Edit Warehouse: {{ $warehouse->name }}
        </h1>
        <div>
            <a href="{{ route('admin.warehouses.index') }}" class="btn-secondary">← Back to Warehouses</a>
        </div>
    </div>

    <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST" class="form-panel">
        @csrf
        @method('PUT')

        <div class="form-panel-head">
            <h2>Warehouse Information</h2>
        </div>

        <div class="form-panel-body">
            @include('admin.warehouses._form')

            <div class="form-actions">
                <a href="{{ route('admin.warehouses.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">💾 Update Warehouse</button>
            </div>
        </div>
    </form>
@endsection
