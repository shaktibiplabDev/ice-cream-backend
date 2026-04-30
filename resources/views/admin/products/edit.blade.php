@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
    <div class="page-header">
        <h1>
            <small>Product Catalog</small>
            Edit Product: {{ $product->name }}
        </h1>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">← Back to Products</a>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="form-panel">
        @csrf
        @method('PUT')

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
                    <input type="text" name="name" class="form-input" value="{{ old('name', $product->name) }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        SKU
                        <span class="required-label">Required</span>
                    </label>
                    <input type="text" name="sku" class="form-input" value="{{ old('sku', $product->sku) }}" required>
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
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                    <input type="text" name="size" class="form-input" value="{{ old('size', $product->size) }}" required>
                    @error('size')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        MRP Price (Customer)
                        <span class="required-label">Required</span>
                    </label>
                    <input type="number" name="mrp_price" class="form-input" value="{{ old('mrp_price', $product->mrp_price) }}" step="0.01" min="0" required>
                    @error('mrp_price')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Distributor Price
                        <span class="required-label">Required</span>
                    </label>
                    <input type="number" name="distributor_price" class="form-input" value="{{ old('distributor_price', $product->distributor_price) }}" step="0.01" min="0" required>
                    @error('distributor_price')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Retailer Price
                        <span class="required-label">Required</span>
                    </label>
                    <input type="number" name="retailer_price" class="form-input" value="{{ old('retailer_price', $product->retailer_price) }}" step="0.01" min="0" required>
                    @error('retailer_price')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Unit
                        <span class="required-label">Required</span>
                    </label>
                    <input type="text" name="unit" class="form-input" value="{{ old('unit', $product->unit) }}" required>
                    @error('unit')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Low Stock Threshold
                        <span class="optional-label">Optional</span>
                    </label>
                    <input type="number" name="low_stock_threshold" class="form-input" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0">
                    <span class="form-help">Alert when stock falls below this number</span>
                    @error('low_stock_threshold')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Status
                    </label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $product->is_active) === false ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field full">
                    <label class="form-label">
                        Description
                        <span class="optional-label">Optional</span>
                    </label>
                    <textarea name="description" class="form-textarea" rows="3">{{ old('description', $product->description) }}</textarea>
                    @error('description')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-field full">
                    <label class="form-label">
                        Product Image
                        <span class="optional-label">Optional</span>
                    </label>
                    @if($product->image)
                        <div style="margin-bottom: 0.5rem;">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="" style="width: 100px; height: 100px; border-radius: 8px; object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" name="image" class="form-input" accept="image/*">
                    <span class="form-help">Upload new image to replace current (Max 2MB)</span>
                    @error('image')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.products.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">💾 Update Product</button>
            </div>
        </div>
    </form>
@endsection
