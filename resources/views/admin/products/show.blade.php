@extends('layouts.admin')

@section('title', $product->name)

@section('content')
    <div class="page-header">
        <h1>
            <small>Product Details</small>
            {{ $product->name }}
        </h1>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">← Back</a>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-primary">✏️ Edit</a>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Product Info Card -->
        <div class="glass-card" style="grid-column: 1 / -1;">
            <div class="card-head">
                <h2>Product Information</h2>
                <span class="status-badge {{ $product->is_active ? 'status-active' : 'status-inactive' }}">
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: flex; gap: 2rem; margin-bottom: 1.5rem;">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="" style="width: 120px; height: 120px; border-radius: 12px; object-fit: cover;">
                    @else
                        <div style="width: 120px; height: 120px; border-radius: 12px; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center; font-size: 3rem;">🍦</div>
                    @endif
                    <div style="flex: 1;">
                        <div style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $product->name }}</div>
                        <div style="color: var(--text-muted); margin-bottom: 1rem;">{{ $product->category }} | {{ $product->size }}</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--accent-primary-light);">
                            ${{ number_format($product->price, 2) }} <small style="font-size: 0.75rem; color: var(--text-muted);">/ {{ $product->unit }}</small>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle);">
                    <div class="detail-item">
                        <span class="detail-label">SKU</span>
                        <span class="detail-value">{{ $product->sku }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Low Stock Threshold</span>
                        <span class="detail-value">{{ $product->low_stock_threshold }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Created</span>
                        <span class="detail-value">{{ $product->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                @if($product->description)
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle);">
                        <span class="detail-label">Description</span>
                        <p style="margin-top: 0.5rem; color: var(--text-secondary);">{{ $product->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Inventory at Distributors -->
        <div class="glass-card">
            <div class="card-head">
                <h2>Inventory</h2>
                <a href="{{ route('admin.inventory.index') }}" class="action-btn action-view">View All</a>
            </div>
            <div class="table-wrap" style="padding: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Distributor</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->inventory as $inv)
                        <tr>
                            <td>{{ $inv->distributor->name ?? 'Unknown' }}</td>
                            <td>{{ $inv->quantity }}</td>
                            <td>
                                @if($inv->isLowStock())
                                    <span class="status-badge status-inactive">Low Stock</span>
                                @else
                                    <span class="status-badge status-active">OK</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="empty-state">
                                <div class="empty-state-icon">📦</div>
                                <div>No inventory found</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-card">
            <div class="card-head">
                <h2>Quick Actions</h2>
            </div>
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="{{ route('admin.inventory.create', ['product_id' => $product->id]) }}" class="btn-primary" style="text-align: center;">➕ Add Stock</a>
                <a href="{{ route('admin.inventory.history', ['product_id' => $product->id]) }}" class="btn-secondary" style="text-align: center;">📜 View History</a>
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" style="width: 100%;">🗑️ Delete Product</button>
                </form>
            </div>
        </div>
    </div>
@endsection
