@extends('layouts.admin')

@section('title', $email->subject ?: 'No Subject')

@section('content')
    <div class="email-detail-container">
        <!-- Email Header -->
        <div class="email-header">
            <div class="email-header-left">
                <a href="{{ route('admin.mail.inbox') }}" class="btn-back">
                    ← Back to Inbox
                </a>
                <h1 class="email-subject">{{ $email->subject ?: '(No Subject)' }}</h1>
                
                <div class="email-meta">
                    @if($email->type === 'sent')
                        <div class="email-participant">
                            <span class="label">To:</span>
                            <span class="name">{{ $email->to_name ?? 'Unknown' }}</span>
                            <span class="email">&lt;{{ $email->to_email }}&gt;</span>
                        </div>
                        <div class="email-participant" style="margin-top: 0.5rem;">
                            <span class="label">From:</span>
                            <span class="name">{{ $email->from_name }}</span>
                            <span class="email">&lt;{{ $email->from_email }}&gt;</span>
                        </div>
                    @else
                        <div class="email-participant">
                            <span class="label">From:</span>
                            <span class="name">{{ $email->from_name }}</span>
                            <span class="email">&lt;{{ $email->from_email }}&gt;</span>
                        </div>
                        <div class="email-participant" style="margin-top: 0.5rem;">
                            <span class="label">To:</span>
                            <span class="name">{{ $email->to_name ?? 'Me' }}</span>
                            <span class="email">&lt;{{ $email->to_email }}&gt;</span>
                        </div>
                    @endif
                    
                    @if($email->cc)
                        <div class="email-cc">
                            <span class="label">CC:</span> {{ $email->cc }}
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="email-header-right">
                <div class="email-date">
                    {{ $email->sent_at?->format('F j, Y \a\t g:i A') ?? 'Draft' }}
                </div>
                <div class="email-actions">
                    @if($email->type !== 'sent')
                        <a href="{{ route('admin.mail.reply', $email) }}" class="btn-primary">↩️ Reply</a>
                    @endif
                    <button class="btn-secondary" onclick="toggleStar('{{ $email->id }}')" id="star-btn" type="button">
                        {{ $email->is_starred ? '⭐ Starred' : '☆ Star' }}
                    </button>
                    <form action="{{ route('admin.mail.destroy', $email) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this email?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary">🗑️ Delete</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Email Body -->
        <div class="email-body-card">
            <div class="email-body-content">
                {!! nl2br(e($email->body)) !!}
            </div>
            
            @if($email->attachments && count($email->attachments) > 0)
                <div class="email-attachments">
                    <div class="attachments-header">
                        📎 {{ count($email->attachments) }} Attachment{{ count($email->attachments) > 1 ? 's' : '' }}
                    </div>
                    <div class="attachments-list">
                        @foreach($email->attachments as $attachment)
                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="attachment-item">
                                <span class="attachment-icon">📄</span>
                                <span class="attachment-name">{{ $attachment['name'] }}</span>
                                <span class="attachment-size">({{ number_format($attachment['size'] / 1024, 1) }} KB)</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Reply Form -->
        @if($email->type !== 'sent')
            <div id="reply-form" class="reply-form-container" style="display: none;">
                <div class="reply-header">
                    <h3>↩️ Reply to {{ $email->from_name }}</h3>
                </div>
                <form action="{{ route('admin.mail.reply', $email) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>To</label>
                        <input type="text" value="{{ $email->from_name }} <{{ $email->from_email }}>" disabled class="form-control">
                        <input type="hidden" name="to_email" value="{{ $email->from_email }}">
                        <input type="hidden" name="to_name" value="{{ $email->from_name }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" value="Re: {{ $email->subject }}" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="body" rows="8" class="form-control" placeholder="Type your reply here..." required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Attachments</label>
                        <input type="file" name="attachments[]" multiple class="form-control">
                        <small style="color: var(--text-muted);">Max 10MB per file</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">📤 Send Reply</button>
                        <button type="button" class="btn-secondary" onclick="toggleReplyForm()">Cancel</button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Conversation Thread -->
        @if(isset($conversation) && $conversation->count() > 1)
            <div class="conversation-thread">
                <h3>💬 Conversation</h3>
                @foreach($conversation as $threadEmail)
                    @if($threadEmail->id !== $email->id)
                        <a href="{{ route('admin.mail.show', $threadEmail) }}" class="thread-item {{ $threadEmail->id === $email->id ? 'active' : '' }}">
                            <div class="thread-avatar">
                                {{ substr($threadEmail->from_name, 0, 1) }}
                            </div>
                            <div class="thread-content">
                                <div class="thread-header">
                                    <span class="thread-from">{{ $threadEmail->from_name }}</span>
                                    <span class="thread-date">{{ $threadEmail->sent_at?->format('M j, g:i A') }}</span>
                                </div>
                                <div class="thread-subject">{{ $threadEmail->subject }}</div>
                                <div class="thread-preview">{{ $threadEmail->getExcerpt(80) }}</div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Linked Inquiry -->
        @if(isset($linkedInquiry) && $linkedInquiry)
            <div class="linked-inquiry">
                <h3>🔗 Linked Inquiry</h3>
                <a href="{{ route('admin.inquiries.show', $linkedInquiry) }}" class="inquiry-card">
                    <div class="inquiry-number">{{ $linkedInquiry->inquiry_number }}</div>
                    <div class="inquiry-info">
                        <div class="inquiry-name">{{ $linkedInquiry->name }}</div>
                        <div class="inquiry-business">{{ $linkedInquiry->business_name }}</div>
                    </div>
                    <span class="inquiry-status {{ $linkedInquiry->status }}">{{ $linkedInquiry->status }}</span>
                </a>
            </div>
        @endif
    </div>

    <style>
        .email-detail-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .email-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .email-header-left {
            flex: 1;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            transition: color 0.2s;
        }

        .btn-back:hover {
            color: var(--text-primary);
        }

        .email-subject {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .email-meta {
            font-size: 0.875rem;
        }

        .email-participant {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .email-participant .label {
            color: var(--text-muted);
            width: 50px;
        }

        .email-participant .name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .email-participant .email {
            color: var(--text-muted);
        }

        .email-cc {
            margin-top: 0.5rem;
            color: var(--text-muted);
        }

        .email-cc .label {
            color: var(--text-muted);
            width: 50px;
            display: inline-block;
        }

        .email-header-right {
            text-align: right;
        }

        .email-date {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .email-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .email-body-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .email-body-content {
            padding: 2rem;
            line-height: 1.8;
            color: var(--text-secondary);
            font-size: 0.9375rem;
        }

        .email-attachments {
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border-subtle);
            background: rgba(255, 255, 255, 0.02);
        }

        .attachments-header {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
        }

        .attachments-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .attachment-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .attachment-icon {
            font-size: 1.25rem;
        }

        .attachment-name {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .attachment-size {
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        .reply-form-container {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .reply-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .reply-header h3 {
            font-size: 1rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-primary);
        }

        .form-control:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .conversation-thread {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .conversation-thread h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        .thread-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.2s;
            margin-bottom: 0.5rem;
        }

        .thread-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .thread-item.active {
            background: rgba(79, 70, 229, 0.1);
            border: 1px solid var(--accent-primary);
        }

        .thread-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }

        .thread-content {
            flex: 1;
            min-width: 0;
        }

        .thread-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .thread-from {
            font-weight: 500;
            color: var(--text-primary);
        }

        .thread-date {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .thread-subject {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .thread-preview {
            font-size: 0.8125rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .linked-inquiry {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            padding: 1.5rem;
        }

        .linked-inquiry h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        .inquiry-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.2s;
        }

        .inquiry-card:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .inquiry-number {
            font-family: monospace;
            font-size: 0.875rem;
            color: var(--accent-primary);
            font-weight: 600;
        }

        .inquiry-info {
            flex: 1;
        }

        .inquiry-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .inquiry-business {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        .inquiry-status {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .inquiry-status.new {
            background: rgba(248, 113, 113, 0.2);
            color: #f87171;
        }

        .inquiry-status.in_progress {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }

        .inquiry-status.resolved {
            background: rgba(52, 211, 153, 0.2);
            color: #34d399;
        }

        @media (max-width: 768px) {
            .email-header {
                flex-direction: column;
                gap: 1rem;
            }

            .email-header-right {
                text-align: left;
            }

            .email-actions {
                justify-content: flex-start;
            }
        }
    </style>

    <script>
        function toggleReplyForm() {
            const form = document.getElementById('reply-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function toggleStar(emailId) {
            fetch(`/admin/mail/${emailId}/star`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
            .then(response => response.json())
            .then(data => {
                const btn = document.getElementById('star-btn');
                btn.textContent = data.is_starred ? '⭐ Starred' : '☆ Star';
            });
        }
    </script>
@endsection
