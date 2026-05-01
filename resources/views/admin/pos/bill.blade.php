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
                    <th style="width: 4%;">#</th>
                    <th style="width: 28%;">Description</th>
                    <th style="width: 8%;">Unit</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 12%;">MRP</th>
                    <th style="width: 10%;">Disc%</th>
                    <th style="width: 12%;">Disc Amt</th>
                    <th style="width: 14%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalMrp = 0;
                    $totalItemDiscount = 0;
                @endphp
                @foreach($sale->items as $index => $item)
                    @php
                        $itemMrp = $item->product->mrp_price * $item->quantity;
                        $itemDiscount = $item->discount_amount;
                        $totalMrp += $itemMrp;
                        $totalItemDiscount += $itemDiscount;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $item->product->name }}</strong></td>
                        <td>{{ $item->product->unit }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $settings->currency_symbol }}{{ number_format($itemMrp, 2) }}</td>
                        <td>{{ $item->discount_percent > 0 ? $item->discount_percent . '%' : '-' }}</td>
                        <td>{{ $itemDiscount > 0 ? $settings->currency_symbol . number_format($itemDiscount, 2) : '-' }}</td>
                        <td class="text-right"><strong>{{ $settings->currency_symbol }}{{ number_format($item->total_price, 2) }}</strong></td>
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
                @php
                    $distDiscount = $totalMrp - $sale->subtotal;
                    $totalSavings = $totalItemDiscount + $distDiscount;
                @endphp
                @if($totalSavings > 0)
                    <div class="savings-box" style="margin-top: 8px; padding: 6px 8px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 3px;">
                        <div style="font-size: 10px; color: #155724; font-weight: 600;">
                            💰 You Save: {{ $settings->currency_symbol }}{{ number_format($totalSavings, 2) }}
                        </div>
                        <div style="font-size: 9px; color: #155724;">
                            MRP: {{ $settings->currency_symbol }}{{ number_format($totalMrp, 2) }} 
                            @if($totalItemDiscount > 0)- Item Disc: {{ $settings->currency_symbol }}{{ number_format($totalItemDiscount, 2) }} @endif
                            @if($distDiscount > 0)- Dist. Disc: {{ $settings->currency_symbol }}{{ number_format($distDiscount, 2) }} @endif
                        </div>
                    </div>
                @endif
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td style="text-decoration: line-through; color: #666;">MRP Total:</td>
                        <td class="text-right" style="text-decoration: line-through; color: #666;">{{ $settings->currency_symbol }}{{ number_format($totalMrp, 2) }}</td>
                    </tr>
                    @if($totalItemDiscount > 0)
                        <tr>
                            <td style="color: #e74c3c;">Item Discount:</td>
                            <td class="text-right" style="color: #e74c3c;">-{{ $settings->currency_symbol }}{{ number_format($totalItemDiscount, 2) }}</td>
                        </tr>
                    @endif
                    @if($distDiscount > 0)
                        <tr>
                            <td style="color: #27ae60;">Dist. Discount:</td>
                            <td class="text-right" style="color: #27ae60;">-{{ $settings->currency_symbol }}{{ number_format($distDiscount, 2) }}</td>
                        </tr>
                    @endif
                    <tr style="border-top: 1px dashed #ccc;">
                        <td><strong>Subtotal:</strong></td>
                        <td class="text-right"><strong>{{ $settings->currency_symbol }}{{ number_format($sale->subtotal, 2) }}</strong></td>
                    </tr>
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
            <strong>Amount in Words:</strong>
            {{ number_to_words($sale->total_amount) }} Only
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
        /* Invoice Paper - A4 Optimized for up to 10 products */
        .invoice-paper {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            color: #1a1a1a;
            padding: 15px 20px;
            border: 1px solid #e0e0e0;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            min-height: 297mm;
            box-sizing: border-box;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 12px;
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
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .company-legal {
            font-size: 11px;
            color: #555;
            margin-bottom: 4px;
        }

        .company-address {
            font-size: 10px;
            color: #333;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .company-contact {
            font-size: 10px;
            color: #555;
        }

        .company-gst {
            font-size: 10px;
            color: #2c3e50;
            margin-top: 3px;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-title {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .meta-table {
            font-size: 10px;
        }

        .meta-table td {
            padding: 1px 6px;
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
            gap: 10px;
            margin-bottom: 12px;
        }

        .party-box {
            flex: 1;
            border: 1px solid #e0e0e0;
            padding: 8px 10px;
            background: #f9f9f9;
        }

        .party-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .party-name {
            font-size: 12px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .party-gst {
            font-size: 9px;
            color: #555;
            margin-bottom: 3px;
        }

        .party-details {
            font-size: 10px;
            color: #333;
            line-height: 1.3;
        }

        /* Items Table - Compact for 10 items on A4 */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }

        .items-table thead tr {
            background: #2c3e50;
            color: white;
        }

        .items-table th {
            padding: 5px 4px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .items-table th:last-child,
        .items-table th:nth-last-child(2),
        .items-table th:nth-last-child(3) {
            text-align: right;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .items-table tbody tr:last-child {
            border-bottom: 2px solid #2c3e50;
        }

        .items-table td {
            padding: 4px;
            color: #1a1a1a;
            vertical-align: top;
        }

        .text-right {
            text-align: right !important;
        }

        /* Totals Section - Compact */
        .totals-section {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 10px;
            border-top: 2px solid #2c3e50;
            padding-top: 8px;
        }

        .totals-left {
            flex: 1;
        }

        .section-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #2c3e50;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }

        .bank-details {
            font-size: 9px;
            color: #333;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .terms {
            font-size: 9px;
            color: #333;
        }

        .totals-right {
            width: 220px;
        }

        .totals-table {
            width: 100%;
            font-size: 10px;
        }

        .totals-table td {
            padding: 2px 0;
            color: #333;
        }

        .totals-table tr.total-row td {
            padding: 5px 0;
            border-top: 2px solid #2c3e50;
            font-size: 12px;
            color: #2c3e50;
        }

        /* Amount in Words - Compact */
        .amount-words {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            padding: 6px 10px;
            margin-bottom: 8px;
            font-size: 10px;
            color: #333;
        }

        /* Payment Status - Compact */
        .payment-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            margin-bottom: 10px;
            font-size: 10px;
        }

        .status-label {
            font-weight: 600;
            color: #555;
        }

        .status-value {
            padding: 2px 8px;
            border-radius: 2px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
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

        /* Footer - Compact */
        .invoice-footer {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 10px;
            border-top: 1px solid #e0e0e0;
            padding-top: 8px;
        }

        .footer-left {
            flex: 1;
        }

        .terms-text {
            font-size: 8px;
            color: #555;
            line-height: 1.3;
            margin-bottom: 5px;
        }

        .notes {
            font-size: 9px;
            color: #333;
            font-style: italic;
        }

        .footer-right {
            width: 150px;
        }

        .signature-area {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 25px;
            margin-bottom: 4px;
        }

        .signature-text {
            font-size: 9px;
            color: #555;
        }

        .footer-message {
            text-align: center;
            padding-top: 8px;
            border-top: 1px solid #e0e0e0;
            font-size: 9px;
            color: #555;
        }

        /* Print Styles - Optimized for A4 */
        @media print {
            @page {
                size: A4;
                margin: 5mm;
            }
            
            body {
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
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
                padding: 8mm;
                box-shadow: none;
                font-size: 9px;
                min-height: auto;
            }
            
            .invoice-header {
                padding-bottom: 5px;
                margin-bottom: 8px;
            }
            
            .billing-parties {
                margin-bottom: 8px;
            }
            
            .party-box {
                padding: 5px 8px;
            }
            
            .items-table {
                margin-bottom: 6px;
            }
            
            .items-table th,
            .items-table td {
                padding: 3px 4px !important;
                font-size: 8px !important;
            }
            
            .totals-section {
                margin-bottom: 6px;
                padding-top: 5px;
            }
            
            .amount-words {
                padding: 4px 8px;
                margin-bottom: 5px;
                font-size: 9px;
            }
            
            .payment-status {
                margin-bottom: 5px;
                padding: 4px 8px;
            }
            
            .invoice-footer {
                margin-bottom: 5px;
                padding-top: 5px;
            }
            
            .signature-line {
                margin-top: 15px;
            }
            
            .footer-message {
                padding-top: 5px;
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
