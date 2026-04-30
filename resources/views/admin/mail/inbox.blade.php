@extends('layouts.admin')

@section('title', 'Inbox')

@section('content')
    <div class="mail-app">
        <!-- Toolbar -->
        <div class="mail-toolbar">
            <div class="toolbar-left">
                <a href="{{ route('admin.mail.compose') }}" class="btn-compose">
                    <span>✉️</span> Compose
                </a>
            </div>
            <div class="toolbar-right">
                <span class="unread-count">{{ $unreadCount }} unread</span>
            </div>
        </div>

        <div class="mail-layout">
            <!-- Sidebar -->
            <div class="mail-sidebar">
                <a href="{{ route('admin.mail.inbox') }}" class="nav-item active">
                    <span class="nav-icon">📧</span>
                    <span class="nav-label">Inbox</span>
                    @if($unreadCount > 0)
                        <span class="nav-badge">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.mail.starred') }}" class="nav-item">
                    <span class="nav-icon">⭐</span>
                    <span class="nav-label">Starred</span>
                </a>
                <a href="{{ route('admin.mail.sent') }}" class="nav-item">
                    <span class="nav-icon">📤</span>
                    <span class="nav-label">Sent</span>
                </a>
                <a href="{{ route('admin.mail.drafts') }}" class="nav-item">
                    <span class="nav-icon">📝</span>
                    <span class="nav-label">Drafts</span>
                </a>
            </div>

            <!-- Email List -->
            <div class="email-list-container">
                @if($emails->count() > 0)
                    <div class="email-list">
                        @foreach($emails as $email)
                            <div class="email-row {{ !$email->is_read ? 'unread' : '' }}">
                                <div class="row-actions">
                                    <button class="star-btn" onclick="toggleStar('{{ $email->id }}', this)" data-id="{{ $email->id }}">
                                        {{ $email->is_starred ? '⭐' : '☆' }}
                                    </button>
                                </div>
                                <a href="{{ route('admin.mail.show', $email) }}" class="row-content">
                                    <div class="sender-col">
                                        <span class="sender-name">{{ $email->from_name }}</span>
                                    </div>
                                    <div class="message-col">
                                        <span class="subject">{{ $email->subject ?: '(No Subject)' }}</span>
                                        <span class="preview"> — {{ $email->getExcerpt(80) }}</span>
                                    </div>
                                    <div class="date-col">
                                        <span class="date">{{ $email->sent_at?->format('M j') ?? 'Draft' }}</span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="pagination-container">
                        {{ $emails->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">📧</div>
                        <h3>Your inbox is empty</h3>
                        <p>No emails to show. When emails arrive, they'll appear here.</p>
                        <a href="{{ route('admin.mail.compose') }}" class="btn-compose" style="margin-top: 1rem;">Send Email</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .mail-app {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 140px);
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            overflow: hidden;
        }

        .mail-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.02);
        }

        .btn-compose {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: var(--accent-gradient);
            color: white;
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-compose:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .unread-count {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .mail-layout {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .mail-sidebar {
            width: 220px;
            flex-shrink: 0;
            padding: 1rem;
            border-right: 1px solid var(--border-subtle);
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.15s;
            margin-bottom: 0.25rem;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }

        .nav-item.active {
            background: rgba(79, 70, 229, 0.15);
            color: var(--accent-primary);
            font-weight: 500;
        }

        .nav-icon {
            font-size: 1.125rem;
            width: 24px;
            text-align: center;
        }

        .nav-label {
            flex: 1;
            font-size: 0.875rem;
        }

        .nav-badge {
            background: var(--accent-primary);
            color: white;
            padding: 0.125rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .email-list-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .email-list {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .email-row {
            display: flex;
            align-items: stretch;
            border-bottom: 1px solid var(--border-subtle);
            transition: all 0.15s;
            cursor: pointer;
        }

        .email-row:hover {
            background: rgba(255, 255, 255, 0.03);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .email-row.unread {
            background: rgba(79, 70, 229, 0.04);
        }

        .email-row.unread .sender-name,
        .email-row.unread .subject {
            font-weight: 600;
            color: var(--text-primary);
        }

        .row-actions {
            display: flex;
            align-items: center;
            padding: 0.75rem 0.5rem;
            flex-shrink: 0;
        }

        .star-btn {
            background: none;
            border: none;
            font-size: 1.125rem;
            cursor: pointer;
            padding: 0.25rem;
            color: #fbbf24;
            opacity: 0.6;
            transition: all 0.15s;
        }

        .star-btn:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        .row-content {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            text-decoration: none;
            color: inherit;
            gap: 1rem;
        }

        .sender-col {
            width: 180px;
            flex-shrink: 0;
        }

        .sender-name {
            font-size: 0.875rem;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .message-col {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .subject {
            font-size: 0.875rem;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .preview {
            font-size: 0.8125rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        .date-col {
            width: 70px;
            flex-shrink: 0;
            text-align: right;
        }

        .date {
            font-size: 0.75rem;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .pagination-container {
            padding: 0.75rem 1rem;
            border-top: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.02);
        }

        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 3rem;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            font-size: 0.875rem;
            color: var(--text-muted);
            max-width: 300px;
        }

        @media (max-width: 768px) {
            .mail-sidebar {
                display: none;
            }

            .sender-col {
                width: 120px;
            }

            .preview {
                display: none;
            }
        }
    </style>

    <script>
        function toggleStar(emailId, btn) {
            event.preventDefault();
            event.stopPropagation();
            
            fetch(`/admin/mail/${emailId}/star`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                btn.textContent = data.is_starred ? '⭐' : '☆';
                btn.style.opacity = data.is_starred ? '1' : '0.6';
            })
            .catch(err => console.error('Error:', err));
        }
    </script>
@endsection
