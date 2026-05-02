{{-- resources/views/admin/mail/starred.blade.php --}}
@extends('layouts.admin')

@section('title', 'Starred')

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
            <span class="unread-badge">{{ $emails->total() }} starred</span>
        </div>
        <div class="toolbar-right">
            @if($emails->hasPages())
                <span class="toolbar-page-info">
                    {{ $emails->firstItem() }}–{{ $emails->lastItem() }} of {{ $emails->total() }}
                </span>
                @if($emails->previousPageUrl())
                    <a href="{{ $emails->previousPageUrl() }}" class="toolbar-nav-btn">&#8249;</a>
                @else
                    <span class="toolbar-nav-btn disabled">&#8249;</span>
                @endif
                @if($emails->nextPageUrl())
                    <a href="{{ $emails->nextPageUrl() }}" class="toolbar-nav-btn">&#8250;</a>
                @else
                    <span class="toolbar-nav-btn disabled">&#8250;</span>
                @endif
            @endif
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
                <a href="{{ route('admin.mail.starred') }}" class="nav-link active">
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

        {{-- Starred emails list --}}
        <main class="inbox-email-list">
            @if($emails->count() > 0)
                <div class="email-table-wrapper">
                    <div class="email-table-header">
                        <div class="header-star"></div>
                        <div class="header-from">From</div>
                        <div class="header-subject">Subject</div>
                        <div class="header-date">Received</div>
                        <div class="header-actions"></div>
                    </div>

                    <div class="email-rows">
                        @foreach($emails as $email)
                            <div class="email-row starred-row {{ (!$email->is_read && $email->type === 'inbox') ? 'unread' : '' }}" data-email-id="{{ $email->id }}">
                                <div class="row-star">
                                    <button class="star-btn starred" data-id="{{ $email->id }}" data-starred="true">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1">
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="row-from">
                                    <span class="sender-name">
                                        {{ $email->from_name ?? ($email->type === 'sent' ? 'Me' : $email->to_email) }}
                                    </span>
                                </div>
                                <div class="row-subject">
                                    <span class="email-subject">{{ $email->subject ?: '(No subject)' }}</span>
                                    <span class="email-snippet">{{ $email->getExcerpt(60) }}</span>
                                </div>
                                <div class="row-date">
                                    {{ $email->sent_at ? $email->sent_at->format('M j') : $email->created_at->format('M j') }}
                                </div>
                                <div class="row-actions">
                                    <a href="{{ route('admin.mail.show', $email) }}"
                                       class="action-btn action-view" title="View email">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        View
                                    </a>
                                    <form action="{{ route('admin.mail.destroy', $email) }}" method="POST"
                                          style="display:inline" onsubmit="return confirm('Delete this email?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn action-delete" title="Delete email">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pagination --}}
                @if($emails->hasPages())
                <div class="inbox-pagination">
                    <span class="pagination-info">
                        Showing {{ $emails->firstItem() }} to {{ $emails->lastItem() }} of {{ $emails->total() }} starred emails
                    </span>
                    <div class="pagination-links">
                        @if($emails->onFirstPage())
                            <span class="page-btn disabled">&#8249;</span>
                        @else
                            <a href="{{ $emails->previousPageUrl() }}" class="page-btn">&#8249;</a>
                        @endif

                        @php
                            $currentPage = $emails->currentPage();
                            $lastPage    = $emails->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end   = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($start > 1)
                            <a href="{{ $emails->url(1) }}" class="page-btn">1</a>
                            @if($start > 2)<span class="page-ellipsis">…</span>@endif
                        @endif
                        @for($p = $start; $p <= $end; $p++)
                            @if($p === $currentPage)
                                <span class="page-btn current">{{ $p }}</span>
                            @else
                                <a href="{{ $emails->url($p) }}" class="page-btn">{{ $p }}</a>
                            @endif
                        @endfor
                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)<span class="page-ellipsis">…</span>@endif
                            <a href="{{ $emails->url($lastPage) }}" class="page-btn">{{ $lastPage }}</a>
                        @endif

                        @if($emails->hasMorePages())
                            <a href="{{ $emails->nextPageUrl() }}" class="page-btn">&#8250;</a>
                        @else
                            <span class="page-btn disabled">&#8250;</span>
                        @endif
                    </div>
                </div>
                @endif

            @else
                <div class="empty-inbox">
                    <div class="empty-icon">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                        </svg>
                    </div>
                    <h3>No starred emails</h3>
                    <p>Click the star icon next to any email to mark it as important.</p>
                    <a href="{{ route('admin.mail.inbox') }}" class="btn-compose empty-compose-btn">
                        Go to Inbox
                    </a>
                </div>
            @endif
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
    .toolbar-right { display: flex; align-items: center; gap: 6px; }
    .toolbar-page-info { font-size: 13px; color: var(--text-muted, #8e8e96); margin-right: 4px; }
    .toolbar-nav-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        font-size: 18px;
        line-height: 1;
        color: var(--text-secondary, #d0d0d8);
        text-decoration: none;
        background: rgba(255,255,255,.05);
        transition: background .15s;
    }
    .toolbar-nav-btn:hover:not(.disabled) { background: rgba(255,255,255,.1); }
    .toolbar-nav-btn.disabled { opacity: .3; cursor: default; }

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

    /* ─── Email list pane ────────────────────────────────────────────── */
    .inbox-email-list {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: var(--bg-card, #0f0f12);
        min-width: 0;
    }
    .email-table-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 0;
    }
    .email-table-header {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        background: rgba(255,255,255,.02);
        border-bottom: 1px solid var(--border-subtle, #2c2c30);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted, #8e8e96);
        letter-spacing: .3px;
        flex-shrink: 0;
    }
    .email-rows { flex: 1; overflow-y: auto; min-height: 0; }

    .email-row {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        border-bottom: 1px solid var(--border-subtle, #2c2c30);
        color: var(--text-secondary, #d0d0d8);
        transition: background .1s;
        gap: 0;
        cursor: pointer;
    }
    .email-row:hover { background: rgba(255,255,255,.03); }
    .email-row.unread {
        background: rgba(79,70,229,.05);
    }
    .email-row.unread .sender-name,
    .email-row.unread .email-subject {
        font-weight: 600;
        color: var(--text-primary, #fff);
    }

    /* Column widths */
    .header-star, .row-star     { width: 40px; flex-shrink: 0; text-align: center; }
    .header-from, .row-from     { width: 160px; flex-shrink: 0; padding-right: 16px; min-width: 0; }
    .header-subject, .row-subject {
        flex: 1; min-width: 0;
        display: flex;
        gap: 10px;
        align-items: baseline;
    }
    .header-date, .row-date     { width: 80px; flex-shrink: 0; text-align: right; font-size: 12px; color: var(--text-muted, #8e8e96); }
    .header-actions, .row-actions {
        width: 130px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    /* Star button */
    .star-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all .15s;
        color: #6b7280;
    }
    .star-btn:hover {
        transform: scale(1.1);
        background: rgba(255,255,255,.05);
    }
    .star-btn.starred {
        color: #fbbf24;
    }
    .star-btn.starred svg {
        fill: #fbbf24;
    }

    .sender-name {
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        color: var(--text-secondary, #d0d0d8);
    }
    .email-row.unread .sender-name {
        color: var(--text-primary, #fff);
    }

    .email-subject {
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--text-secondary, #e0e0e0);
    }
    .email-snippet {
        font-size: 13px;
        color: var(--text-muted, #8e8e96);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
    }
    .email-snippet::before { content: "—"; margin-right: 8px; opacity: .6; }

    /* Action buttons */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: all .15s;
        font-family: inherit;
    }
    .action-view {
        background: rgba(79,70,229,.12);
        color: #818cf8;
        border: 1px solid rgba(79,70,229,.2);
    }
    .action-view:hover { background: rgba(79,70,229,.25); }
    .action-delete {
        background: rgba(248,113,113,.08);
        color: #f87171;
        border: 1px solid rgba(248,113,113,.15);
    }
    .action-delete:hover { background: rgba(248,113,113,.2); }

    /* ─── Pagination ─────────────────────────────────────────────────── */
    .inbox-pagination {
        padding: 10px 20px;
        border-top: 1px solid var(--border-subtle, #2c2c30);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-surface, #1a1a1f);
        flex-shrink: 0;
    }
    .pagination-info { font-size: 12px; color: var(--text-muted, #8e8e96); }
    .pagination-links { display: flex; align-items: center; gap: 4px; }
    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 30px;
        padding: 0 6px;
        border-radius: 6px;
        font-size: 13px;
        color: var(--text-secondary, #d0d0d8);
        text-decoration: none;
        background: rgba(255,255,255,.04);
        border: 1px solid transparent;
        transition: all .15s;
    }
    .page-btn:hover:not(.disabled):not(.current) { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.1); }
    .page-btn.current { background: rgba(79,70,229,.3); border-color: #4f46e5; color: #a5b4fc; font-weight: 600; }
    .page-btn.disabled { opacity: .3; cursor: default; }
    .page-ellipsis { font-size: 13px; color: var(--text-muted, #8e8e96); padding: 0 4px; }

    /* ─── Empty state ────────────────────────────────────────────────── */
    .empty-inbox {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 16px;
        padding: 40px;
    }
    .empty-icon svg { stroke: var(--text-muted, #6c6c74); opacity: .4; }
    .empty-inbox h3 { font-size: 22px; font-weight: 400; color: var(--text-primary, #f0f0f0); margin: 0; }
    .empty-inbox p { font-size: 14px; color: var(--text-muted, #9ca3af); max-width: 320px; margin: 0; }
    .empty-compose-btn { background: rgba(79,70,229,.2); color: #a5b4fc; box-shadow: none; }
    .empty-compose-btn:hover { background: rgba(79,70,229,.35); color: #c7d2fe; }

    /* Prevent action buttons from triggering row click */
    .row-actions, .row-star {
        pointer-events: auto;
    }
    .action-btn, .star-btn {
        position: relative;
        z-index: 1;
    }

    /* ─── Responsive ─────────────────────────────────────────────────── */
    @media (max-width: 800px) {
        .inbox-sidebar { width: 60px; padding: 12px 4px; }
        .nav-link span:first-of-type, .count-badge { display: none; }
        .nav-link { justify-content: center; padding: 10px 0; gap: 0; }
        .row-from { width: 110px; }
        .email-snippet { display: none; }
        .pagination-info { display: none; }
    }
    @media (max-width: 600px) {
        .toolbar-center { display: none; }
        .row-from { width: 80px; }
        .header-actions, .row-actions { width: 100px; }
        .action-btn span { display: none; }
        .action-btn svg { margin: 0; }
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

    [data-theme="light"] .inbox-email-list {
        background: rgba(255, 255, 255, 0.95);
    }

    [data-theme="light"] .email-table-header {
        background: rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        color: var(--text-muted);
    }

    [data-theme="light"] .email-row {
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        color: var(--text-secondary);
    }

    [data-theme="light"] .email-row:hover {
        background: rgba(0, 0, 0, 0.03);
    }

    [data-theme="light"] .row-date {
        color: var(--text-muted);
    }

    [data-theme="light"] .email-snippet {
        color: var(--text-muted);
    }

    [data-theme="light"] .inbox-pagination {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        background: rgba(255, 255, 255, 0.98);
    }

    [data-theme="light"] .page-btn {
        background: rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.1);
        color: var(--text-secondary);
    }

    [data-theme="light"] .page-btn.current {
        background: var(--accent-gradient);
        color: white;
        border-color: transparent;
    }

    [data-theme="light"] .star-svg {
        color: #9ca3af;
    }

    [data-theme="light"] .star-svg.starred-active {
        color: #f59e0b;
    }
</style>

<script>
    // Make entire row clickable (except star button and action buttons)
    document.querySelectorAll('.email-row').forEach(row => {
        const viewLink = row.querySelector('.action-view');
        if (viewLink) {
            row.addEventListener('click', (e) => {
                // Don't trigger if clicking on star button, action buttons, or forms
                if (!e.target.closest('.star-btn') && 
                    !e.target.closest('.action-btn') && 
                    !e.target.closest('form')) {
                    window.location.href = viewLink.href;
                }
            });
        }
    });

    // Toggle star functionality
    function toggleStar(emailId, button, isStarred) {
        fetch(`/admin/mail/${emailId}/star`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ star: !isStarred }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.is_starred === false) {
                // Remove from starred list if unstarred
                const row = button.closest('.email-row');
                row.style.opacity = '0.5';
                setTimeout(() => {
                    row.remove();
                    
                    // Show empty state if no emails left
                    const remainingRows = document.querySelectorAll('.email-row').length;
                    if (remainingRows === 0) {
                        location.reload(); // Reload to show empty state
                    }
                }, 200);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Attach star button click handlers
    document.querySelectorAll('.star-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const emailId = btn.dataset.id;
            const isStarred = btn.dataset.starred === 'true';
            toggleStar(emailId, btn, isStarred);
        });
    });
</script>
@endsection