@extends('layouts.admin')

@section('title', $email->subject ?: 'No Subject')

@section('content')
    <div class="email-view-container">
        <!-- Toolbar -->
        <div class="email-toolbar">
            <div class="toolbar-left">
                <a href="{{ route('admin.mail.inbox') }}" class="btn-icon" title="Back to Inbox">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                @if($email->type !== 'sent')
                    <a href="{{ route('admin.mail.reply', $email) }}" class="btn-icon" title="Reply">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </a>
                @endif
                <button class="btn-icon {{ $email->is_starred ? 'active' : '' }}" onclick="toggleStar('{{ $email->id }}')" id="star-btn" type="button" title="Star">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $email->is_starred ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </button>
                <form action="{{ route('admin.mail.destroy', $email) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this email?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-icon" title="Delete">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Email Content -->
        <div class="email-content-wrapper">
            <h1 class="email-view-subject">{{ $email->subject ?: '(No Subject)' }}</h1>
            
            <!-- Sender Info Card -->
            <div class="email-sender-card">
                <div class="sender-avatar">
                    {{ strtoupper(substr($email->type === 'sent' ? $email->to_name : $email->from_name, 0, 1)) }}
                </div>
                <div class="sender-details">
                    <div class="sender-line">
                        <span class="sender-name">{{ $email->type === 'sent' ? $email->to_name : $email->from_name }}</span>
                        <span class="sender-email">&lt;{{ $email->type === 'sent' ? $email->to_email : $email->from_email }}&gt;</span>
                    </div>
                    <div class="recipient-line">
                        <span class="to-label">to</span>
                        <span class="to-email">{{ $email->type === 'sent' ? $email->from_email : $email->to_email }}</span>
                    </div>
                </div>
                <div class="email-time">
                    {{ $email->sent_at?->format('M j, Y, g:i A') ?? 'Draft' }}
                </div>
            </div>

            <!-- Email Body -->
            <div class="email-view-body">
                {!! nl2br(e($email->body)) !!}
            </div>

            <!-- Attachments -->
            @if($email->attachments && count($email->attachments) > 0)
                <div class="email-view-attachments">
                    <div class="attachments-title">{{ count($email->attachments) }} Attachment{{ count($email->attachments) > 1 ? 's' : '' }}</div>
                    <div class="attachments-grid">
                        @foreach($email->attachments as $attachment)
                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="attachment-card">
                                <div class="attachment-icon-file">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                        <polyline points="13 2 13 9 20 9"></polyline>
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

            <!-- Reply Actions -->
            @if($email->type !== 'sent')
                <div class="email-reply-actions">
                    <a href="{{ route('admin.mail.reply', $email) }}" class="btn-reply">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 10h10a8 8 0 0 1 8 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Reply
                    </a>
                </div>
            @endif
        </div>

        <!-- Linked Inquiry -->
        @if(isset($linkedInquiry) && $linkedInquiry)
            <div class="linked-inquiry-section">
                <div class="section-header">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                    </svg>
                    Linked to Inquiry
                </div>
                <a href="{{ route('admin.inquiries.show', $linkedInquiry) }}" class="inquiry-link-card">
                    <div class="inquiry-number-badge">{{ $linkedInquiry->inquiry_number }}</div>
                    <div class="inquiry-details">
                        <div class="inquiry-person">{{ $linkedInquiry->name }}</div>
                        <div class="inquiry-company">{{ $linkedInquiry->business_name }}</div>
                    </div>
                    <span class="inquiry-status-badge {{ $linkedInquiry->status }}">{{ $linkedInquiry->status }}</span>
                </a>
            </div>
        @endif

        <!-- Conversation Thread -->
        @if(isset($conversation) && $conversation->count() > 0)
            <div class="conversation-section">
                <div class="section-header">💬 Conversation</div>
                <div class="conversation-list">
                    @foreach($conversation as $threadEmail)
                        <a href="{{ route('admin.mail.show', $threadEmail) }}" class="conversation-item {{ $threadEmail->id === $email->id ? 'current' : '' }}">
                            <div class="conv-avatar">{{ strtoupper(substr($threadEmail->from_name, 0, 1)) }}</div>
                            <div class="conv-content">
                                <div class="conv-header">
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
    </div>

    <style>
        .email-view-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1.5rem 1.5rem;
        }

        .email-toolbar {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 1.5rem;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }

        .btn-icon:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }

        .btn-icon.active {
            color: #fbbf24;
        }

        .email-content-wrapper {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }

        .email-view-subject {
            font-size: 1.375rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }

        .email-sender-card {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 1.5rem;
        }

        .sender-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sender-details {
            flex: 1;
            min-width: 0;
        }

        .sender-line {
            margin-bottom: 0.25rem;
        }

        .sender-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .sender-email {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .recipient-line {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .to-label {
            color: var(--text-muted);
            margin-right: 0.25rem;
        }

        .to-email {
            color: var(--text-secondary);
        }

        .email-time {
            font-size: 0.8125rem;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .email-view-body {
            font-size: 0.9375rem;
            line-height: 1.7;
            color: var(--text-secondary);
            white-space: pre-wrap;
            margin-bottom: 1.5rem;
        }

        .email-view-attachments {
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-subtle);
            margin-bottom: 1.5rem;
        }

        .attachments-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
        }

        .attachments-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .attachment-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.15s;
            border: 1px solid var(--border-subtle);
        }

        .attachment-card:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-primary);
        }

        .attachment-icon-file {
            color: var(--text-muted);
        }

        .attachment-name-file {
            font-size: 0.875rem;
            font-weight: 500;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .attachment-size-file {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .email-reply-actions {
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-subtle);
        }

        .btn-reply {
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
        }

        .btn-reply:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .linked-inquiry-section,
        .conversation-section {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .inquiry-link-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.15s;
        }

        .inquiry-link-card:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .inquiry-number-badge {
            font-family: monospace;
            font-size: 0.75rem;
            color: var(--accent-primary);
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            background: rgba(79, 70, 229, 0.1);
            border-radius: var(--radius-sm);
        }

        .inquiry-details {
            flex: 1;
        }

        .inquiry-person {
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .inquiry-company {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .inquiry-status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .inquiry-status-badge.new {
            background: rgba(248, 113, 113, 0.2);
            color: #f87171;
        }

        .inquiry-status-badge.in_progress {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }

        .inquiry-status-badge.resolved {
            background: rgba(52, 211, 153, 0.2);
            color: #34d399;
        }

        .conversation-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .conversation-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.875rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.15s;
        }

        .conversation-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .conversation-item.current {
            background: rgba(79, 70, 229, 0.1);
            border: 1px solid var(--accent-primary);
        }

        .conv-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .conv-content {
            flex: 1;
            min-width: 0;
        }

        .conv-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .conv-from {
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .conv-date {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .conv-subject {
            font-size: 0.8125rem;
            color: var(--text-secondary);
            margin-bottom: 0.125rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conv-preview {
            font-size: 0.75rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {
            .email-content-wrapper {
                padding: 1rem;
            }

            .email-sender-card {
                flex-wrap: wrap;
            }

            .email-time {
                width: 100%;
                margin-top: 0.5rem;
            }
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
            .then(response => response.json())
            .then(data => {
                const btn = document.getElementById('star-btn');
                const svg = btn.querySelector('svg');
                svg.setAttribute('fill', data.is_starred ? 'currentColor' : 'none');
                btn.classList.toggle('active', data.is_starred);
            })
            .catch(err => console.error('Error:', err));
        }
    </script>
@endsection
