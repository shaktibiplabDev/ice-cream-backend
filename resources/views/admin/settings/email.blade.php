@php
    use App\Services\EnvService;
    $envService = new EnvService();
    $envSettings = $envService->getEmailSettings();
@endphp
@extends('layouts.admin')

@section('title', 'Email Settings')

@section('content')
    <div class="page-header">
        <h1>
            <small>Configure Email (syncs with .env)</small>
            Email Settings
        </h1>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="settings-form">
        @csrf
        @method('PUT')

        <div class="settings-single">
            <div class="glass-card">
                <div class="card-head">
                    <h2>📧 Email Configuration (Inbox)</h2>
                </div>
                <div class="form-body" style="padding: 1.25rem;">
                    <div style="background: rgba(79, 70, 229, 0.1); border: 1px solid rgba(79, 70, 229, 0.3); border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1rem;">
                        <small style="color: var(--text-secondary);">📋 Settings shown below are from your <code>.env</code> file. Changes will update both database and .env.</small>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="email_fetching_enabled" value="1" {{ old('email_fetching_enabled', $settings->email_fetching_enabled) ? 'checked' : '' }} style="width: auto;">
                            Enable Email Fetching
                        </label>
                        <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">Automatically fetch emails every 1 minute</small>
                    </div>

                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; margin-top: 1rem;">
                        <h4 style="margin-bottom: 1rem; font-size: 0.875rem; color: var(--text-muted);">IMAP Settings (for Receiving)</h4>

                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>IMAP Host <small style="color: var(--text-muted);">(env: IMAP_HOST)</small></label>
                                <input type="text" name="imap_host" value="{{ old('imap_host', $settings->imap_host ?: $envSettings['imap_host']) }}" class="form-control" placeholder="imap.gmail.com">
                            </div>
                            <div class="form-group">
                                <label>IMAP Port <small style="color: var(--text-muted);">(env: IMAP_PORT)</small></label>
                                <input type="number" name="imap_port" value="{{ old('imap_port', $settings->imap_port ?: $envSettings['imap_port']) }}" class="form-control">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>IMAP Username <small style="color: var(--text-muted);">(env: IMAP_USERNAME)</small></label>
                                <input type="text" name="imap_username" value="{{ old('imap_username', $settings->imap_username ?: $envSettings['imap_username']) }}" class="form-control" placeholder="your@email.com">
                            </div>
                            <div class="form-group">
                                <label>IMAP Password <small style="color: var(--text-muted);">(env: IMAP_PASSWORD)</small></label>
                                <input type="password" name="imap_password" value="{{ old('imap_password', $settings->imap_password ?: $envSettings['imap_password']) }}" class="form-control">
                                <small style="color: var(--text-muted);">For Gmail, use App Password</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Encryption <small style="color: var(--text-muted);">(env: IMAP_ENCRYPTION)</small></label>
                            <select name="imap_encryption" class="form-control">
                                <option value="ssl" {{ old('imap_encryption', $settings->imap_encryption ?: $envSettings['imap_encryption']) == 'ssl' ? 'selected' : '' }}>SSL (Recommended)</option>
                                <option value="tls" {{ old('imap_encryption', $settings->imap_encryption ?: $envSettings['imap_encryption']) == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="none" {{ old('imap_encryption', $settings->imap_encryption ?: $envSettings['imap_encryption']) == 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Folder to Monitor <small style="color: var(--text-muted);">(env: IMAP_FOLDER)</small></label>
                            <input type="text" name="imap_folder" value="{{ old('imap_folder', $settings->imap_folder ?: $envSettings['imap_folder']) }}" class="form-control">
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; margin-top: 1rem;">
                        <h4 style="margin-bottom: 1rem; font-size: 0.875rem; color: var(--text-muted);">SMTP Settings (for Sending)</h4>

                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>SMTP Host <small style="color: var(--text-muted);">(env: MAIL_HOST)</small></label>
                                <input type="text" name="mail_host" value="{{ old('mail_host', $settings->mail_host ?: $envSettings['mail_host']) }}" class="form-control" placeholder="smtp.gmail.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP Port <small style="color: var(--text-muted);">(env: MAIL_PORT)</small></label>
                                <input type="number" name="mail_port" value="{{ old('mail_port', $settings->mail_port ?: $envSettings['mail_port']) }}" class="form-control">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>SMTP Username <small style="color: var(--text-muted);">(env: MAIL_USERNAME)</small></label>
                                <input type="text" name="mail_username" value="{{ old('mail_username', $settings->mail_username ?: $envSettings['mail_username']) }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>SMTP Password <small style="color: var(--text-muted);">(env: MAIL_PASSWORD)</small></label>
                                <input type="password" name="mail_password" value="{{ old('mail_password', $settings->mail_password ?: $envSettings['mail_password']) }}" class="form-control">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>From Email <small style="color: var(--text-muted);">(env: MAIL_FROM_ADDRESS)</small></label>
                                <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings->mail_from_address ?: $envSettings['mail_from_address']) }}" class="form-control" placeholder="noreply@yourcompany.com">
                            </div>
                            <div class="form-group">
                                <label>From Name <small style="color: var(--text-muted);">(env: MAIL_FROM_NAME)</small></label>
                                <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings->mail_from_name ?: $envSettings['mail_from_name']) }}" class="form-control" placeholder="Your Company Name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Encryption <small style="color: var(--text-muted);">(env: MAIL_ENCRYPTION)</small></label>
                            <select name="mail_encryption" class="form-control">
                                <option value="tls" {{ old('mail_encryption', $settings->mail_encryption ?: $envSettings['mail_encryption']) == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                <option value="ssl" {{ old('mail_encryption', $settings->mail_encryption ?: $envSettings['mail_encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="" {{ old('mail_encryption', $settings->mail_encryption ?: $envSettings['mail_encryption']) == '' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="position: fixed; bottom: 0; left: var(--sidebar-w); right: 0; background: var(--bg-card); border-top: 1px solid var(--border-subtle); padding: 1rem 1.5rem; display: flex; justify-content: flex-end; z-index: 100;">
            <button type="submit" class="btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem;">
                💾 Save Email Settings
            </button>
        </div>
    </form>

    <style>
        .settings-form { margin-bottom: 100px; }
        .settings-single { max-width: 800px; margin: 0 auto; }
    </style>
@endsection
