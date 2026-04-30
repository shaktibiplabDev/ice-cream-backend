@extends('layouts.admin')

@section('title', 'Tax & GST Settings')

@section('content')
    <div class="page-header">
        <h1>
            <small>Configure Tax Rates</small>
            Tax & GST Settings
        </h1>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="settings-form">
        @csrf
        @method('PUT')

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
                        
                        <div class="form-group">
                            <label>Total GST %</label>
                            <input type="number" name="gst_percentage" value="{{ old('gst_percentage', $settings->gst_percentage) }}" class="form-control" step="0.01" min="0" max="100">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
                            <div class="form-group">
                                <label>CGST %</label>
                                <input type="number" name="cgst_percentage" value="{{ old('cgst_percentage', $settings->cgst_percentage) }}" class="form-control" step="0.01" min="0" max="100">
                            </div>
                            <div class="form-group">
                                <label>SGST %</label>
                                <input type="number" name="sgst_percentage" value="{{ old('sgst_percentage', $settings->sgst_percentage) }}" class="form-control" step="0.01" min="0" max="100">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 0.75rem;">
                            <label>IGST %</label>
                            <input type="number" name="igst_percentage" value="{{ old('igst_percentage', $settings->igst_percentage) }}" class="form-control" step="0.01" min="0" max="100">
                            <small style="color: var(--text-muted);">Used for B2B interstate sales</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="position: fixed; bottom: 0; left: var(--sidebar-w); right: 0; background: var(--bg-card); border-top: 1px solid var(--border-subtle); padding: 1rem 1.5rem; display: flex; justify-content: flex-end; z-index: 100;">
            <button type="submit" class="btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">
                💾 Save Tax Settings
            </button>
        </div>
    </form>

    <style>
        .settings-form { margin-bottom: 100px; }
        .settings-single { max-width: 800px; margin: 0 auto; }
    </style>
@endsection
