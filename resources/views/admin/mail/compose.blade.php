{{-- resources/views/admin/mail/compose.blade.php --}}
@extends('layouts.admin')

@section('title', 'Compose Email')

@section('content')
<div class="email-inbox-container">

    {{-- ── Toolbar ── --}}
    <div class="inbox-toolbar">
        <div class="toolbar-left">
            <a href="{{ route('admin.mail.compose') }}" class="btn-compose active-compose">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Compose
            </a>
        </div>
        <div class="toolbar-center">
            <span class="unread-badge">{{ $unreadCount ?? 0 }} new</span>
        </div>
        <div class="toolbar-right">
            <a href="{{ route('admin.mail.inbox') }}" class="toolbar-nav-btn" title="Back to Inbox">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- ── Body ── --}}
    <div class="inbox-main-layout">

        {{-- Sidebar --}}
        <aside class="inbox-sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('admin.mail.inbox') }}" class="nav-link">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <span>Inbox</span>
                    @if(($unreadCount ?? 0) > 0)
                        <span class="count-badge">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.mail.starred') }}" class="nav-link">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                    <span>Starred</span>
                </a>
                <a href="{{ route('admin.mail.sent') }}" class="nav-link">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                    <span>Sent</span>
                </a>
                <a href="{{ route('admin.mail.drafts') }}" class="nav-link">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <span>Drafts</span>
                </a>
            </nav>
        </aside>

        {{-- Compose pane --}}
        <main class="email-reading-pane">
            <div class="reading-scroll">

                <form method="POST" action="{{ route('admin.mail.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="compose-card">

                        {{-- From --}}
                        <div class="compose-row">
                            <span class="compose-label">From</span>
                            <span class="compose-from-display">
                                {{ $settings->company_name }} &lt;{{ $settings->email }}&gt;
                            </span>
                        </div>

                        {{-- To --}}
                        <div class="compose-row">
                            <label class="compose-label" for="to_email">To</label>
                            <input type="email" name="to_email" id="to_email"
                                   value="{{ old('to_email') }}"
                                   class="compose-input @error('to_email') is-invalid @enderror"
                                   placeholder="recipient@example.com"
                                   required>
                            @error('to_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Cc --}}
                        <div class="compose-row">
                            <label class="compose-label" for="cc">Cc</label>
                            <input type="text" name="cc" id="cc"
                                   value="{{ old('cc') }}"
                                   class="compose-input"
                                   placeholder="cc1@example.com, cc2@example.com">
                        </div>

                        {{-- Subject --}}
                        <div class="compose-row">
                            <label class="compose-label" for="subject">Subject</label>
                            <input type="text" name="subject" id="subject"
                                   value="{{ old('subject') }}"
                                   class="compose-input @error('subject') is-invalid @enderror"
                                   placeholder="Email subject"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Body --}}
                        <div class="compose-row compose-row-body">
                            <textarea name="body" id="body"
                                      class="compose-textarea @error('body') is-invalid @enderror"
                                      placeholder="Write your message here..."
                                      required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Footer: attachments + actions --}}
                        <div class="compose-footer">
                            <div class="compose-footer-left">
                                {{-- Send button --}}
                                <button type="submit" name="send_now" value="1" class="btn-send">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                    </svg>
                                    Send
                                </button>

                                {{-- Attach --}}
                                <div class="attach-btn-wrap">
                                    <input type="file" name="attachments[]" id="attachments" multiple class="file-input-hidden">
                                    <label for="attachments" class="btn-attach" title="Attach files">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                        </svg>
                                    </label>
                                </div>
                            </div>

                            <a href="{{ route('admin.mail.inbox') }}" class="btn-cancel">Discard</a>
                        </div>

                        {{-- File list (shown below footer) --}}
                        <div id="file-list" class="file-list"></div>

                    </div>
                </form>

            </div>{{-- /reading-scroll --}}
        </main>

    </div>{{-- /inbox-main-layout --}}
</div>{{-- /email-inbox-container --}}

