@extends('layouts.admin')

@section('title', 'Warehouses')

@section('content')
    <div class="page-header">
        <h1>
            <small>Celesty Storage Network</small>
            All Warehouses
        </h1>
        <div>
            <a href="{{ route('admin.warehouses.create') }}" style="text-decoration: none;">
                <span class="btn-primary">➕ Add Warehouse</span>
            </a>
        </div>
    </div>

    <div class="glass-card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Warehouse</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $wh)
                    <tr>
                        <td class="order-id">{{ $wh->code }}</td>
                        <td>
                            <div style="font-weight: 500;">{{ $wh->name }}</div>
                            @if($wh->manager_name)
                                <div style="font-size: 0.7rem; color: var(--text-muted);">Mgr: {{ $wh->manager_name }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $wh->city }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">{{ Str::limit($wh->address, 30) }}</div>
                            @if($wh->map_url)
                                <a href="{{ $wh->map_url }}" target="_blank" style="font-size: 0.7rem; text-decoration: none;">📍 View Map</a>
                            @endif
                        </td>
                        <td>
                            <div>{{ $wh->phone ?? 'N/A' }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $wh->email ?? '' }}</div>
                        </td>
                        <td>
                            <span class="status-badge {{ $wh->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $wh->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.warehouses.show', $wh->id) }}" style="text-decoration: none;">
                                    <span class="action-btn action-view">👁️ View</span>
                                </a>
                                <a href="{{ route('admin.warehouses.edit', $wh->id) }}" style="text-decoration: none;">
                                    <span class="action-btn action-edit">✏️ Edit</span>
                                </a>
                                <form action="{{ route('admin.warehouses.destroy', $wh->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this warehouse?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete" style="background:none; border:none; cursor:pointer;">🗑️ Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div class="empty-state-icon">🏭</div>
                            <div>No warehouses found</div>
                            <div style="margin-top: 0.5rem;">
                                <a href="{{ route('admin.warehouses.create') }}" style="text-decoration: none;">
                                    <span class="btn-primary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">+ Add your first warehouse</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($warehouses->hasPages())
        <div class="pagination">
            <div class="pagination-info">
                Showing {{ $warehouses->firstItem() ?? 0 }} to {{ $warehouses->lastItem() ?? 0 }} of {{ $warehouses->total() }} warehouses
            </div>
            <div class="pagination-links">
                {{ $warehouses->onEachSide(1)->links('pagination::simple-tailwind') }}
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
