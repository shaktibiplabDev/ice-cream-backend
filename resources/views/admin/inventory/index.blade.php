@extends('layouts.admin')

@section('title', 'Inventory')

@section('content')
    <div class="page-header">
        <h1>
            <small>Stock Management</small>
            Inventory
        </h1>
        <div>
            <a href="{{ route('admin.inventory.create') }}" class="btn-primary">➕ Add Stock</a>
        </div>
    </div>

    <!-- Distributor Selector -->
    <div class="glass-card" style="margin-bottom: 1.5rem;">
        <div style="padding: 1.25rem;">
            <form method="GET" action="{{ route('admin.inventory.index') }}" style="display: flex; gap: 1rem; align-items: flex-end;">
                <div class="form-field" style="flex: 1; margin: 0;">
                    <label class="form-label">Select Distributor</label>
                    <select name="distributor_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Choose Distributor --</option>
                        @foreach($distributors as $dist)
                            <option value="{{ $dist->id }}" {{ $selectedDistributor?->id == $dist->id ? 'selected' : '' }}>
                                {{ $dist->name }} - {{ $dist->address }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedDistributor)
                    <a href="{{ route('admin.inventory.index') }}" class="btn-secondary">Clear</a>
                @endif
            </form>
        </div>
    </div>

    @if($selectedDistributor)
        <div class="filter-bar">
            <div class="date-badge">
                <span>🏪</span>
                {{ $selectedDistributor->name }}
            </div>
            <div style="flex:1"></div>
            <a href="{{ route('admin.inventory.history', ['distributor_id' => $selectedDistributor->id]) }}" class="btn-secondary">📜 View History</a>
        </div>

        <div class="glass-card">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Current Stock</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $inv)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    @if($inv->product->image)
                                        <img src="{{ asset('storage/' . $inv->product->image) }}" alt="" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 8px; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center;">🍦</div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 500;">{{ $inv->product->name }}</div>
                                        <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $inv->product->category }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $inv->product->sku }}</td>
                            <td style="font-weight: 600; font-size: 1rem;">{{ $inv->quantity }}</td>
                            <td>{{ $inv->location ?? 'Main Warehouse' }}</td>
                            <td>
                                @if($inv->isLowStock())
                                    <span class="status-badge status-inactive">⚠️ Low Stock</span>
                                @else
                                    <span class="status-badge status-active">✓ OK</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.inventory.edit', $inv->id) }}" class="action-btn action-edit">✏️ Manage</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <div class="empty-state-icon">📦</div>
                                <div>No inventory found for this distributor</div>
                                <div style="margin-top: 0.5rem;">
                                    <a href="{{ route('admin.inventory.create', ['distributor_id' => $selectedDistributor->id]) }}" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">+ Add inventory</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($inventory->hasPages())
            <div class="pagination">
                <div class="pagination-info">
                    Showing {{ $inventory->firstItem() ?? 0 }} to {{ $inventory->lastItem() ?? 0 }} of {{ $inventory->total() }} items
                </div>
                <div class="pagination-links">
                    {{ $inventory->onEachSide(1)->links('pagination::simple-tailwind') }}
                </div>
            </div>
            @endif
        </div>
    @else
        <div class="glass-card">
            <div class="empty-state">
                <div class="empty-state-icon">🏪</div>
                <div>Select a distributor to view inventory</div>
            </div>
        </div>
    @endif
@endsection
