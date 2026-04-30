@extends('layouts.admin')

@section('title', 'Sent Mail')

@section('content')
    <div class="page-header">
        <h1>
            <small>Sent Emails</small>
            Sent
        </h1>
        <a href="{{ route('admin.mail.compose') }}" class="btn-primary">✉️ Compose</a>
    </div>

    <div class="mail-container">
        <div class="mail-sidebar">
            <a href="{{ route('admin.mail.inbox') }}" class="mail-nav-item">
                <span>📧</span> Inbox
            </a>
            <a href="{{ route('admin.mail.starred') }}" class="mail-nav-item">
                <span>⭐</span> Starred
            </a>
            <a href="{{ route('admin.mail.sent') }}" class="mail-nav-item active">
                <span>📤</span> Sent
            </a>
            <a href="{{ route('admin.mail.drafts') }}" class="mail-nav-item">
                <span>📝</span> Drafts
            </a>
        </div>

        <div class="mail-content">
            @if($emails->count() > 0)
                <div class="email-list">
                    @foreach($emails as $email)
                        <a href="{{ route('admin.mail.show', $email) }}" class="email-item">
                            <div class="email-sender">To: {{ $email->to_email }}</div>
                            <div class="email-subject">
                                {{ $email->subject }}
                                <span class="email-excerpt"> - {{ $email->getExcerpt(50) }}</span>
                            </div>
                            <div class="email-date">{{ $email->sent_at->format('M d, Y') }}</div>
                        </a>
                    @endforeach
                </div>
                <div style="padding: 1rem;">
                    {{ $emails->links() }}
                </div>
            @else
                <div class="empty-state" style="padding: 3rem;">
                    <div class="empty-state-icon">📤</div>
                    <div>No sent emails</div>
                    <a href="{{ route('admin.mail.compose') }}" class="btn-primary" style="margin-top: 1rem;">Send Your First Email</a>
                </div>
            @endif
        </div>
    </div>

    <style>
        .mail-container {
            display: flex;
            gap: 1rem;
            height: calc(100vh - 200px);
        }

        .mail-sidebar {
            width: 200px;
            flex-shrink: 0;
        }

        .mail-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
        }

        .mail-nav-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }

        .mail-nav-item.active {
            background: rgba(79, 70, 229, 0.1);
            color: var(--accent-primary);
        }

        .mail-content {
            flex: 1;
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .email-list {
            display: flex;
            flex-direction: column;
        }

        .email-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
        }

        .email-item:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .email-sender {
            width: 200px;
            flex-shrink: 0;
        }

        .email-subject {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .email-excerpt {
            color: var(--text-muted);
        }

        .email-date {
            width: 100px;
            text-align: right;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .mail-sidebar {
                display: none;
            }
        }
    </style>
@endsection
