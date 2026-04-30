@extends('layouts.admin')

@section('title', 'Compose Email')

@section('content')
    <div class="compose-container">
        <!-- Toolbar -->
        <div class="compose-toolbar">
            <a href="{{ route('admin.mail.inbox') }}" class="btn-icon" title="Back">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <span class="toolbar-title">New Message</span>
        </div>

        <!-- Compose Form -->
        <div class="compose-form-wrapper">
            <form method="POST" action="{{ route('admin.mail.store') }}" enctype="multipart/form-data" class="compose-form">
                @csrf
                
                <div class="form-row">
                    <label class="form-label">From</label>
                    <div class="sender-display">
                        {{ $settings->company_name }} &lt;{{ $settings->email }}&gt;
                    </div>
                </div>

                <div class="form-row">
                    <label class="form-label" for="to_email">To</label>
                    <input type="email" name="to_email" id="to_email" value="{{ old('to_email') }}" required
                        class="form-input @error('to_email') is-invalid @enderror"
                        placeholder="recipient@example.com">
                    @error('to_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <label class="form-label" for="cc">Cc</label>
                    <input type="text" name="cc" id="cc" value="{{ old('cc') }}"
                        class="form-input"
                        placeholder="cc1@example.com, cc2@example.com">
                </div>

                <div class="form-row">
                    <label class="form-label" for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                        class="form-input @error('subject') is-invalid @enderror"
                        placeholder="Email subject">
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row form-row-body">
                    <textarea name="body" id="body" rows="15" required
                        class="form-textarea @error('body') is-invalid @enderror"
                        placeholder="Write your message here...">{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <label class="form-label">Attachments</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="attachments[]" id="attachments" multiple class="file-input">
                        <label for="attachments" class="file-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                            </svg>
                            <span>Add files</span>
                        </label>
                        <span class="file-hint">Max 10MB per file</span>
                    </div>
                    <div id="file-list" class="file-list"></div>
                </div>

                <div class="compose-actions">
                    <a href="{{ route('admin.mail.inbox') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" name="send_now" value="1" class="btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path>
                        </svg>
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .compose-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1.5rem 1.5rem;
        }

        .compose-toolbar {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 1.5rem;
        }

        .toolbar-title {
            font-size: 1.125rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .compose-form-wrapper {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            overflow: hidden;
        }

        .compose-form {
            padding: 1.5rem;
        }

        .form-row {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-subtle);
        }

        .form-row:last-child {
            border-bottom: none;
        }

        .form-row-body {
            align-items: flex-start;
            padding: 1rem 0;
        }

        .form-label {
            width: 60px;
            flex-shrink: 0;
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .form-input,
        .form-textarea {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 0.9375rem;
            padding: 0;
            outline: none;
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: var(--text-muted);
        }

        .form-textarea {
            min-height: 300px;
            resize: vertical;
            line-height: 1.6;
        }

        .sender-display {
            flex: 1;
            font-size: 0.9375rem;
            color: var(--text-secondary);
        }

        .file-input-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .file-input {
            display: none;
        }

        .file-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 0.875rem;
            transition: all 0.15s;
        }

        .file-label:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .file-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .file-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.75rem;
            padding-left: 60px;
        }

        .file-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            font-size: 0.8125rem;
            color: var(--text-secondary);
        }

        .compose-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.02);
            border-top: 1px solid var(--border-subtle);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: transparent;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            border: 1px solid var(--border-subtle);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.5rem;
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

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .is-invalid {
            border-color: #f87171;
        }

        .invalid-feedback {
            color: #f87171;
            font-size: 0.8125rem;
            margin-top: 0.25rem;
        }

        @media (max-width: 768px) {
            .compose-form {
                padding: 1rem;
            }

            .form-label {
                width: 50px;
            }
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
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                    </svg>
                    <span>${file.name}</span>
                    <span style="color: var(--text-muted);">(${(file.size / 1024).toFixed(1)} KB)</span>
                `;
                fileList.appendChild(div);
            });
        });
    </script>
@endsection
