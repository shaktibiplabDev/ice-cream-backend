@php
    $settings = \App\Models\CompanySetting::getSettings();
@endphp

@extends('layouts.admin')

@section('title', 'Invoice - ' . $sale->invoice_number)

@section('content')
    <div class="page-header no-print">
        <h1>
            <small>Sale Invoice</small>
            {{ $sale->invoice_number }}
        </h1>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="window.print()" class="btn-secondary" style="text-decoration: none;">🖨️ Print</button>
            <a href="{{ route('admin.pos.index') }}" class="btn-primary" style="text-decoration: none;">🛒 New Sale</a>
        </div>
    </div>

    <!-- Invoice Paper -->
    <div class="invoice-paper">
        <!-- Header with Logo -->
        <div class="invoice-header">
            <div class="company-info">
                @if($settings->logo_path)
                    <img src="{{ Storage::url($settings->logo_path) }}" alt="Logo" class="company-logo">
                @endif
                <div class="company-name">{{ $settings->company_name }}</div>
                @if($settings->company_legal_name)
                    <div class="company-legal">{{ $settings->company_legal_name }}</div>
                @endif
                <div class="company-address">
                    {{ $settings->address }}<br>
                    @if($settings->city){{ $settings->city }}, @endif
                    @if($settings->state){{ $settings->state }} @endif
                    @if($settings->postal_code){{ $settings->postal_code }}@endif
                </div>
                @if($settings->phone)
                    <div class="company-contact">Phone: {{ $settings->phone }}</div>
                @endif
                @if($settings->email)
                    <div class="company-contact">Email: {{ $settings->email }}</div>
                @endif
                @if($settings->gst_number)
                    <div class="company-gst"><strong>GSTIN: {{ $settings->gst_number }}</strong></div>
                @endif
            </div>
            <div class="invoice-meta">
                <div class="invoice-title">TAX INVOICE</div>
                <table class="meta-table">
                    <tr>
                        <td>Invoice #</td>
                        <td>: {{ $sale->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>: {{ $sale->sale_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Time</td>
                        <td>: {{ $sale->sale_date->format('h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Billing Parties -->
        <div class="billing-parties">
            <div class="party-box">
                <div class="party-label">Bill To:</div>
                <div class="party-name">{{ $sale->distributor->name }}</div>
                @if($sale->distributor->gst_number)
                    <div class="party-gst">GSTIN: {{ $sale->distributor->gst_number }}</div>
                @endif
                <div class="party-details">
                    @if($sale->distributor->contact_person)
                        <div>Contact: {{ $sale->distributor->contact_person }}</div>
                    @endif
                    @if($sale->distributor->address)
                        <div>{{ $sale->distributor->address }}</div>
                    @endif
                    @if($sale->distributor->phone)
                        <div>Phone: {{ $sale->distributor->phone }}</div>
                    @endif
                </div>
            </div>
            <div class="party-box">
                <div class="party-label">Ship From:</div>
                <div class="party-name">{{ $sale->warehouse->name }}</div>
                <div class="party-details">
                    <div>Code: {{ $sale->warehouse->code }}</div>
                    <div>{{ $sale->warehouse->address }}</div>
                    <div>{{ $sale->warehouse->city }}</div>
                    @if($sale->warehouse->phone)
                        <div>Phone: {{ $sale->warehouse->phone }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Description</th>
                    <th style="width: 10%;">Unit</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 15%;">Rate</th>
                    <th style="width: 10%;">Disc %</th>
                    <th style="width: 15%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $item->product->name }}</strong></td>
                        <td>{{ $item->product->unit }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $settings->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->discount_percent > 0 ? $item->discount_percent . '%' : '-' }}</td>
                        <td class="text-right">{{ $settings->currency_symbol }}{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-left">
                @if($settings->bank_name)
                    <div class="bank-details">
                        <div class="section-title">Bank Details:</div>
                        <div><strong>{{ $settings->bank_name }}</strong></div>
                        @if($settings->bank_account_number)
                            <div>A/C: {{ $settings->bank_account_number }}</div>
                        @endif
                        @if($settings->bank_ifsc_code)
                            <div>IFSC: {{ $settings->bank_ifsc_code }}</div>
                        @endif
                    </div>
                @endif
                <div class="terms">
                    <div class="section-title">Terms:</div>
                    <div>{{ $settings->invoice_terms ?? 'Due on Receipt' }}</div>
                </div>
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">{{ $settings->currency_symbol }}{{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    @if($sale->discount_amount > 0)
                        <tr>
                            <td>Discount:</td>
                            <td class="text-right">-{{ $settings->currency_symbol }}{{ number_format($sale->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if($sale->igst_amount > 0)
                        <tr>
                            <td>IGST ({{ $settings->igst_percentage }}%):</td>
                            <td class="text-right">{{ $settings->currency_symbol }}{{ number_format($sale->igst_amount, 2) }}</td>
                        </tr>
                    @elseif($sale->cgst_amount > 0 || $sale->sgst_amount > 0)
                        <tr>
                            <td>CGST ({{ $settings->cgst_percentage }}%):</td>
                            <td class="text-right">{{ $settings->currency_symbol }}{{ number_format($sale->cgst_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>SGST ({{ $settings->sgst_percentage }}%):</td>
                            <td class="text-right">{{ $settings->currency_symbol }}{{ number_format($sale->sgst_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td><strong>Total Amount:</strong></td>
                        <td class="text-right"><strong>{{ $settings->currency_symbol }}{{ number_format($sale->total_amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Amount in Words:</strong> {{ number_to_words($sale->total_amount) }} Only
        </div>

        <!-- Payment Status -->
        <div class="payment-status">
            <span class="status-label">Payment Status:</span>
            <span class="status-value {{ $sale->payment_status }}">{{ strtoupper($sale->payment_status) }}</span>
            <span class="payment-method">via {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="footer-left">
                @if($settings->terms_and_conditions)
                    <div class="terms-text">
                        <strong>Terms & Conditions:</strong><br>
                        {{ $settings->terms_and_conditions }}
                    </div>
                @endif
                @if($sale->notes)
                    <div class="notes">
                        <strong>Notes:</strong> {{ $sale->notes }}
                    </div>
                @endif
            </div>
            <div class="footer-right">
                <div class="signature-area">
                    <div class="signature-line"></div>
                    <div class="signature-text">Authorized Signatory</div>
                </div>
            </div>
        </div>

        <div class="footer-message">
            {{ $settings->invoice_footer_text ?? 'Thank you for your business!' }}<br>
            <small>Processed by {{ $sale->creator->name }} | {{ $settings->company_name }}</small>
        </div>
    </div>

    <style>
        /* Invoice Paper - Professional Design */
        .invoice-paper {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            color: #1a1a1a;
            padding: 40px;
            border: 1px solid #e0e0e0;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .company-info {
            flex: 1;
        }

        .company-logo {
            max-height: 60px;
            max-width: 200px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .company-legal {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }

        .company-address {
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .company-contact {
            font-size: 12px;
            color: #555;
        }

        .company-gst {
            font-size: 12px;
            color: #2c3e50;
            margin-top: 6px;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-title {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            letter-spacing: 2px;
        }

        .meta-table {
            font-size: 12px;
        }

        .meta-table td {
            padding: 2px 8px;
            color: #333;
        }

        .meta-table td:first-child {
            text-align: right;
            font-weight: 600;
            color: #555;
        }

        /* Billing Parties */
        .billing-parties {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .party-box {
            flex: 1;
            border: 1px solid #e0e0e0;
            padding: 15px;
            background: #f9f9f9;
        }

        .party-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .party-name {
            font-size: 15px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .party-gst {
            font-size: 11px;
            color: #555;
            margin-bottom: 6px;
        }

        .party-details {
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .items-table thead tr {
            background: #2c3e50;
            color: white;
        }

        .items-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table th:last-child,
        .items-table th:nth-last-child(2),
        .items-table th:nth-last-child(3) {
            text-align: right;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .items-table td {
            padding: 10px 8px;
            color: #1a1a1a;
        }

        .text-right {
            text-align: right !important;
        }

        /* Totals Section */
        .totals-section {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-bottom: 20px;
            border-top: 2px solid #2c3e50;
            padding-top: 20px;
        }

        .totals-left {
            flex: 1;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #2c3e50;
            margin-bottom: 6px;
            letter-spacing: 1px;
        }

        .bank-details {
            font-size: 12px;
            color: #333;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .terms {
            font-size: 12px;
            color: #333;
        }

        .totals-right {
            width: 280px;
        }

        .totals-table {
            width: 100%;
            font-size: 12px;
        }

        .totals-table td {
            padding: 4px 0;
            color: #333;
        }

        .totals-table tr.total-row td {
            padding: 10px 0;
            border-top: 2px solid #2c3e50;
            font-size: 15px;
            color: #2c3e50;
        }

        /* Amount in Words */
        .amount-words {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            margin-bottom: 15px;
            font-size: 12px;
            color: #333;
        }

        /* Payment Status */
        .payment-status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .status-label {
            font-weight: 600;
            color: #555;
        }

        .status-value {
            padding: 4px 12px;
            border-radius: 3px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
        }

        .status-value.paid {
            background: #d4edda;
            color: #155724;
        }

        .status-value.pending {
            background: #fff3cd;
            color: #856404;
        }

        .payment-method {
            margin-left: auto;
            color: #666;
        }

        /* Footer */
        .invoice-footer {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-bottom: 20px;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
        }

        .footer-left {
            flex: 1;
        }

        .terms-text {
            font-size: 10px;
            color: #555;
            line-height: 1.4;
            margin-bottom: 10px;
        }

        .notes {
            font-size: 11px;
            color: #333;
            font-style: italic;
        }

        .footer-right {
            width: 200px;
        }

        .signature-area {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            margin-bottom: 8px;
        }

        .signature-text {
            font-size: 11px;
            color: #555;
        }

        .footer-message {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #555;
        }

        /* Print Styles */
        @media print {
            body {
                background: white !important;
            }

            .no-print, .sidebar, .topbar, .mobile-nav {
                display: none !important;
            }

            .main {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .content {
                padding: 0 !important;
            }

            .invoice-paper {
                border: none;
                max-width: 100%;
                padding: 20px;
                box-shadow: none;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .invoice-paper {
                padding: 20px;
            }

            .invoice-header {
                flex-direction: column;
                gap: 20px;
            }

            .invoice-meta {
                text-align: left;
            }

            .billing-parties {
                flex-direction: column;
            }

            .items-table {
                font-size: 11px;
            }

            .items-table th,
            .items-table td {
                padding: 6px 4px;
            }

            .totals-section {
                flex-direction: column;
            }

            .totals-right {
                width: 100%;
            }

            .invoice-footer {
                flex-direction: column;
            }
        }
    </style>
@endsection
