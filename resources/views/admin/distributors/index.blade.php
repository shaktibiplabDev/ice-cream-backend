@extends('layouts.admin')

@section('title', 'Distributors')

@section('content')
    <div class="page-header">
        <h1>
            <small>Distribution Network</small>
            All Distributors
        </h1>
        <div>
            <a href="{{ route('admin.distributors.create') }}" class="btn-primary">➕ Add New Distributor</a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-group">
            <select class="filter-select" onchange="window.location.href=this.value">
                <option value="{{ route('admin.distributors.index') }}" {{ !request('status') ? 'selected' : '' }}>All Status</option>
                <option value="{{ route('admin.distributors.index', ['status' => 'active']) }}" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="{{ route('admin.distributors.index', ['status' => 'inactive']) }}" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div style="flex:1"></div>
        <div class="date-badge">
            <span>📍</span>
            Total: {{ $distributors->total() }}
        </div>
    </div>

    <!-- Table -->
    <div class="glass-card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name / Address</th>
                        <th>Contact Person</th>
                        <th>Phone / Email</th>
                        <th>Service Area</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distributors as $distributor)
                    <tr>
                        <td class="order-id">#{{ $distributor->id }}</td>
                        <td>
                            <div style="font-weight: 500;">{{ $distributor->name }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">{{ Str::limit($distributor->address, 50) }}</div>
                        </td>
                        <td>{{ $distributor->contact_person ?? 'N/A' }}</td>
                        <td>
                            <div>{{ $distributor->phone ?? 'N/A' }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $distributor->email ?? 'N/A' }}</div>
                        </td>
                        <td>{{ $distributor->service_area ?? 'N/A' }}</td>
                        <td>
                            <span class="status-badge {{ $distributor->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $distributor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.distributors.show', $distributor->id) }}" class="action-btn action-view">👁️ View</a>
                                <a href="{{ route('admin.distributors.edit', $distributor->id) }}" class="action-btn action-edit">✏️ Edit</a>
                                <form action="{{ route('admin.distributors.destroy', $distributor->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this distributor?')">
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
                            <div class="empty-state-icon">🏪</div>
                            <div>No distributors found</div>
                            <div style="margin-top: 0.5rem;">
                                <a href="{{ route('admin.distributors.create') }}" class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">+ Add your first distributor</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($distributors->hasPages())
        <div class="pagination">
            <div class="pagination-info">
                Showing {{ $distributors->firstItem() ?? 0 }} to {{ $distributors->lastItem() ?? 0 }} of {{ $distributors->total() }} distributors
            </div>
            <div class="pagination-links">
                {{ $distributors->onEachSide(1)->links('pagination::simple-tailwind') }}
            </div>
        </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .inline { display: inline; }
</style>
@endpush