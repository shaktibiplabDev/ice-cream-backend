@extends('layouts.admin')

@section('title', 'Low Stock Alert')

@section('content')
    <div class="page-header">
        <h1>
            <small>Stock Management</small>
            Low Stock Alerts
        </h1>
        <div>
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">← Back to Inventory</a>
        </div>
    </div>

    @if($lowStockItems->count() > 0)
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 1.5rem;">
            <div class="stat-card" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3);">
                <div class="stat-value" style="color: #f87171;">{{ $lowStockItems->count() }}</div>
                <div class="stat-label">Items Below Threshold</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $lowStockItems->sum('quantity') }}</div>
                <div class="stat-label">Total Units Low</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $lowStockItems->unique('product_id')->count() }}</div>
                <div class="stat-label">Products Affected</div>
            </div>
        </div>

        <div class="glass-card">
            <div class="card-head">
                <h2>⚠️ Items Requiring Attention</h2>
                <a href="{{ route('admin.inventory.create') }}" class="btn-primary" style="font-size: 0.75rem; padding: 0.5rem 1rem;">➕ Add Stock</a>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Distributor</th>
                            <th>Current Stock</th>
                            <th>Threshold</th>
                            <th>Shortage</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockItems as $item)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 8px; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center;">🍦</div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 500;">{{ $item->product->name }}</div>
                                        <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $item->product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->distributor->name ?? 'Unknown' }}</td>
                            <td style="font-weight: 600; color: #f87171;">{{ $item->quantity }}</td>
                            <td>{{ $item->product->low_stock_threshold }}</td>
                            <td style="font-weight: 600; color: #ef4444;">{{ $item->product->low_stock_threshold - $item->quantity }}</td>
                            <td>
                                <a href="{{ route('admin.inventory.edit', $item->id) }}" class="btn-primary" style="font-size: 0.75rem; padding: 0.375rem 0.75rem;">Manage Stock</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="glass-card">
            <div class="empty-state" style="padding: 4rem 2rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                <div style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">All Stock Levels Healthy!</div>
                <div style="color: var(--text-muted);">No products are currently below their low stock threshold.</div>
            </div>
        </div>
    @endif
@endsection
