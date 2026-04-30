@extends('layouts.admin')

@section('title', $email->subject)

@section('content')
    <div class="page-header">
        <h1>
            <small>{{ $email->type === 'sent' ? 'Sent' : 'Received' }}</small>
            {{ $email->subject }}
        </h1>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.mail.compose') }}" class="btn-secondary">Reply</a>
            <form action="{{ route('admin.mail.destroy', $email) }}" method="POST" onsubmit="return confirm('Delete this email?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-secondary">🗑️ Delete</button>
            </form>
        </div>
    </div>

    <div class="glass-card">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div>
                    <div style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $email->subject }}</div>
                    <div style="color: var(--text-muted);">
                        @if($email->type === 'sent')
                            <strong>To:</strong> {{ $email->to_name ?? '' }} &lt;{{ $email->to_email }}&gt;
                        @else
                            <strong>From:</strong> {{ $email->from_name }} &lt;{{ $email->from_email }}&gt;
                        @endif
                    </div>
                    @if($email->cc)
                        <div style="color: var(--text-muted); font-size: 0.875rem;">
                            <strong>CC:</strong> {{ $email->cc }}
                        </div>
                    @endif
                </div>
                <div style="text-align: right; color: var(--text-muted);">
                    <div>{{ $email->sent_at?->format('M d, Y') }}</div>
                    <div style="font-size: 0.875rem;">{{ $email->sent_at?->format('h:i A') }}</div>
                </div>
            </div>
        </div>

        <div style="padding: 1.5rem; min-height: 300px;">
            <div style="white-space: pre-wrap; line-height: 1.6;">{{ $email->body }}</div>
        </div>

        @if($email->attachments && count($email->attachments) > 0)
            <div style="padding: 1.5rem; border-top: 1px solid var(--border-subtle);">
                <div style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-muted);">Attachments</div>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    @foreach($email->attachments as $attachment)
                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank"
                            style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: rgba(255,255,255,0.05); border-radius: var(--radius-md); text-decoration: none; color: var(--text-secondary);">
                            <span>📎</span>
                            <span>{{ $attachment['name'] }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">({{ number_format($attachment['size'] / 1024, 1) }} KB)</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
