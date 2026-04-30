@extends('layouts.admin')

@section('title', 'Invoice Settings')

@section('content')
    <div class="page-header">
        <h1>
            <small>Configure Invoice Options</small>
            Invoice Settings
        </h1>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="settings-form">
        @csrf
        @method('PUT')

        <div class="settings-single">
            <div class="glass-card">
                <div class="card-head">
                    <h2>🧾 Invoice Settings</h2>
                </div>
                <div class="form-body" style="padding: 1.25rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Invoice Prefix *</label>
                            <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $settings->invoice_prefix) }}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Payment Terms</label>
                            <input type="text" name="invoice_terms" value="{{ old('invoice_terms', $settings->invoice_terms) }}" class="form-control" placeholder="NET30, Due on Receipt, etc.">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Currency *</label>
                        <input type="text" name="currency" value="{{ old('currency', $settings->currency) }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Currency Symbol *</label>
                        <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings->currency_symbol) }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Invoice Footer Text</label>
                        <textarea name="invoice_footer_text" rows="2" class="form-control" placeholder="Thank you for your business!">{{ old('invoice_footer_text', $settings->invoice_footer_text) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Terms & Conditions</label>
                        <textarea name="terms_and_conditions" rows="4" class="form-control" placeholder="Payment terms, return policy, etc.">{{ old('terms_and_conditions', $settings->terms_and_conditions) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div style="position: fixed; bottom: 0; left: var(--sidebar-w); right: 0; background: var(--bg-card); border-top: 1px solid var(--border-subtle); padding: 1rem 1.5rem; display: flex; justify-content: flex-end; z-index: 100;">
            <button type="submit" class="btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">
                💾 Save Invoice Settings
            </button>
        </div>
    </form>

    <style>
        .settings-form { margin-bottom: 100px; }
        .settings-single { max-width: 800px; margin: 0 auto; }
    </style>
@endsection
