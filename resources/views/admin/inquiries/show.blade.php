@extends('layouts.admin')

@section('title', 'Inquiry Details')

@section('content')
    @php
        $statusClass = match($inquiry->status) {
            'new' => 'status-new',
            'read' => 'status-in-progress',
            'replied' => 'status-resolved',
            default => 'status-new'
        };
        $statusText = ucfirst($inquiry->status);
    @endphp

    <div class="page-header">
        <h1>
            <small>{{ $inquiry->displayNumber() }}</small>
            {{ $inquiry->name }}
        </h1>
        <a href="{{ route('admin.inquiries.index') }}" class="date-badge">← Back to inquiries</a>
    </div>

    <div class="conversation-layout">
        <!-- Conversation Thread -->
        <div class="glass-card">
            <div class="card-head">
                <div>
                    <h2>Conversation</h2>
                    <p>Incoming inquiry and outgoing email replies</p>
                </div>
                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
            </div>

            <div class="form-panel-body">
                <div class="thread-list">
                    @forelse($inquiry->messages as $message)
                        <div class="thread-message {{ $message->direction === 'outbound' ? 'outbound' : 'inbound' }}">
                            <div class="thread-meta">
                                <span>{{ $message->direction === 'outbound' ? 'Celesty → ' . $message->recipient_email : $message->sender_name . ' → Celesty' }}</span>
                                <span>{{ optional($message->sent_at ?? $message->created_at)->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="thread-subject">{{ $message->subject }}</div>
                            <div class="thread-body">{{ $message->body }}</div>
                        </div>
                    @empty
                        <div class="thread-message inbound">
                            <div class="thread-meta">
                                <span>{{ $inquiry->name }} → Celesty</span>
                                <span>{{ $inquiry->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="thread-subject">Original inquiry</div>
                            <div class="thread-body">{{ $inquiry->requirement }}</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Panels -->
        <div class="form-shell">
            <!-- Reply Form -->
            <div class="form-panel">
                <div class="form-panel-head">
                    <div>
                        <h2>Reply by Email</h2>
                        <p>Send a reply directly from the admin panel.</p>
                    </div>
                </div>
                <form action="{{ route('admin.inquiries.reply', $inquiry->id) }}" method="POST" class="form-panel-body">
                    @csrf
                    <div class="form-field" style="margin-bottom: 1rem;">
                        <label class="form-label" for="subject">
                            Subject
                            <span class="required-label">Required</span>
                        </label>
                        <input class="form-input" type="text" name="subject" id="subject" value="{{ old('subject', 'Re: Celesty inquiry ' . $inquiry->displayNumber()) }}" required>
                        @error('subject') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-field" style="margin-bottom: 1rem;">
                        <label class="form-label" for="body">
                            Message
                            <span class="required-label">Required</span>
                        </label>
                        <textarea class="form-textarea" name="body" id="body" rows="8" required placeholder="Write your reply...">{{ old('body') }}</textarea>
                        @error('body') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">✉️ Send Reply</button>
                    </div>
                </form>
            </div>

            <!-- Customer Info & Status -->
            <div class="form-panel">
                <div class="form-panel-head">
                    <div>
                        <h2>Customer Information</h2>
                        <p>Inquiry details and status controls.</p>
                    </div>
                </div>
                <div class="form-panel-body">
                    <div style="margin-bottom: 1.25rem;">
                        <div class="form-help" style="margin-bottom: 0.25rem;">Business Name</div>
                        <div class="thread-subject">{{ $inquiry->business_name ?: 'Not provided' }}</div>
                    </div>
                    <div style="margin-bottom: 1.25rem;">
                        <div class="form-help" style="margin-bottom: 0.25rem;">Email Address</div>
                        <a class="action-link" href="mailto:{{ $inquiry->email }}" style="font-size: 0.875rem;">{{ $inquiry->email }}</a>
                    </div>
                    <div style="margin-bottom: 1.25rem;">
                        <div class="form-help" style="margin-bottom: 0.25rem;">Submitted On</div>
                        <div>{{ $inquiry->created_at->format('F d, Y H:i') }}</div>
                    </div>
                    <div style="margin-bottom: 1.25rem;">
                        <div class="form-help" style="margin-bottom: 0.25rem;">Phone Number</div>
                        <div>{{ $inquiry->phone ?? 'Not provided' }}</div>
                    </div>

                    <form action="{{ route('admin.inquiries.update-status', $inquiry->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-field">
                            <label class="form-label" for="status">Update Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="new" @selected($inquiry->status === 'new')>📋 New</option>
                                <option value="read" @selected($inquiry->status === 'read')>👁️ Read</option>
                                <option value="replied" @selected($inquiry->status === 'replied')>✉️ Replied</option>
                            </select>
                        </div>
                        <div class="form-actions" style="margin-top: 1rem;">
                            <button type="submit" class="btn-secondary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection