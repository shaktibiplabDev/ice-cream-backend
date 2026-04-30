@extends('layouts.admin')

@section('title', 'Bank Settings')

@section('content')
    <div class="page-header">
        <h1>
            <small>Configure Bank Details</small>
            Bank Settings
        </h1>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="settings-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to" value="admin.settings.bank">

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
        </div>

        <div style="position: fixed; bottom: 0; left: var(--sidebar-w); right: 0; background: var(--bg-card); border-top: 1px solid var(--border-subtle); padding: 1rem 1.5rem; display: flex; justify-content: flex-end; z-index: 100;">
            <button type="submit" class="btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">
                💾 Save Bank Settings
            </button>
        </div>
    </form>

    <style>
        .settings-form { margin-bottom: 100px; }
        .settings-single { max-width: 800px; margin: 0 auto; }
    </style>
@endsection
