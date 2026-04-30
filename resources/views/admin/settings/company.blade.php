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
        <input type="hidden" name="redirect_to" value="admin.settings.company">

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
        </div>

        <div style="position: fixed; bottom: 0; left: var(--sidebar-w); right: 0; background: var(--bg-card); border-top: 1px solid var(--border-subtle); padding: 1rem 1.5rem; display: flex; justify-content: flex-end; z-index: 100;">
            <button type="submit" class="btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">
                💾 Save Company Settings
            </button>
        </div>
    </form>

    <style>
        .settings-form { margin-bottom: 100px; }
        .settings-single { max-width: 800px; margin: 0 auto; }
    </style>
@endsection
