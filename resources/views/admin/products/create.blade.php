@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
    <div class="page-header">
        <h1>
            <small>Product Catalog</small>
            Add New Product
        </h1>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">← Back to Products</a>
        </div>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="form-panel">
        @csrf

        <div class="form-panel-head">
            <h2>Product Information</h2>
        </div>

        <div class="form-panel-body">
            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label">
                        Product Name
                        <span class="required-label">Required</span>
                    </label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        SKU
                        <span class="required-label">Required</span>
                    </label>
                    <input type="text" name="sku" class="form-input" value="{{ old('sku') }}" required>
                    @error('sku')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Category
                        <span class="optional-label">Optional</span>
                    </label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon ?? '🏷️' }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Size
                        <span class="required-label">Required</span>
                    </label>
                    <input type="text" name="size" class="form-input" value="{{ old('size') }}" placeholder="e.g., 500ml, 1L, 5L" required>
                    @error('size')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        MRP Price (Customer)
                        <span class="required-label">Required</span>
                    </label>
                    <input type="number" name="mrp_price" class="form-input" value="{{ old('mrp_price') }}" step="0.01" min="0" required>
                    @error('mrp_price')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Distributor Price
                        <span class="required-label">Required</span>
                    </label>
                    <input type="number" name="distributor_price" class="form-input" value="{{ old('distributor_price') }}" step="0.01" min="0" required>
                    @error('distributor_price')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Retailer Price
                        <span class="required-label">Required</span>
                    </label>
                    <input type="number" name="retailer_price" class="form-input" value="{{ old('retailer_price') }}" step="0.01" min="0" required>
                    @error('retailer_price')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Unit
                        <span class="required-label">Required</span>
                    </label>
                    <input type="text" name="unit" class="form-input" value="{{ old('unit', 'unit') }}" placeholder="e.g., tub, box, case" required>
                    @error('unit')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Low Stock Threshold
                        <span class="optional-label">Optional</span>
                    </label>
                    <input type="number" name="low_stock_threshold" class="form-input" value="{{ old('low_stock_threshold', 10) }}" min="0">
                    <span class="form-help">Alert when stock falls below this number</span>
                    @error('low_stock_threshold')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Status
                    </label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', true) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field full">
                    <label class="form-label">
                        Description
                        <span class="optional-label">Optional</span>
                    </label>
                    <textarea name="description" class="form-textarea" rows="3">{{ old('description') }}</textarea>
                    @error('description')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field full">
                    <label class="form-label">
                        Product Image
                        <span class="optional-label">Optional</span>
                    </label>
                    <input type="file" name="image" class="form-input" accept="image/*">
                    <span class="form-help">Max 2MB. JPG, PNG, or GIF</span>
                    @error('image')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">💾 Save Product</button>
            </div>
        </div>
    </form>
@endsection
