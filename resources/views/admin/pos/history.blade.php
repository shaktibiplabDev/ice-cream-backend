@extends('layouts.admin')

@section('title', 'Sales History')

@section('content')
    <div class="page-header">
        <h1>
            <small>All Transactions</small>
            Sales History
        </h1>
        <a href="{{ route('admin.pos.index') }}" class="btn-primary" style="text-decoration: none;">🛒 New Sale</a>
    </div>

    <div class="glass-card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Distributor</th>
                        <th>Warehouse</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>
                                <span style="font-weight: 600;">{{ $sale->invoice_number }}</span>
                            </td>
                            <td>{{ $sale->sale_date->format('M d, Y h:i A') }}</td>
                            <td>
                                <div>{{ $sale->distributor->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $sale->distributor->contact_person }}</div>
                            </td>
                            <td>
                                <div>{{ $sale->warehouse->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $sale->warehouse->code }}</div>
                            </td>
                            <td>{{ $sale->items->count() }}</td>
                            <td style="font-weight: 600; color: #34d399;">{{ \App\Models\CompanySetting::getSettings()->currency_symbol }}{{ number_format($sale->total_amount, 2) }}</td>
                            <td>
                                <span class="status-badge {{ $sale->payment_status === 'paid' ? 'status-active' : ($sale->payment_status === 'pending' ? 'status-inactive' : 'status-in-progress') }}">
                                    {{ ucfirst($sale->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.pos.bill', $sale->id) }}" style="text-decoration: none;">
                                    <span class="action-btn action-view">View Bill</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div class="empty-state-icon">📭</div>
                                <p>No sales records found</p>
                                <a href="{{ route('admin.pos.index') }}" class="btn-primary" style="margin-top: 1rem; text-decoration: none;">Make First Sale</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div style="padding: 1rem; display: flex; justify-content: center;">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
@endsection
