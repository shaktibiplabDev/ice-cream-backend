@extends('layouts.admin')

@section('title', 'Reply to ' . $email->from_name)

@section('content')
    <div class="reply-container">
        <div class="page-header">
            <h1>
                <small>Reply to email from</small>
                {{ $email->from_name }}
            </h1>
        </div>

        <!-- Original Email Summary -->
        <div class="original-email-card">
            <div class="original-header">
                <div class="original-subject">{{ $email->subject }}</div>
                <div class="original-meta">
                    From: {{ $email->from_name }} &lt;{{ $email->from_email }}&gt;
                    · {{ $email->sent_at?->format('M j, Y g:i A') }}
                </div>
            </div>
            <div class="original-preview">
                {{ $email->getExcerpt(200) }}
            </div>
        </div>

        <!-- Linked Inquiry -->
        @if($linkedInquiry)
            <div class="linked-inquiry-alert">
                <div class="alert-icon">🔗</div>
                <div class="alert-content">
                    <div class="alert-title">Linked to Inquiry</div>
                    <div class="alert-text">
                        This email is from {{ $linkedInquiry->name }} ({{ $linkedInquiry->inquiry_number }}).
                        Your reply will be added to the inquiry conversation.
                    </div>
                    <a href="{{ route('admin.inquiries.show', $linkedInquiry) }}" class="alert-link">
                        View Inquiry →
                    </a>
                </div>
            </div>
        @endif

        <!-- Reply Form -->
        <div class="reply-form-card">
            <form action="{{ route('admin.mail.reply.send', $email) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-section">
                    <label class="form-label">To</label>
                    <div class="recipient-display">
                        <div class="recipient-avatar">{{ substr($email->from_name, 0, 1) }}</div>
                        <div class="recipient-info">
                            <div class="recipient-name">{{ $email->from_name }}</div>
                            <div class="recipient-email">{{ $email->from_email }}</div>
                        </div>
                    </div>
                </div>

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

                <div class="form-section">
                    <label class="form-label" for="body">Message</label>
                    <textarea id="body" 
                              name="body" 
                              rows="10" 
                              class="form-control @error('body') is-invalid @enderror"
                              placeholder="Type your reply here..."
                              required></textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-section">
                    <label class="form-label">Attachments</label>
                    <div class="file-upload-area">
                        <input type="file" 
                               name="attachments[]" 
                               multiple 
                               class="file-input"
                               id="attachments"
                               accept="*/*">
                        <label for="attachments" class="file-upload-label">
                            <span class="upload-icon">📎</span>
                            <span class="upload-text">Drop files here or click to upload</span>
                            <span class="upload-hint">Max 10MB per file</span>
                        </label>
                    </div>
                    <div id="file-list" class="file-list"></div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.mail.show', $email) }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">📤 Send Reply</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .reply-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .original-email-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .original-header {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .original-subject {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .original-meta {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        .original-preview {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .linked-inquiry-alert {
            display: flex;
            gap: 1rem;
            background: rgba(79, 70, 229, 0.1);
            border: 1px solid rgba(79, 70, 229, 0.3);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .alert-text {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.75rem;
        }

        .alert-link {
            font-size: 0.875rem;
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 500;
        }

        .alert-link:hover {
            text-decoration: underline;
        }

        .reply-form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 2rem;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .recipient-display {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
        }

        .recipient-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 0.875rem;
        }

        .recipient-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .recipient-email {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        .file-upload-area {
            position: relative;
            border: 2px dashed var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 2rem;
            text-align: center;
            transition: all 0.2s;
        }

        .file-upload-area:hover {
            border-color: var(--accent-primary);
            background: rgba(79, 70, 229, 0.05);
        }

        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .upload-icon {
            font-size: 1.5rem;
        }

        .upload-text {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .upload-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .file-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .file-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            font-size: 0.8125rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-subtle);
        }

        .is-invalid {
            border-color: #f87171;
        }

        .invalid-feedback {
            color: #f87171;
            font-size: 0.8125rem;
            margin-top: 0.25rem;
        }
    </style>

    <script>
        document.getElementById('attachments').addEventListener('change', function(e) {
            const fileList = document.getElementById('file-list');
            fileList.innerHTML = '';
            
            Array.from(e.target.files).forEach(file => {
                const div = document.createElement('div');
                div.className = 'file-item';
                div.innerHTML = `
                    <span>📎</span>
                    <span>${file.name}</span>
                    <span style="color: var(--text-muted);">(${(file.size / 1024).toFixed(1)} KB)</span>
                `;
                fileList.appendChild(div);
            });
        });
    </script>
@endsection