<style>
    /* ─── Shared shell ───────────────────────────────────────────────── */
    .email-inbox-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        background: var(--bg-card, #0f0f12);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.15);
    }

    .inbox-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 24px;
        background: var(--bg-surface, #1a1a1f);
        border-bottom: 1px solid var(--border-subtle, #2c2c30);
        flex-shrink: 0;
    }

    .btn-compose {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        background: #c2e7ff;
        color: #001d35;
        text-decoration: none;
        border-radius: 18px;
        font-weight: 500;
        font-size: 14px;
        transition: all .2s;
    }
    .btn-compose:hover { background: #b0d4f0; transform: translateY(-1px); }

    .toolbar-center .unread-badge {
        font-size: 14px;
        background: rgba(79,70,229,.15);
        padding: 4px 12px;
        border-radius: 20px;
        color: var(--text-secondary, #e0e0e0);
    }

    .toolbar-right { display: flex; align-items: center; gap: 4px; }

    .toolbar-nav-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: transparent;
        border: none;
        color: var(--text-muted, #8e8e96);
        cursor: pointer;
        text-decoration: none;
        transition: all .15s;
    }
    .toolbar-nav-btn:hover { background: rgba(255,255,255,.08); color: var(--text-primary, #fff); }

    .inbox-main-layout { display: flex; flex: 1; overflow: hidden; }

    /* ─── Sidebar ────────────────────────────────────────────────────── */
    .inbox-sidebar {
        width: 200px;
        flex-shrink: 0;
        padding: 16px 12px;
        border-right: 1px solid var(--border-subtle, #2c2c30);
        background: var(--bg-surface, #1a1a1f);
        overflow-y: auto;
    }
    .sidebar-nav { display: flex; flex-direction: column; gap: 4px; }
    .nav-link {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 10px 16px;
        border-radius: 20px;
        color: var(--text-secondary, #b0b0b8);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all .15s;
    }
    .nav-link:hover { background: rgba(255,255,255,.05); color: var(--text-primary, #fff); }
    .nav-link.active { background: rgba(79,70,229,.2); color: #818cf8; }
    .nav-icon { opacity: .7; flex-shrink: 0; }
    .nav-link span:first-of-type { flex: 1; }
    .count-badge {
        background: #4f46e5;
        color: #fff;
        padding: 2px 8px;
        border-radius: 16px;
        font-size: 11px;
        font-weight: 600;
    }

    /* ─── Compose pane ───────────────────────────────────────────────── */
    .email-reading-pane {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: var(--bg-card, #0f0f12);
    }
    .reading-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 28px 36px 40px;
        min-height: 0;
    }

    /* ─── Compose card (Gmail-style inline fields) ───────────────────── */
    .compose-card {
        background: rgba(255,255,255,.015);
        border: 1px solid var(--border-subtle, #2c2c30);
        border-radius: 10px;
        overflow: hidden;
    }

    .compose-row {
        display: flex;
        align-items: center;
        padding: 0 20px;
        border-bottom: 1px solid var(--border-subtle, #2c2c30);
        min-height: 48px;
    }
    .compose-row-body {
        align-items: stretch;
        padding: 0;
        border-bottom: 1px solid var(--border-subtle, #2c2c30);
    }

    .compose-label {
        width: 56px;
        flex-shrink: 0;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-muted, #8e8e96);
    }

    .compose-from-display {
        flex: 1;
        font-size: 14px;
        color: var(--text-secondary, #c0c0c8);
    }

    .compose-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: var(--text-primary, #f0f0f0);
        font-size: 14px;
        font-family: inherit;
        padding: 0;
    }
    .compose-input::placeholder { color: var(--text-muted, #6c6c74); }
    .compose-input.is-invalid { color: #f87171; }

    .compose-textarea {
        flex: 1;
        width: 100%;
        min-height: 340px;
        background: transparent;
        border: none;
        outline: none;
        resize: none;
        color: var(--text-primary, #f0f0f0);
        font-size: 14px;
        font-family: inherit;
        line-height: 1.7;
        padding: 16px 20px;
        box-sizing: border-box;
    }
    .compose-textarea::placeholder { color: var(--text-muted, #6c6c74); }

    .invalid-feedback { color: #f87171; font-size: 12px; margin-top: 2px; }

    /* ─── Footer bar ─────────────────────────────────────────────────── */
    .compose-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        background: rgba(255,255,255,.02);
    }

    .compose-footer-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-send {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 22px;
        background: #c2e7ff;
        color: #001d35;
        border: none;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-send:hover { background: #b0d4f0; transform: translateY(-1px); }

    .attach-btn-wrap { position: relative; }
    .file-input-hidden { display: none; }
    .btn-attach {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        color: var(--text-muted, #8e8e96);
        cursor: pointer;
        transition: all .15s;
    }
    .btn-attach:hover { background: rgba(255,255,255,.08); color: var(--text-primary, #fff); }

    .btn-cancel {
        font-size: 13px;
        color: var(--text-muted, #8e8e96);
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 6px;
        transition: all .15s;
    }
    .btn-cancel:hover { color: #f87171; background: rgba(248,113,113,.08); }

    /* ─── File chips ─────────────────────────────────────────────────── */
    .file-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 0 20px 14px;
    }
    .file-list:empty { padding: 0; }

    .file-item {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        background: rgba(79,70,229,.1);
        border: 1px solid rgba(79,70,229,.2);
        border-radius: 6px;
        font-size: 12px;
        color: var(--text-secondary, #c0c0c8);
    }

    /* ─── Responsive ─────────────────────────────────────────────────── */
    @media (max-width: 800px) {
        .inbox-sidebar { width: 60px; padding: 12px 4px; }
        .nav-link span:first-of-type, .count-badge { display: none; }
        .nav-link { justify-content: center; padding: 10px 0; gap: 0; }
        .reading-scroll { padding: 16px 18px 32px; }
    }
    @media (max-width: 600px) {
        .toolbar-center { display: none; }
        .compose-label { width: 40px; }
    }

    /* Light Theme Support */
    [data-theme="light"] .email-inbox-container {
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .inbox-toolbar {
        background: rgba(255, 255, 255, 0.98);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .toolbar-nav-btn {
        color: var(--text-secondary);
    }

    [data-theme="light"] .toolbar-nav-btn:hover {
        background: rgba(0, 0, 0, 0.08);
        color: var(--text-primary);
    }

    [data-theme="light"] .inbox-sidebar {
        background: rgba(255, 255, 255, 0.98);
        border-right: 1px solid rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .nav-link {
        color: var(--text-secondary);
    }

    [data-theme="light"] .nav-link:hover {
        background: rgba(0, 0, 0, 0.05);
        color: var(--text-primary);
    }

    [data-theme="light"] .nav-link.active {
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
    }

    [data-theme="light"] .email-reading-pane {
        background: rgba(255, 255, 255, 0.95);
    }

    [data-theme="light"] .compose-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    [data-theme="light"] .compose-row {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .compose-row-body {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .compose-label {
        color: var(--text-muted);
    }

    [data-theme="light"] .compose-from-display {
        color: var(--text-secondary);
    }

    [data-theme="light"] .compose-input {
        color: var(--text-primary);
    }

    [data-theme="light"] .compose-input::placeholder {
        color: var(--text-muted);
    }

    [data-theme="light"] .compose-textarea {
        color: var(--text-primary);
    }

    [data-theme="light"] .compose-textarea::placeholder {
        color: var(--text-muted);
    }

    [data-theme="light"] .compose-footer {
        background: rgba(0, 0, 0, 0.03);
    }

    [data-theme="light"] .btn-attach {
        color: var(--text-muted);
    }

    [data-theme="light"] .btn-attach:hover {
        background: rgba(0, 0, 0, 0.08);
        color: var(--text-primary);
    }

    [data-theme="light"] .btn-cancel {
        color: var(--text-muted);
    }

    [data-theme="light"] .btn-cancel:hover {
        color: #dc2626;
        background: rgba(239, 68, 68, 0.1);
    }

    [data-theme="light"] .file-item {
        background: rgba(79, 70, 229, 0.08);
        border: 1px solid rgba(79, 70, 229, 0.15);
        color: var(--text-secondary);
    }
</style>

<script>
    document.getElementById('attachments').addEventListener('change', function (e) {
        const fileList = document.getElementById('file-list');
        fileList.innerHTML = '';
        Array.from(e.target.files).forEach(file => {
            const div = document.createElement('div');
            div.className = 'file-item';
            div.innerHTML = `
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                    <polyline points="13 2 13 9 20 9"/>
                </svg>
                <span>${file.name}</span>
                <span style="color:var(--text-muted,#6c6c74)">(${(file.size/1024).toFixed(1)} KB)</span>
            `;
            fileList.appendChild(div);
        });
    });
</script>
@endsection