@extends('layouts.admin')

@section('title', 'Company Settings')

@section('content')
    <div class="page-header">
        <h1>
            <small>Configure Business Information</small>
            Company Settings
        </h1>
    </div>

    <!-- Settings Tabs -->
    <div class="settings-tabs">
        <button type="button" class="tab-btn active" data-tab="company">🏢 Company</button>
        <button type="button" class="tab-btn" data-tab="tax">📋 Tax & GST</button>
        <button type="button" class="tab-btn" data-tab="invoice">🧾 Invoice</button>
        <button type="button" class="tab-btn" data-tab="bank">🏦 Bank</button>
        <button type="button" class="tab-btn" data-tab="email">📧 Email</button>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="settings-form">
        @csrf
        @method('PUT')

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Company Information Tab -->
            <div id="tab-company" class="tab-pane active">
                <div class="settings-single">
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

            <!-- Tax & GST Tab -->
            <div id="tab-tax" class="tab-pane">
                <div class="settings-single">
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

            <!-- Invoice Tab -->
            <div id="tab-invoice" class="tab-pane">
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

            <!-- Bank Tab -->
            <div id="tab-bank" class="tab-pane">
                <div class="settings-single">
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

            <!-- Email Tab -->
            <div id="tab-email" class="tab-pane">
                <div class="settings-single">
                    <div class="glass-card">
                        <div class="card-head">
                            <h2>📧 Email Configuration (Inbox)</h2>
                </div>
                <div class="form-body" style="padding: 1.25rem;">
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="email_fetching_enabled" value="1" {{ old('email_fetching_enabled', $settings->email_fetching_enabled) ? 'checked' : '' }} style="width: auto;">
                            Enable Email Fetching
                        </label>
                        <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">Automatically fetch emails from your email server every 5 minutes</small>
                    </div>

                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; margin-top: 1rem;">
                        <h4 style="margin-bottom: 1rem; font-size: 0.875rem; color: var(--text-muted);">IMAP Settings (for Receiving)</h4>

                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>IMAP Host</label>
                                <input type="text" name="imap_host" value="{{ old('imap_host', $settings->imap_host) }}" class="form-control" placeholder="imap.gmail.com">
                            </div>
                            <div class="form-group">
                                <label>IMAP Port</label>
                                <input type="number" name="imap_port" value="{{ old('imap_port', $settings->imap_port ?? 993) }}" class="form-control">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>IMAP Username</label>
                                <input type="text" name="imap_username" value="{{ old('imap_username', $settings->imap_username) }}" class="form-control" placeholder="your@email.com">
                            </div>
                            <div class="form-group">
                                <label>IMAP Password</label>
                                <input type="password" name="imap_password" value="{{ old('imap_password', $settings->imap_password) }}" class="form-control">
                                <small style="color: var(--text-muted);">For Gmail, use App Password</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Encryption</label>
                            <select name="imap_encryption" class="form-control">
                                <option value="ssl" {{ old('imap_encryption', $settings->imap_encryption) == 'ssl' ? 'selected' : '' }}>SSL (Recommended)</option>
                                <option value="tls" {{ old('imap_encryption', $settings->imap_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="none" {{ old('imap_encryption', $settings->imap_encryption) == 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Folder to Monitor</label>
                            <input type="text" name="imap_folder" value="{{ old('imap_folder', $settings->imap_folder ?? 'INBOX') }}" class="form-control">
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; margin-top: 1rem;">
                        <h4 style="margin-bottom: 1rem; font-size: 0.875rem; color: var(--text-muted);">SMTP Settings (for Sending)</h4>

                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>SMTP Host</label>
                                <input type="text" name="mail_host" value="{{ old('mail_host', $settings->mail_host) }}" class="form-control" placeholder="smtp.gmail.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Port</label>
                                <input type="number" name="mail_port" value="{{ old('mail_port', $settings->mail_port ?? 587) }}" class="form-control">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>SMTP Username</label>
                                <input type="text" name="mail_username" value="{{ old('mail_username', $settings->mail_username) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>SMTP Password</label>
                                <input type="password" name="mail_password" value="{{ old('mail_password', $settings->mail_password) }}" class="form-control">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>From Email</label>
                                <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings->mail_from_address) }}" class="form-control" placeholder="noreply@yourcompany.com">
                            </div>
                            <div class="form-group">
                                <label>From Name</label>
                                <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings->mail_from_name) }}" class="form-control" placeholder="Your Company Name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Encryption</label>
                            <select name="mail_encryption" class="form-control">
                                <option value="tls" {{ old('mail_encryption', $settings->mail_encryption) == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                <option value="ssl" {{ old('mail_encryption', $settings->mail_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="" {{ old('mail_encryption', $settings->mail_encryption) == '' ? 'selected' : '' }}>None</option>
                            </select>
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
            margin-bottom: 100px;
        }
        .settings-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            padding: 0 0.5rem;
        }
        .tab-btn {
            padding: 0.75rem 1.25rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .tab-btn:hover {
            background: rgba(255,255,255,0.1);
            color: var(--text-secondary);
        }
        .tab-btn.active {
            background: var(--accent-gradient);
            color: white;
            border-color: transparent;
        }
        .tab-content {
            position: relative;
        }
        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .settings-single {
            max-width: 800px;
            margin: 0 auto;
        }
        @media (max-width: 768px) {
            .settings-tabs {
                gap: 0.25rem;
            }
            .tab-btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }
        }
    </style>

    @push('scripts')
    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active from all tabs
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

                // Add active to clicked tab
                btn.classList.add('active');
                const tabId = 'tab-' + btn.dataset.tab;
                document.getElementById(tabId).classList.add('active');

                // Save to localStorage
                localStorage.setItem('settings_active_tab', btn.dataset.tab);
            });
        });

        // Restore active tab from localStorage
        const savedTab = localStorage.getItem('settings_active_tab');
        if (savedTab) {
            const btn = document.querySelector('.tab-btn[data-tab="' + savedTab + '"]');
            if (btn) btn.click();
        }
    </script>
    @endpush
@endsection
