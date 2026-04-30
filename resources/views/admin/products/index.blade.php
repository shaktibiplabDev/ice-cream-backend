@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="page-header">
        <h1>
            <small>Product Catalog</small>
            All Products
        </h1>
        <div>
            <a href="{{ route('admin.products.create') }}" class="btn-primary">➕ Add New Product</a>
        </div>
    </div>

    <div class="filter-bar">
        <div class="filter-group">
            <select class="filter-select" onchange="window.location.href=this.value">
                <option value="{{ route('admin.products.index') }}" {{ !request('status') ? 'selected' : '' }}>All Status</option>
                <option value="{{ route('admin.products.index', ['status' => 'active']) }}" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="{{ route('admin.products.index', ['status' => 'inactive']) }}" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div style="flex:1"></div>
        <div class="date-badge">
            <span>📦</span>
            Total: {{ $products->total() }}
        </div>
    </div>

    <div class="glass-card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="order-id">#{{ $product->id }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                @else
                                    <div style="width: 40px; height: 40px; border-radius: 8px; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">🍦</div>
                                @endif
                                <div>
                                    <div style="font-weight: 500;">{{ $product->name }}</div>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $product->size }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->category }}</td>
                        <td>{{ \App\Models\CompanySetting::getSettings()->currency_symbol }}{{ number_format($product->price, 2) }} / {{ $product->unit }}</td>
                        <td>
                            <span class="status-badge {{ $product->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.products.show', $product->id) }}" class="action-btn action-view">👁️ View</a>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="action-btn action-edit">✏️ Edit</a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete" style="background:none; border:none; cursor:pointer;">🗑️ Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-state-icon">📦</div>
                            <div>No products found</div>
                            <div style="margin-top: 0.5rem;">
                                <a href="{{ route('admin.products.create') }}" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">+ Add your first product</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="pagination">
            <div class="pagination-info">
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
            </div>
            <div class="pagination-links">
                {{ $products->onEachSide(1)->links('pagination::simple-tailwind') }}
            </div>
        </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .inline { display: inline; }
</style>
@endpush
