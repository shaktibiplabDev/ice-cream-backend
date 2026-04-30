@extends('layouts.admin')

@section('title', 'Compose Email')

@section('content')
    <div class="page-header">
        <h1>
            <small>Send Email</small>
            Compose
        </h1>
    </div>

    <div class="glass-card">
        <form method="POST" action="{{ route('admin.mail.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-subtle);">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">From</label>
                    <div style="padding: 0.75rem 1rem; background: rgba(0,0,0,0.2); border-radius: var(--radius-md); color: var(--text-primary);">
                        {{ $settings->company_name }} &lt;{{ $settings->email }}&gt;
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="to_email" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">To</label>
                    <input type="email" name="to_email" id="to_email" value="{{ old('to_email') }}" required
                        class="form-control"
                        placeholder="recipient@example.com">
                    @error('to_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="to_name" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">Recipient Name (Optional)</label>
                    <input type="text" name="to_name" id="to_name" value="{{ old('to_name') }}"
                        class="form-control"
                        placeholder="John Doe">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="cc" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">CC</label>
                    <input type="text" name="cc" id="cc" value="{{ old('cc') }}"
                        class="form-control"
                        placeholder="cc1@example.com, cc2@example.com">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="subject" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">Subject</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                        class="form-control"
                        placeholder="Email subject">
                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-subtle);">
                <div class="form-group">
                    <label for="body" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">Message</label>
                    <textarea name="body" id="body" rows="12" required
                        class="form-control"
                        placeholder="Write your message here...">{{ old('body') }}</textarea>
                    @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label for="attachments" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">Attachments</label>
                    <input type="file" name="attachments[]" id="attachments" multiple
                        class="form-control"
                        style="padding: 0.5rem;">
                    <small style="color: var(--text-muted);">You can select multiple files. Max 10MB per file.</small>
                </div>
            </div>

            <div style="padding: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('admin.mail.inbox') }}" class="btn-secondary">Cancel</a>
                <button type="submit" name="send_now" value="0" class="btn-secondary">Save as Draft</button>
                <button type="submit" name="send_now" value="1" class="btn-primary">📤 Send Email</button>
            </div>
        </form>
    </div>
@endsection
