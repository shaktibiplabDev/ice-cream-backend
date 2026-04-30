@extends('layouts.admin')

@section('title', $warehouse->name)

@section('content')
    <div class="page-header">
        <h1>
            <small>Warehouse Details</small>
            {{ $warehouse->name }}
        </h1>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.warehouses.index') }}" class="btn-secondary">← Back</a>
            <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" class="btn-primary">✏️ Edit</a>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Warehouse Info Card -->
        <div class="glass-card" style="grid-column: 1 / -1;">
            <div class="card-head">
                <h2>🏭 Warehouse Information</h2>
                <span class="status-badge {{ $warehouse->is_active ? 'status-active' : 'status-inactive' }}">
                    {{ $warehouse->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div class="detail-item">
                        <span class="detail-label">Code</span>
                        <span class="detail-value">{{ $warehouse->code }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Manager</span>
                        <span class="detail-value">{{ $warehouse->manager_name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value">{{ $warehouse->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">{{ $warehouse->email ?? 'N/A' }}</span>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-subtle);">
                    <div class="detail-item">
                        <span class="detail-label">Full Address</span>
                        <span class="detail-value">{{ $warehouse->full_address }}</span>
                    </div>
                    @if($warehouse->map_url)
                        <div style="margin-top: 1rem;">
                            <a href="{{ $warehouse->map_url }}" target="_blank" class="btn-primary" style="font-size: 0.875rem; padding: 0.5rem 1rem;">📍 View on Google Maps</a>
                        </div>
                    @endif
                </div>

                @if($warehouse->notes)
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-subtle);">
                        <span class="detail-label">Notes</span>
                        <p style="margin-top: 0.5rem; color: var(--text-secondary);">{{ $warehouse->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Current Inventory -->
        <div class="glass-card">
            <div class="card-head">
                <h2>📦 Current Inventory</h2>
                <a href="{{ route('admin.inventory.index', ['warehouse_id' => $warehouse->id]) }}" class="action-btn action-view">View All</a>
            </div>
            <div class="table-wrap" style="padding: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouse->inventory->take(5) as $inv)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    @if($inv->product->image)
                                        <img src="{{ asset('storage/' . $inv->product->image) }}" alt="" style="width: 30px; height: 30px; border-radius: 6px; object-fit: cover;">
                                    @else
                                        <div style="width: 30px; height: 30px; border-radius: 6px; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">🍦</div>
                                    @endif
                                    {{ $inv->product->name }}
                                </div>
                            </td>
                            <td style="font-weight: 600;">{{ $inv->quantity }}</td>
                            <td>
                                @if($inv->isLowStock())
                                    <span class="status-badge status-inactive">Low</span>
                                @else
                                    <span class="status-badge status-active">OK</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="empty-state" style="padding: 2rem;">
                                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📦</div>
                                <div>No inventory</div>
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
                <h2>⚡ Quick Actions</h2>
            </div>
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="{{ route('admin.inventory.create', ['warehouse_id' => $warehouse->id]) }}" class="btn-primary" style="text-align: center;">➕ Add Stock</a>
                <a href="{{ route('admin.inventory.history', ['warehouse_id' => $warehouse->id]) }}" class="btn-secondary" style="text-align: center;">📜 View History</a>
                <form action="{{ route('admin.warehouses.destroy', $warehouse->id) }}" method="POST" onsubmit="return confirm('Delete this warehouse?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" style="width: 100%;">🗑️ Delete Warehouse</button>
                </form>
            </div>
        </div>
    </div>
@endsection
