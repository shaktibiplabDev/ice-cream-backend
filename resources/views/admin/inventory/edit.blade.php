@extends('layouts.admin')

@section('title', 'Manage Stock')

@section('content')
    <div class="page-header">
        <h1>
            <small>Stock Management</small>
            {{ $inventory->product->name }} at {{ $inventory->warehouse->name }}
        </h1>
        <div>
            <a href="{{ route('admin.inventory.index', ['warehouse_id' => $inventory->warehouse_id]) }}" style="text-decoration: none;">
                <span class="btn-secondary">← Back</span>
            </a>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Current Stock Info -->
        <div class="glass-card">
            <div class="card-head">
                <h2>Current Stock</h2>
            </div>
            <div style="padding: 1.5rem; text-align: center;">
                <div style="font-size: 4rem; font-weight: 700; color: var(--accent-primary-light); margin-bottom: 0.5rem;">
                    {{ $inventory->quantity }}
                </div>
                <div style="color: var(--text-muted); margin-bottom: 1rem;">
                    {{ $inventory->product->unit }}s
                </div>
                @if($inventory->isLowStock())
                    <span class="status-badge status-inactive">⚠️ Low Stock Alert</span>
                @else
                    <span class="status-badge status-active">✓ Stock Level OK</span>
                @endif

                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle);">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: left;">
                        <div class="detail-item">
                            <span class="detail-label">Threshold</span>
                            <span class="detail-value">{{ $inventory->product->low_stock_threshold }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Location</span>
                            <span class="detail-value">{{ $inventory->location ?? 'Not set' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Operations -->
        <div class="glass-card">
            <div class="card-head">
                <h2>Stock Operations</h2>
            </div>
            <div style="padding: 1.5rem;">
                <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-field" style="margin-bottom: 1rem;">
                        <label class="form-label">Operation</label>
                        <select name="operation" class="form-select" required>
                            <option value="add">➕ Add Stock</option>
                            <option value="remove">➖ Remove Stock</option>
                            <option value="adjust">🔄 Adjust to Amount</option>
                        </select>
                    </div>

                    <div class="form-field" style="margin-bottom: 1rem;">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-input" step="0.01" min="0" required>
                        <span class="form-help">For Adjust: enter the new total amount</span>
                    </div>

                    <div class="form-field" style="margin-bottom: 1rem;">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-textarea" rows="2" placeholder="Reason for this operation..."></textarea>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%;">Execute Operation</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Stock Movement History -->
    <div class="glass-card" style="margin-top: 1.5rem;">
        <div class="card-head">
            <h2>Recent Movements</h2>
            <a href="{{ route('admin.inventory.history', ['warehouse_id' => $inventory->warehouse_id, 'product_id' => $inventory->product_id]) }}" style="text-decoration: none;">
                <span class="action-btn action-view">View All</span>
            </a>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Notes</th>
                        <th>By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory->stockMovements()->latest()->take(5)->get() as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($movement->type == 'in')
                                <span style="color: #10b981;">➕ In</span>
                            @elseif($movement->type == 'out')
                                <span style="color: #ef4444;">➖ Out</span>
                            @else
                                <span style="color: #f59e0b;">🔄 Adjust</span>
                            @endif
                        </td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ Str::limit($movement->notes, 30) }}</td>
                        <td>{{ $movement->creator?->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state" style="padding: 2rem;">
                            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📜</div>
                            <div>No movements recorded</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
