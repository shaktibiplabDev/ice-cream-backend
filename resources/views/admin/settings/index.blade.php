@extends('layouts.admin')

@section('title', 'Company Settings')

@section('content')
    <div class="page-header">
        <h1>
            <small>Configure Business Information</small>
            Company Settings
        </h1>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="settings-form">
        @csrf
        @method('PUT')

        <div class="settings-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
            
            <!-- Company Information -->
            <div class="glass-card">
                <div class="card-head">
                    <h2>🏢 Company Information</h2>
                </div>
                <div class="form-body" style="padding: 1.25rem;">
                    <div class="form-group">
                        <label>Company Name *</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $settings->company_name) }}" class="form-control @error('company_name') is-invalid @enderror" required>
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>Legal/Registered Name</label>
                        <input type="text" name="company_legal_name" value="{{ old('company_legal_name', $settings->company_legal_name) }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Address *</label>
                        <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $settings->address) }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" value="{{ old('city', $settings->city) }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" value="{{ old('state', $settings->state) }}" class="form-control">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $settings->postal_code) }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="{{ old('country', $settings->country) }}" class="form-control">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" value="{{ old('email', $settings->email) }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Website</label>
                        <input type="url" name="website" value="{{ old('website', $settings->website) }}" class="form-control" placeholder="https://">
                    </div>

                    <div class="form-group">
                        <label>Company Logo</label>
                        @if($settings->logo_path)
                            <div style="margin-bottom: 0.5rem;">
                                <img src="{{ Storage::url($settings->logo_path) }}" alt="Logo" style="max-height: 60px;">
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <small style="color: var(--text-muted);">Max 2MB. Recommended: 200x60px</small>
                    </div>
                </div>
            </div>

            <!-- GST & Tax Settings -->
            <div class="glass-card">
                <div class="card-head">
                    <h2>📋 GST & Tax Settings</h2>
                </div>
                <div class="form-body" style="padding: 1.25rem;">
                    <div class="form-group">
                        <label>GST Number</label>
                        <input type="text" name="gst_number" value="{{ old('gst_number', $settings->gst_number) }}" class="form-control" placeholder="22AAAAA0000A1Z5">
                    </div>

                    <div class="form-group">
                        <label>PAN Number</label>
                        <input type="text" name="pan_number" value="{{ old('pan_number', $settings->pan_number) }}" class="form-control" placeholder="AAAAA0000A">
                    </div>

                    <div class="form-group">
                        <label>FSSAI License Number</label>
                        <input type="text" name="fssai_number" value="{{ old('fssai_number', $settings->fssai_number) }}" class="form-control">
                        <small style="color: var(--text-muted);">Required for food businesses in India</small>
                    </div>

                    <div class="form-group">
                        <label>GST Type *</label>
                        <select name="gst_type" class="form-control" required>
                            <option value="b2c" {{ old('gst_type', $settings->gst_type) == 'b2c' ? 'selected' : '' }}>B2C (CGST + SGST for local sales)</option>
                            <option value="b2b" {{ old('gst_type', $settings->gst_type) == 'b2b' ? 'selected' : '' }}>B2B (IGST for interstate/inter-business)</option>
                            <option value="none" {{ old('gst_type', $settings->gst_type) == 'none' ? 'selected' : '' }}>No GST / GST Exempt</option>
                        </select>
                    </div>

                    <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: var(--radius-md); margin: 1rem 0;">
                        <div style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-secondary);">GST Rate Configuration</div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group" style="margin-bottom: 0.5rem;">
                                <label>Total GST %</label>
                                <input type="number" name="gst_percentage" value="{{ old('gst_percentage', $settings->gst_percentage) }}" class="form-control" step="0.01" min="0" max="100">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>CGST %</label>
                                <input type="number" name="cgst_percentage" value="{{ old('cgst_percentage', $settings->cgst_percentage) }}" class="form-control" step="0.01" min="0" max="100">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>SGST %</label>
                                <input type="number" name="sgst_percentage" value="{{ old('sgst_percentage', $settings->sgst_percentage) }}" class="form-control" step="0.01" min="0" max="100">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 0.75rem; margin-bottom: 0;">
                            <label>IGST %</label>
                            <input type="number" name="igst_percentage" value="{{ old('igst_percentage', $settings->igst_percentage) }}" class="form-control" step="0.01" min="0" max="100">
                            <small style="color: var(--text-muted);">Used for B2B interstate sales</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Settings -->
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

            <!-- Bank Details -->
            <div class="glass-card">
                <div class="card-head">
                    <h2>🏦 Bank Details</h2>
                </div>
                <div class="form-body" style="padding: 1.25rem;">
                    <div class="form-group">
                        <label>Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $settings->bank_name) }}" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $settings->bank_account_number) }}" class="form-control">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>IFSC Code</label>
                            <input type="text" name="bank_ifsc_code" value="{{ old('bank_ifsc_code', $settings->bank_ifsc_code) }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Branch</label>
                            <input type="text" name="bank_branch" value="{{ old('bank_branch', $settings->bank_branch) }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="position: fixed; bottom: 0; left: var(--sidebar-w); right: 0; background: var(--bg-card); border-top: 1px solid var(--border-subtle); padding: 1rem 1.5rem; display: flex; justify-content: flex-end; z-index: 100;">
            <button type="submit" class="btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">
                💾 Save All Settings
            </button>
        </div>
    </form>

    <style>
        .settings-form {
            margin-bottom: 80px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-secondary);
        }
        .form-group small {
            display: block;
            margin-top: 0.25rem;
        }
    </style>
@endsection
