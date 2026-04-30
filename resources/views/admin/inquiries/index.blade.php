@extends('layouts.admin')

@section('title', 'Inquiries')

@section('content')
    <div class="page-header">
        <h1>
            <small>Customer Inquiries</small>
            All Inquiries
        </h1>
        <div class="date-badge">
            <span>📋</span>
            Total: {{ $inquiries->total() }}
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-group">
            <select class="filter-select" onchange="window.location.href=this.value">
                <option value="{{ route('admin.inquiries.index') }}" {{ !request('status') ? 'selected' : '' }}>All Status</option>
                <option value="{{ route('admin.inquiries.index', ['status' => 'new']) }}" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                <option value="{{ route('admin.inquiries.index', ['status' => 'read']) }}" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                <option value="{{ route('admin.inquiries.index', ['status' => 'replied']) }}" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
            </select>
        </div>
        <div style="flex:1"></div>
        <a href="{{ route('admin.dashboard') }}" class="chip">📊 Dashboard</a>
    </div>

    <!-- Table -->
    <div class="glass-card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Inquiry No.</th>
                        <th>Name</th>
                        <th>Business</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inquiry)
                    <tr>
                        <td class="order-id">{{ $inquiry->displayNumber() }}</td>
                        <td>{{ $inquiry->name }}</td>
                        <td>{{ $inquiry->business_name ?? 'N/A' }}</td>
                        <td>{{ $inquiry->email }}</td>
                        <td>
                            @php
                                $statusClass = match($inquiry->status) {
                                    'new' => 'status-new',
                                    'read' => 'status-in-progress',
                                    'replied' => 'status-resolved',
                                    default => 'status-new'
                                };
                                $statusText = ucfirst($inquiry->status);
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td>{{ $inquiry->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.inquiries.show', $inquiry->id) }}" class="action-btn action-view">👁️ View</a>
                                <form action="{{ route('admin.inquiries.destroy', $inquiry->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this inquiry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete" style="background:none; border:none; cursor:pointer;">🗑️ Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-state-icon">📭</div>
                            <div>No inquiries found</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($inquiries->hasPages())
        <div class="pagination">
            <div class="pagination-info">
                Showing {{ $inquiries->firstItem() ?? 0 }} to {{ $inquiries->lastItem() ?? 0 }} of {{ $inquiries->total() }} results
            </div>
            <div class="pagination-links">
                {{ $inquiries->onEachSide(1)->links('pagination::simple-tailwind') }}
            </div>
        </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    /* Custom pagination styling to match dark theme */
    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border-subtle);
        flex-wrap: wrap;
        gap: 1rem;
    }
    .pagination-info {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .pagination-links {
        display: flex;
        gap: 0.25rem;
    }
    .pagination-links a,
    .pagination-links span {
        padding: 0.375rem 0.75rem;
        border-radius: var(--radius-md);
        font-size: 0.75rem;
        text-decoration: none;
        color: var(--text-secondary);
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-subtle);
    }
    .pagination-links a:hover {
        background: rgba(255, 255, 255, 0.08);
    }
    .pagination-links .active span {
        background: var(--accent-gradient);
        color: white;
        border-color: transparent;
    }
    .inline {
        display: inline;
    }
</style>
@endpush