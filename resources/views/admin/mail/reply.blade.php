{{-- resources/views/admin/mail/reply.blade.php --}}
@extends('layouts.admin')

@section('title', 'Reply to ' . $email->from_name)

@section('content')
<div class="email-inbox-container">

    {{-- ── Toolbar ── --}}
    <div class="inbox-toolbar">
        <div class="toolbar-left">
            <a href="{{ route('admin.mail.compose') }}" class="btn-compose">
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
            <a href="{{ route('admin.mail.show', $email) }}" class="toolbar-nav-btn" title="Back">
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
                <a href="{{ route('admin.mail.inbox') }}" class="nav-link {{ request()->routeIs('admin.mail.inbox') ? 'active' : '' }}">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <span>Inbox</span>
                    @if(($unreadCount ?? 0) > 0)
                        <span class="count-badge">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.mail.starred') }}" class="nav-link {{ request()->routeIs('admin.mail.starred') ? 'active' : '' }}">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                    <span>Starred</span>
                </a>
                <a href="{{ route('admin.mail.sent') }}" class="nav-link {{ request()->routeIs('admin.mail.sent') ? 'active' : '' }}">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                    <span>Sent</span>
                </a>
                <a href="{{ route('admin.mail.drafts') }}" class="nav-link {{ request()->routeIs('admin.mail.drafts') ? 'active' : '' }}">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <span>Drafts</span>
                </a>
            </nav>
        </aside>

        {{-- Reply pane --}}
        <main class="email-reading-pane">
            <div class="reading-scroll">

                {{-- Original email context --}}
                <div class="original-email-card">
                    <div class="original-header">
                        <div class="original-subject">{{ $email->subject }}</div>
                        <div class="original-meta">
                            From: {{ $email->from_name }} &lt;{{ $email->from_email }}&gt;
                            · {{ $email->sent_at?->format('M j, Y g:i A') }}
                        </div>
                    </div>
                    <div class="original-preview">{{ $email->getExcerpt(200) }}</div>
                </div>

                {{-- Linked inquiry notice --}}
                @if($linkedInquiry)
                    <div class="linked-inquiry-alert">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#818cf8;margin-top:1px">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                        <div class="alert-content">
                            <div class="alert-title">Linked to Inquiry</div>
                            <div class="alert-text">
                                This email is from {{ $linkedInquiry->name }} ({{ $linkedInquiry->inquiry_number }}).
                                Your reply will be added to the inquiry conversation.
                            </div>
                            <a href="{{ route('admin.inquiries.show', $linkedInquiry) }}" class="alert-link">View Inquiry →</a>
                        </div>
                    </div>
                @endif

                {{-- Reply form --}}
                <div class="reply-form-card">
                    <form action="{{ route('admin.mail.reply.send', $email) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- To --}}
                        <div class="form-section">
                            <label class="form-label">To</label>
                            <div class="recipient-display">
                                <div class="recipient-avatar">{{ strtoupper(substr($email->from_name, 0, 1)) }}</div>
                                <div class="recipient-info">
                                    <div class="recipient-name">{{ $email->from_name }}</div>
                                    <div class="recipient-email">{{ $email->from_email }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Subject --}}
                        <div class="form-section">
                            <label class="form-label" for="subject">Subject</label>
                            <input type="text"
                                   id="subject"
                                   name="subject"
                                   value="Re: {{ $email->subject }}"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Body --}}
                        <div class="form-section">
                            <label class="form-label" for="body">Message</label>
                            <textarea id="body"
                                      name="body"
                                      rows="12"
                                      class="form-control @error('body') is-invalid @enderror"
                                      placeholder="Type your reply here..."
                                      required></textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Attachments --}}
                        <div class="form-section">
                            <label class="form-label">Attachments</label>
                            <div class="file-upload-area" id="drop-zone">
                                <input type="file"
                                       name="attachments[]"
                                       multiple
                                       class="file-input"
                                       id="attachments"
                                       accept="*/*">
                                <label for="attachments" class="file-upload-label">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--text-muted,#6c6c74)">
                                        <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                    </svg>
                                    <span class="upload-text">Drop files here or click to upload</span>
                                    <span class="upload-hint">Max 10MB per file</span>
                                </label>
                            </div>
                            <div id="file-list" class="file-list"></div>
                        </div>

                        {{-- Actions --}}
                        <div class="form-actions">
                            <a href="{{ route('admin.mail.show', $email) }}" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-send">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" fill="currentColor"/>
                                </svg>
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>

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

    /* ─── Reading / reply pane ───────────────────────────────────────── */
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

    /* ─── Original email card ────────────────────────────────────────── */
    .original-email-card {
        background: rgba(255,255,255,.02);
        border: 1px solid var(--border-subtle, #2c2c30);
        border-left: 3px solid rgba(79,70,229,.5);
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    .original-header { margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid var(--border-subtle, #2c2c30); }
    .original-subject { font-size: 14px; font-weight: 600; color: var(--text-primary, #f0f0f0); margin-bottom: 4px; }
    .original-meta { font-size: 12px; color: var(--text-muted, #8e8e96); }
    .original-preview { font-size: 13px; color: var(--text-secondary, #c0c0c8); line-height: 1.6; }

    /* ─── Linked inquiry alert ───────────────────────────────────────── */
    .linked-inquiry-alert {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        background: rgba(79,70,229,.08);
        border: 1px solid rgba(79,70,229,.25);
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }
    .alert-content { flex: 1; }
    .alert-title { font-size: 13px; font-weight: 600; color: var(--text-primary, #f0f0f0); margin-bottom: 4px; }
    .alert-text { font-size: 13px; color: var(--text-secondary, #c0c0c8); margin-bottom: 8px; line-height: 1.5; }
    .alert-link { font-size: 13px; color: #818cf8; text-decoration: none; font-weight: 500; }
    .alert-link:hover { text-decoration: underline; }

    /* ─── Reply form card ────────────────────────────────────────────── */
    .reply-form-card {
        background: rgba(255,255,255,.015);
        border: 1px solid var(--border-subtle, #2c2c30);
        border-radius: 10px;
        padding: 24px 28px;
    }

    .form-section { margin-bottom: 20px; }

    .form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted, #8e8e96);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 8px;
    }

    /* Recipient row */
    .recipient-display {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: rgba(255,255,255,.04);
        border: 1px solid var(--border-subtle, #2c2c30);
        border-radius: 8px;
    }
    .recipient-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #fff;
        font-size: 14px;
        flex-shrink: 0;
    }
    .recipient-name { font-size: 14px; font-weight: 500; color: var(--text-primary, #f0f0f0); }
    .recipient-email { font-size: 12px; color: var(--text-muted, #8e8e96); }

    /* Inputs */
    .form-control {
        width: 100%;
        padding: 10px 14px;
        background: rgba(255,255,255,.04);
        border: 1px solid var(--border-subtle, #2c2c30);
        border-radius: 8px;
        color: var(--text-primary, #f0f0f0);
        font-size: 14px;
        font-family: inherit;
        transition: border-color .15s;
        box-sizing: border-box;
        resize: vertical;
    }
    .form-control:focus {
        outline: none;
        border-color: rgba(79,70,229,.6);
        background: rgba(255,255,255,.06);
    }
    .form-control::placeholder { color: var(--text-muted, #6c6c74); }
    .form-control.is-invalid { border-color: #f87171; }
    .invalid-feedback { color: #f87171; font-size: 12px; margin-top: 4px; }

    /* File upload */
    .file-upload-area {
        position: relative;
        border: 2px dashed var(--border-subtle, #2c2c30);
        border-radius: 8px;
        padding: 28px;
        text-align: center;
        transition: all .2s;
        cursor: pointer;
    }
    .file-upload-area:hover, .file-upload-area.drag-over {
        border-color: rgba(79,70,229,.6);
        background: rgba(79,70,229,.05);
    }
    .file-input {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        pointer-events: none;
    }
    .upload-text { font-size: 13px; color: var(--text-secondary, #c0c0c8); }
    .upload-hint { font-size: 12px; color: var(--text-muted, #8e8e96); }

    .file-list { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
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

    /* Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid var(--border-subtle, #2c2c30);
        margin-top: 4px;
    }
    .btn-cancel {
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-secondary, #c0c0c8);
        text-decoration: none;
        background: rgba(255,255,255,.05);
        border: 1px solid var(--border-subtle, #2c2c30);
        transition: all .15s;
    }
    .btn-cancel:hover { background: rgba(255,255,255,.1); }

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

    /* Responsive */
    @media (max-width: 800px) {
        .inbox-sidebar { width: 60px; padding: 12px 4px; }
        .nav-link span:first-of-type, .count-badge { display: none; }
        .nav-link { justify-content: center; padding: 10px 0; gap: 0; }
        .reading-scroll { padding: 16px 18px 32px; }
        .reply-form-card { padding: 16px; }
    }
    @media (max-width: 600px) {
        .toolbar-center { display: none; }
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
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                <span>${file.name}</span>
                <span style="color:var(--text-muted,#6c6c74)">(${(file.size / 1024).toFixed(1)} KB)</span>
            `;
            fileList.appendChild(div);
        });
    });

    // Drag-over highlight
    const dropZone = document.getElementById('drop-zone');
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', () => dropZone.classList.remove('drag-over'));
</script>
@endsection