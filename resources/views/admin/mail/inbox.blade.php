@extends('layouts.admin')

@section('title', 'Inbox')

@section('content')
    <div class="gmail-container">
        <!-- Toolbar -->
        <div class="gmail-toolbar">
            <div class="toolbar-left">
                <a href="{{ route('admin.mail.compose') }}" class="btn-compose-gmail">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Compose
                </a>
            </div>
            <div class="toolbar-center">
                <span class="emails-count">{{ $unreadCount }} new</span>
            </div>
            <div class="toolbar-right">
                {{ $emails->links('pagination::simple-tailwind') }}
            </div>
        </div>

        <div class="gmail-layout">
            <!-- Sidebar -->
            <aside class="gmail-sidebar">
                <nav class="sidebar-nav">
                    <a href="{{ route('admin.mail.inbox') }}" class="nav-item {{ request()->routeIs('admin.mail.inbox') ? 'active' : '' }}">
                        <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <span class="nav-label">Inbox</span>
                        @if($unreadCount > 0)
                            <span class="nav-count">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    
                    <a href="{{ route('admin.mail.starred') }}" class="nav-item {{ request()->routeIs('admin.mail.starred') ? 'active' : '' }}">
                        <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                        </svg>
                        <span class="nav-label">Starred</span>
                    </a>
                    
                    <a href="{{ route('admin.mail.sent') }}" class="nav-item {{ request()->routeIs('admin.mail.sent') ? 'active' : '' }}">
                        <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                        <span class="nav-label">Sent</span>
                    </a>
                    
                    <a href="{{ route('admin.mail.drafts') }}" class="nav-item {{ request()->routeIs('admin.mail.drafts') ? 'active' : '' }}">
                        <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                        <span class="nav-label">Drafts</span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="gmail-main">
                @if($emails->count() > 0)
                    <div class="email-table">
                        <div class="email-table-header">
                            <div class="col-checkbox">
                                <input type="checkbox" class="checkbox-all">
                            </div>
                            <div class="col-star"></div>
                            <div class="col-sender">From</div>
                            <div class="col-content">Subject</div>
                            <div class="col-date">Date</div>
                        </div>

                        <div class="email-list-gmail">
                            @foreach($emails as $email)
                                <a href="{{ route('admin.mail.show', $email) }}" class="email-row-gmail {{ !$email->is_read ? 'unread' : '' }}">
                                    <div class="col-checkbox" onclick="event.stopPropagation()">
                                        <input type="checkbox" class="email-checkbox">
                                    </div>
                                    <div class="col-star" onclick="event.stopPropagation(); toggleStar(this, '{{ $email->id }}')" role="button">
                                        <svg class="star-icon {{ $email->is_starred ? 'starred' : '' }}" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                        </svg>
                                    </div>
                                    <div class="col-sender">
                                        <span class="sender-text">{{ $email->from_name }}</span>
                                    </div>
                                    <div class="col-content">
                                        <span class="subject-text">{{ $email->subject ?: '(No Subject)' }}</span>
                                        <span class="body-preview">{{ $email->getExcerpt(60) }}</span>
                                    </div>
                                    <div class="col-date">
                                        <span class="date-text">{{ $email->sent_at?->format('M j') ?? 'Draft' }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="pagination-gmail">
                        {{ $emails->links() }}
                    </div>
                @else
                    <div class="empty-gmail">
                        <div class="empty-icon-gmail">
                            <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <h3>No messages to show</h3>
                        <p>Your inbox is empty. When you receive emails, they will appear here.</p>
                        <a href="{{ route('admin.mail.compose') }}" class="btn-compose-gmail" style="margin-top: 1.5rem;">
                            Send an email
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <style>
        .gmail-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 140px);
            background: var(--bg-card);
            border-radius: 8px;
            overflow: hidden;
        }

        .gmail-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            border-bottom: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.02);
        }

        .toolbar-left {
            min-width: 180px;
        }

        .btn-compose-gmail {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            background: #c2e7ff;
            color: #001d35;
            text-decoration: none;
            border-radius: 16px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            border: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .btn-compose-gmail:hover {
            background: #b4dbfa;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .btn-compose-gmail svg {
            color: #001d35;
        }

        .toolbar-center {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
        }

        .emails-count {
            font-size: 14px;
            color: var(--text-muted);
        }

        .toolbar-right {
            min-width: 180px;
            display: flex;
            justify-content: flex-end;
        }

        .gmail-layout {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .gmail-sidebar {
            width: 256px;
            flex-shrink: 0;
            padding: 8px 12px;
            border-right: 1px solid var(--border-subtle);
            overflow-y: auto;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 8px 16px;
            border-radius: 0 16px 16px 0;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.15s;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item.active {
            background: rgba(79, 70, 229, 0.2);
            color: var(--accent-primary);
            font-weight: 500;
        }

        .nav-icon {
            flex-shrink: 0;
            opacity: 0.7;
        }

        .nav-label {
            flex: 1;
        }

        .nav-count {
            background: var(--accent-primary);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .gmail-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .email-table {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .email-table-header {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            border-bottom: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.02);
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .email-list-gmail {
            flex: 1;
            overflow-y: auto;
        }

        .email-row-gmail {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            border-bottom: 1px solid var(--border-subtle);
            cursor: pointer;
            font-size: 14px;
            transition: all 0.15s;
            color: var(--text-secondary);
            text-decoration: none;
        }

        a.email-row-gmail {
            color: inherit;
        }

        .email-row-gmail:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .email-row-gmail.unread {
            background: rgba(79, 70, 229, 0.08);
        }

        .email-row-gmail.unread .sender-text,
        .email-row-gmail.unread .subject-text {
            font-weight: 600;
            color: var(--text-primary);
        }

        .col-checkbox {
            width: 32px;
            flex-shrink: 0;
        }

        .email-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--accent-primary);
        }

        .col-star {
            width: 36px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .star-icon {
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.15s;
            opacity: 0.5;
        }

        .star-icon:hover {
            opacity: 1;
            color: #fbbc04;
        }

        .star-icon.starred {
            color: #fbbc04;
            opacity: 1;
        }

        .col-sender {
            width: 200px;
            flex-shrink: 0;
            padding-right: 16px;
        }

        .sender-text {
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            font-weight: 500;
        }

        .col-content {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .subject-text {
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .body-preview {
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            margin-left: 8px;
        }

        .body-preview::before {
            content: "—";
            margin-right: 8px;
            color: var(--text-muted);
        }

        .col-date {
            width: 80px;
            flex-shrink: 0;
            text-align: right;
        }

        .date-text {
            color: var(--text-muted);
            font-size: 13px;
        }

        .pagination-gmail {
            padding: 12px 24px;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            justify-content: center;
        }

        .empty-gmail {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 48px;
        }

        .empty-icon-gmail {
            color: var(--text-muted);
            opacity: 0.3;
            margin-bottom: 24px;
        }

        .empty-gmail h3 {
            font-size: 20px;
            font-weight: 400;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .empty-gmail p {
            font-size: 14px;
            color: var(--text-muted);
            max-width: 400px;
        }

        @media (max-width: 768px) {
            .gmail-sidebar {
                display: none;
            }
            
            .col-sender {
                width: 140px;
            }
            
            .body-preview {
                display: none;
            }
        }
    </style>

    <script>
        function toggleStar(el, emailId) {
            fetch(`/admin/mail/${emailId}/star`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                el.querySelector('.star-icon').classList.toggle('starred', data.is_starred);
            })
            .catch(err => console.error('Error:', err));
        }
    </script>
@endsection
