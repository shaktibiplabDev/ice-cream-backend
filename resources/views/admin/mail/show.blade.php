{{-- resources/views/admin/mail/show.blade.php --}}
@extends('layouts.admin')

@section('title', $email->subject ?: 'No Subject')

@section('content')
<div class="email-inbox-container">

    {{-- ── Top Toolbar ── --}}
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
            {{-- Email-level actions in toolbar --}}
            <a href="{{ route('admin.mail.inbox') }}" class="toolbar-nav-btn" title="Back to Inbox">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            @if($email->type !== 'sent')
                <a href="{{ route('admin.mail.reply', $email) }}" class="toolbar-nav-btn" title="Reply">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </a>
            @endif
            <button class="toolbar-nav-btn {{ $email->is_starred ? 'starred' : '' }}" onclick="toggleStar('{{ $email->id }}')" id="star-btn" type="button" title="Star">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $email->is_starred ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </button>
            <form action="{{ route('admin.mail.destroy', $email) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this email?')">
                @csrf @method('DELETE')
                <button type="submit" class="toolbar-nav-btn danger" title="Delete">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- ── Three-column body ── --}}
    <div class="inbox-main-layout">

        {{-- Sidebar --}}
        <aside class="inbox-sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('admin.mail.inbox') }}" class="nav-link {{ request()->routeIs('admin.mail.inbox') || request()->routeIs('admin.mail.show') ? 'active' : '' }}">
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

        {{-- Reading pane --}}
        <main class="email-reading-pane">
            <div class="reading-scroll">

                {{-- Subject --}}
                <h1 class="email-view-subject">{{ $email->subject ?: '(No Subject)' }}</h1>

                {{-- Sender card --}}
                <div class="email-sender-card">
                    <div class="sender-avatar">
                        {{ strtoupper(substr($email->type === 'sent' ? ($email->to_name ?: $email->to_email) : ($email->from_name ?: $email->from_email), 0, 1)) }}
                    </div>
                    <div class="sender-details">
                        <div class="sender-line">
                            <span class="sender-name-text">{{ $email->type === 'sent' ? $email->to_name : $email->from_name }}</span>
                            <span class="sender-email-text">&lt;{{ $email->type === 'sent' ? $email->to_email : $email->from_email }}&gt;</span>
                        </div>
                        <div class="recipient-line">
                            <span class="to-label">to</span>
                            <span class="to-email-text">{{ $email->type === 'sent' ? $email->from_email : $email->to_email }}</span>
                        </div>
                    </div>
                    <div class="email-timestamp">
                        {{ $email->sent_at?->format('M j, Y, g:i A') ?? 'Draft' }}
                    </div>
                </div>

                {{-- Body --}}
                <div class="email-view-body">
                    {!! nl2br(e($email->body)) !!}
                </div>

                {{-- Attachments --}}
                @if($email->attachments && count($email->attachments) > 0)
                    <div class="email-view-attachments">
                        <div class="attachments-title">
                            {{ count($email->attachments) }} Attachment{{ count($email->attachments) > 1 ? 's' : '' }}
                        </div>
                        <div class="attachments-grid">
                            @foreach($email->attachments as $attachment)
                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="attachment-card">
                                    <div class="attachment-icon-file">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                                            <polyline points="13 2 13 9 20 9"/>
                                        </svg>
                                    </div>
                                    <div class="attachment-info">
                                        <div class="attachment-name-file">{{ $attachment['name'] }}</div>
                                        <div class="attachment-size-file">{{ number_format($attachment['size'] / 1024, 1) }} KB</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Reply button --}}
                @if($email->type !== 'sent')
                    <div class="email-reply-actions">
                        <a href="{{ route('admin.mail.reply', $email) }}" class="btn-reply">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Reply
                        </a>
                    </div>
                @endif

                {{-- Linked Inquiry --}}
                @if(isset($linkedInquiry) && $linkedInquiry)
                    <div class="linked-inquiry-section">
                        <div class="section-header">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                            </svg>
                            Linked to Inquiry
                        </div>
                        <a href="{{ route('admin.inquiries.show', $linkedInquiry) }}" class="inquiry-link-card">
                            <div class="inquiry-number-badge">{{ $linkedInquiry->inquiry_number }}</div>
                            <div class="inquiry-details-block">
                                <div class="inquiry-person">{{ $linkedInquiry->name }}</div>
                                <div class="inquiry-company">{{ $linkedInquiry->business_name }}</div>
                            </div>
                            <span class="inquiry-status-badge {{ $linkedInquiry->status }}">{{ $linkedInquiry->status }}</span>
                        </a>
                    </div>
                @endif

                {{-- Conversation thread --}}
                @if(isset($conversation) && $conversation->count() > 0)
                    <div class="conversation-section">
                        <div class="section-header">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            Conversation ({{ $conversation->count() }})
                        </div>
                        <div class="conversation-list">
                            @foreach($conversation as $threadEmail)
                                <a href="{{ route('admin.mail.show', $threadEmail) }}" class="conversation-item {{ $threadEmail->id === $email->id ? 'current' : '' }}">
                                    <div class="conv-avatar">{{ strtoupper(substr($threadEmail->from_name, 0, 1)) }}</div>
                                    <div class="conv-content">
                                        <div class="conv-header-row">
                                            <span class="conv-from">{{ $threadEmail->from_name }}</span>
                                            <span class="conv-date">{{ $threadEmail->sent_at?->format('M j') }}</span>
                                        </div>
                                        <div class="conv-subject">{{ $threadEmail->subject ?: '(No Subject)' }}</div>
                                        <div class="conv-preview">{{ $threadEmail->getExcerpt(60) }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>{{-- /reading-scroll --}}
        </main>

    </div>{{-- /inbox-main-layout --}}
</div>{{-- /email-inbox-container --}}

<style>
    /* ─── Shared shell (same as inbox.blade.php) ─────────────────────── */
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

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Toolbar action buttons */
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
    .toolbar-nav-btn:hover {
        background: rgba(255,255,255,.08);
        color: var(--text-primary, #fff);
    }
    .toolbar-nav-btn.starred { color: #fbbc04; }
    .toolbar-nav-btn.danger:hover { color: #f87171; background: rgba(248,113,113,.1); }

    /* ─── Layout ─────────────────────────────────────────────────────── */
    .inbox-main-layout {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    /* ─── Sidebar (identical to inbox) ───────────────────────────────── */
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

    /* ─── Reading pane ───────────────────────────────────────────────── */
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

    /* Subject */
    .email-view-subject {
        font-size: 1.25rem;
        font-weight: 500;
        color: var(--text-primary, #f0f0f0);
        margin: 0 0 20px;
        line-height: 1.35;
    }

    /* Sender card */
    .email-sender-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-subtle, #2c2c30);
        margin-bottom: 24px;
    }

    .sender-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #fff;
        font-size: 15px;
        flex-shrink: 0;
    }

    .sender-details { flex: 1; min-width: 0; }

    .sender-line { margin-bottom: 3px; }
    .sender-name-text { font-weight: 600; font-size: 14px; color: var(--text-primary, #f0f0f0); }
    .sender-email-text { font-size: 13px; color: var(--text-muted, #8e8e96); margin-left: 6px; }

    .recipient-line { font-size: 13px; color: var(--text-muted, #8e8e96); }
    .to-label { margin-right: 4px; }
    .to-email-text { color: var(--text-secondary, #c0c0c8); }

    .email-timestamp {
        font-size: 12px;
        color: var(--text-muted, #8e8e96);
        white-space: nowrap;
        flex-shrink: 0;
    }

    /* Body */
    .email-view-body {
        font-size: 14px;
        line-height: 1.75;
        color: var(--text-secondary, #d0d0d8);
        white-space: pre-wrap;
        word-break: break-word;
        margin-bottom: 28px;
    }

    /* Attachments */
    .email-view-attachments {
        padding-top: 20px;
        border-top: 1px solid var(--border-subtle, #2c2c30);
        margin-bottom: 24px;
    }
    .attachments-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted, #8e8e96);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 10px;
    }
    .attachments-grid { display: flex; flex-wrap: wrap; gap: 10px; }
    .attachment-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: rgba(255,255,255,.04);
        border: 1px solid var(--border-subtle, #2c2c30);
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-secondary, #d0d0d8);
        transition: all .15s;
        max-width: 240px;
    }
    .attachment-card:hover { background: rgba(255,255,255,.08); border-color: #4f46e5; }
    .attachment-icon-file { color: var(--text-muted, #6c6c74); flex-shrink: 0; }
    .attachment-name-file {
        font-size: 13px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
    }
    .attachment-size-file { font-size: 11px; color: var(--text-muted, #8e8e96); margin-top: 2px; }

    /* Reply button */
    .email-reply-actions {
        padding-top: 20px;
        border-top: 1px solid var(--border-subtle, #2c2c30);
        margin-bottom: 28px;
    }
    .btn-reply {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        background: rgba(79,70,229,.15);
        border: 1px solid rgba(79,70,229,.4);
        color: #a5b4fc;
        text-decoration: none;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        transition: all .2s;
    }
    .btn-reply:hover { background: rgba(79,70,229,.3); color: #c7d2fe; transform: translateY(-1px); }

    /* Linked Inquiry */
    .linked-inquiry-section,
    .conversation-section {
        background: rgba(255,255,255,.02);
        border: 1px solid var(--border-subtle, #2c2c30);
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 16px;
    }
    .section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted, #8e8e96);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-subtle, #2c2c30);
    }
    .inquiry-link-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: rgba(255,255,255,.03);
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-secondary, #d0d0d8);
        transition: background .15s;
    }
    .inquiry-link-card:hover { background: rgba(255,255,255,.07); }
    .inquiry-number-badge {
        font-family: monospace;
        font-size: 12px;
        color: #818cf8;
        font-weight: 600;
        padding: 3px 8px;
        background: rgba(79,70,229,.15);
        border-radius: 6px;
        flex-shrink: 0;
    }
    .inquiry-details-block { flex: 1; }
    .inquiry-person { font-size: 13px; font-weight: 500; color: var(--text-primary, #f0f0f0); }
    .inquiry-company { font-size: 12px; color: var(--text-muted, #8e8e96); }
    .inquiry-status-badge {
        padding: 3px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        text-transform: capitalize;
        flex-shrink: 0;
    }
    .inquiry-status-badge.new { background: rgba(248,113,113,.15); color: #f87171; }
    .inquiry-status-badge.in_progress { background: rgba(251,191,36,.15); color: #fbbf24; }
    .inquiry-status-badge.resolved { background: rgba(52,211,153,.15); color: #34d399; }

    /* Conversation thread */
    .conversation-list { display: flex; flex-direction: column; gap: 4px; }
    .conversation-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-secondary, #d0d0d8);
        transition: background .15s;
        border: 1px solid transparent;
    }
    .conversation-item:hover { background: rgba(255,255,255,.04); }
    .conversation-item.current {
        background: rgba(79,70,229,.1);
        border-color: rgba(79,70,229,.4);
    }
    .conv-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #fff;
        font-size: 12px;
        flex-shrink: 0;
    }
    .conv-content { flex: 1; min-width: 0; }
    .conv-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px; }
    .conv-from { font-size: 13px; font-weight: 500; color: var(--text-primary, #f0f0f0); }
    .conv-date { font-size: 11px; color: var(--text-muted, #8e8e96); }
    .conv-subject { font-size: 12px; color: var(--text-secondary, #c0c0c8); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 1px; }
    .conv-preview { font-size: 11px; color: var(--text-muted, #8e8e96); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Responsive */
    @media (max-width: 800px) {
        .inbox-sidebar { width: 60px; padding: 12px 4px; }
        .nav-link span:first-of-type, .count-badge { display: none; }
        .nav-link { justify-content: center; padding: 10px 0; gap: 0; }
        .reading-scroll { padding: 16px 18px 32px; }
    }
    @media (max-width: 600px) {
        .toolbar-center { display: none; }
        .sender-email-text { display: none; }
    }
</style>

<script>
    function toggleStar(emailId) {
        fetch(`/admin/mail/${emailId}/star`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            const btn = document.getElementById('star-btn');
            const svg = btn.querySelector('svg');
            svg.setAttribute('fill', data.is_starred ? 'currentColor' : 'none');
            btn.classList.toggle('starred', !!data.is_starred);
        })
        .catch(err => console.error('Star error:', err));
    }
</script>
@endsection