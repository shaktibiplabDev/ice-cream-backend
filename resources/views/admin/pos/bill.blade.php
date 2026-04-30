@extends('layouts.admin')

@section('title', 'Invoice - ' . $sale->invoice_number)

@section('content')
    <div class="page-header">
        <h1>
            <small>Sale Invoice</small>
            {{ $sale->invoice_number }}
        </h1>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="window.print()" class="btn-secondary" style="text-decoration: none;">🖨️ Print</button>
            <a href="{{ route('admin.pos.index') }}" class="btn-primary" style="text-decoration: none;">🛒 New Sale</a>
        </div>
    </div>

    <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
        <!-- Invoice Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 1.5rem; border-bottom: 2px solid var(--border-subtle);">
            <div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary-light);">✨ Celesty</div>
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">Ice Cream Distribution</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1.25rem; font-weight: 600;">INVOICE</div>
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">{{ $sale->invoice_number }}</div>
                <div style="font-size: 0.8125rem; color: var(--text-muted);">{{ $sale->sale_date->format('F d, Y h:i A') }}</div>
            </div>
        </div>

        <!-- Billing Details -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 1.5rem; border-bottom: 1px solid var(--border-subtle);">
            <div>
                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.5rem;">Bill To</div>
                <div style="font-weight: 600; font-size: 1rem;">{{ $sale->distributor->name }}</div>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">{{ $sale->distributor->contact_person }}</div>
                @if($sale->distributor->address)
                    <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.5rem;">{{ $sale->distributor->address }}</div>
                @endif
                @if($sale->distributor->phone)
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">📞 {{ $sale->distributor->phone }}</div>
                @endif
                @if($sale->distributor->email)
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">✉️ {{ $sale->distributor->email }}</div>
                @endif
            </div>
            <div>
                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.5rem;">Fulfilled By</div>
                <div style="font-weight: 600; font-size: 1rem;">{{ $sale->warehouse->name }}</div>
                <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">{{ $sale->warehouse->code }}</div>
                <div style="font-size: 0.8125rem; color: var(--text-muted);">{{ $sale->warehouse->address }}, {{ $sale->warehouse->city }}</div>
                @if($sale->warehouse->phone)
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">📞 {{ $sale->warehouse->phone }}</div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div style="padding: 1.5rem;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.05);">
                        <th style="padding: 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Item</th>
                        <th style="padding: 0.75rem; text-align: center; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Qty</th>
                        <th style="padding: 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Unit Price</th>
                        <th style="padding: 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                        <tr style="border-bottom: 1px solid var(--border-subtle);">
                            <td style="padding: 0.75rem;">
                                <div style="font-weight: 500;">{{ $item->product->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $item->product->unit }}</div>
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">{{ $item->quantity }}</td>
                            <td style="padding: 0.75rem; text-align: right;">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td style="padding: 0.75rem; text-align: right; font-weight: 500;">₹{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div style="padding: 1.5rem; background: rgba(0,0,0,0.2); border-top: 1px solid var(--border-subtle);">
            <div style="max-width: 300px; margin-left: auto;">
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem;">
                    <span>Subtotal:</span>
                    <span>₹{{ number_format($sale->subtotal, 2) }}</span>
                </div>
                @if($sale->discount_amount > 0)
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem;">
                        <span>Discount:</span>
                        <span style="color: #f87171;">-₹{{ number_format($sale->discount_amount, 2) }}</span>
                    </div>
                @endif
                @if($sale->tax_amount > 0)
                    <div style="font-size: 0.6875rem; color: var(--text-muted); margin-bottom: 0.25rem; text-transform: uppercase; margin-top: 0.5rem;">
                        GST Breakdown
                    </div>
                    @if($sale->igst_amount > 0)
                        <div style="display: flex; justify-content: space-between; padding: 0.25rem 0; font-size: 0.8125rem;">
                            <span>IGST:</span>
                            <span>₹{{ number_format($sale->igst_amount, 2) }}</span>
                        </div>
                    @else
                        @if($sale->cgst_amount > 0)
                            <div style="display: flex; justify-content: space-between; padding: 0.25rem 0; font-size: 0.8125rem;">
                                <span>CGST:</span>
                                <span>₹{{ number_format($sale->cgst_amount, 2) }}</span>
                            </div>
                        @endif
                        @if($sale->sgst_amount > 0)
                            <div style="display: flex; justify-content: space-between; padding: 0.25rem 0; font-size: 0.8125rem;">
                                <span>SGST:</span>
                                <span>₹{{ number_format($sale->sgst_amount, 2) }}</span>
                            </div>
                        @endif
                    @endif
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; border-top: 1px dashed var(--border-subtle);">
                        <span>Total Tax:</span>
                        <span>₹{{ number_format($sale->tax_amount, 2) }}</span>
                    </div>
                @endif
                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; margin-top: 0.5rem; border-top: 2px solid var(--border-subtle); font-size: 1.25rem; font-weight: 600;">
                    <span>Total:</span>
                    <span style="color: #34d399;">₹{{ number_format($sale->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-top: 1px solid var(--border-subtle); font-size: 0.8125rem;">
            <div>
                <span style="color: var(--text-muted);">Payment Method:</span>
                <span style="font-weight: 500; margin-left: 0.5rem;">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
                <span class="status-badge {{ $sale->payment_status === 'paid' ? 'status-active' : ($sale->payment_status === 'pending' ? 'status-inactive' : 'status-in-progress') }}" style="margin-left: 1rem;">
                    {{ ucfirst($sale->payment_status) }}
                </span>
            </div>
            <div style="color: var(--text-muted);">
                Processed by: {{ $sale->creator->name }}
            </div>
        </div>

        <!-- Notes -->
        @if($sale->notes)
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-subtle);">
                <div style="font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.5rem;">Notes</div>
                <div style="font-size: 0.8125rem; color: var(--text-secondary);">{{ $sale->notes }}</div>
            </div>
        @endif

        <!-- Footer -->
        <div style="text-align: center; padding: 1.5rem; border-top: 2px solid var(--border-subtle);">
            <div style="font-size: 0.75rem; color: var(--text-muted);">Thank you for your business!</div>
            <div style="font-size: 0.6875rem; color: var(--text-muted); margin-top: 0.25rem;">For any queries, please contact {{ $sale->warehouse->phone ?? 'our support team' }}</div>
        </div>
    </div>

    <style>
        @media print {
            .sidebar, .topbar, .page-header button, .page-header a {
                display: none !important;
            }
            .main {
                margin-left: 0 !important;
            }
            .glass-card {
                border: 1px solid #ddd;
                box-shadow: none;
            }
        }
    </style>
@endsection
