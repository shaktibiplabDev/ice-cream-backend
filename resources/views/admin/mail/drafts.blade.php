@extends('layouts.admin')

@section('title', 'Drafts')

@section('content')
    <div class="page-header">
        <h1>
            <small>Draft Emails</small>
            Drafts
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
            <a href="{{ route('admin.mail.sent') }}" class="mail-nav-item">
                <span>📤</span> Sent
            </a>
            <a href="{{ route('admin.mail.drafts') }}" class="mail-nav-item active">
                <span>📝</span> Drafts
            </a>
        </div>

        <div class="mail-content">
            @if($emails->count() > 0)
                <div class="email-list">
                    @foreach($emails as $email)
                        <div class="email-item">
                            <div class="email-sender">{{ $email->to_email ?? 'No recipient' }}</div>
                            <div class="email-subject">
                                {{ $email->subject ?? '(No subject)' }}
                                <span class="email-excerpt"> - {{ $email->getExcerpt(50) }}</span>
                            </div>
                            <div class="email-date">{{ $email->created_at->format('M d') }}</div>
                            <div class="email-actions">
                                <a href="{{ route('admin.mail.compose') }}?draft={{ $email->id }}" class="action-btn action-edit">Edit</a>
                                <form action="{{ route('admin.mail.destroy', $email) }}" method="POST" class="inline" onsubmit="return confirm('Delete this draft?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div style="padding: 1rem;">
                    {{ $emails->links() }}
                </div>
            @else
                <div class="empty-state" style="padding: 3rem;">
                    <div class="empty-state-icon">📝</div>
                    <div>No drafts</div>
                    <a href="{{ route('admin.mail.compose') }}" class="btn-primary" style="margin-top: 1rem;">Compose Email</a>
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
            width: 80px;
            text-align: right;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .email-actions {
            display: flex;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .mail-sidebar {
                display: none;
            }
        }
    </style>
@endsection
