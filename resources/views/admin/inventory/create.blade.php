@extends('layouts.admin')

@section('title', 'Add Stock')

@section('content')
    <div class="page-header">
        <h1>
            <small>Stock Management</small>
            Add Stock to Warehouse
        </h1>
        <div>
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">← Back to Inventory</a>
        </div>
    </div>

    <form action="{{ route('admin.inventory.store') }}" method="POST" class="form-panel">
        @csrf

        <div class="form-panel-head">
            <h2>Stock Entry</h2>
        </div>

        <div class="form-panel-body">
            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label">
                        Warehouse
                        <span class="required-label">Required</span>
                    </label>
                    <select name="warehouse_id" class="form-select" required>
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} - {{ $wh->city }}
                            </option>
                        @endforeach
                    </select>
                    @error('warehouse_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Product
                        <span class="required-label">Required</span>
                    </label>
                    <select name="product_id" class="form-select" required>
                        <option value="">Select Product</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->name }} ({{ $prod->sku }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Quantity
                        <span class="required-label">Required</span>
                    </label>
                    <input type="number" name="quantity" class="form-input" value="{{ old('quantity') }}" step="0.01" min="0.01" required>
                    <span class="form-help">Amount to add to inventory</span>
                    @error('quantity')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Storage Location
                        <span class="optional-label">Optional</span>
                    </label>
                    <input type="text" name="location" class="form-input" value="{{ old('location') }}" placeholder="e.g., Cold Storage A, Shelf 3">
                    @error('location')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field full">
                    <label class="form-label">
                        Notes
                        <span class="optional-label">Optional</span>
                    </label>
                    <textarea name="notes" class="form-textarea" rows="3" placeholder="Any additional information about this stock entry...">{{ old('notes') }}</textarea>
                    @error('notes')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">➕ Add Stock</button>
            </div>
        </div>
    </form>
@endsection
