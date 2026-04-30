@extends('layouts.admin')

@section('title', 'Stock History')

@section('content')
    <div class="page-header">
        <h1>
            <small>Stock Management</small>
            Stock Movement History
        </h1>
        <div>
            <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">← Back to Inventory</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card" style="margin-bottom: 1.5rem;">
        <div style="padding: 1.25rem;">
            <form method="GET" action="{{ route('admin.inventory.history') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <div class="form-field" style="flex: 1; min-width: 200px; margin: 0;">
                    <label class="form-label">Warehouse</label>
                    <select name="warehouse_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field" style="flex: 1; min-width: 200px; margin: 0;">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Products</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <a href="{{ route('admin.inventory.history') }}" class="btn-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <div class="glass-card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Warehouse</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Balance After</th>
                        <th>Notes</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('M d, Y') }}<br><small style="color: var(--text-muted);">{{ $movement->created_at->format('H:i') }}</small></td>
                        <td>{{ $movement->warehouse->name ?? 'N/A' }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                @if($movement->product->image)
                                    <img src="{{ asset('storage/' . $movement->product->image) }}" alt="" style="width: 30px; height: 30px; border-radius: 6px; object-fit: cover;">
                                @else
                                    <div style="width: 30px; height: 30px; border-radius: 6px; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">🍦</div>
                                @endif
                                {{ $movement->product->name }}
                            </div>
                        </td>
                        <td>
                            @if($movement->type == 'in')
                                <span style="color: #10b981; font-weight: 600;">➕ IN</span>
                            @elseif($movement->type == 'out')
                                <span style="color: #ef4444; font-weight: 600;">➖ OUT</span>
                            @else
                                <span style="color: #f59e0b; font-weight: 600;">🔄 ADJUST</span>
                            @endif
                        </td>
                        <td style="font-weight: 600;">{{ $movement->quantity }}</td>
                        <td>{{ $movement->balance_after }}</td>
                        <td>{{ Str::limit($movement->notes, 30) ?: '-' }}</td>
                        <td>{{ $movement->creator?->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="empty-state-icon">📜</div>
                            <div>No stock movements found</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="pagination">
            <div class="pagination-info">
                Showing {{ $movements->firstItem() ?? 0 }} to {{ $movements->lastItem() ?? 0 }} of {{ $movements->total() }} movements
            </div>
            <div class="pagination-links">
                {{ $movements->onEachSide(1)->links('pagination::simple-tailwind') }}
            </div>
        </div>
        @endif
    </div>
@endsection
