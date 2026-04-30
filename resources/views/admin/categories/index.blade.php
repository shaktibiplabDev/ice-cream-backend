@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <div class="page-header">
        <h1>
            <small>Product Categories</small>
            Categories
        </h1>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary">➕ Add Category</a>
    </div>

    <div class="glass-card">
        <div class="card-head">
            <h2>All Categories</h2>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Icon</th>
                    <th>Name</th>
                    <th>Products</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <div style="font-size: 1.5rem;">{{ $category->icon ?? '🏷️' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $category->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $category->slug }}</div>
                        </td>
                        <td>{{ $category->products->count() }}</td>
                        <td>{{ $category->sort_order }}</td>
                        <td>
                            <span class="status-badge {{ $category->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="action-btn action-edit">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="empty-state">
                                <div class="empty-state-icon">🏷️</div>
                                <div>No categories found</div>
                                <a href="{{ route('admin.categories.create') }}" class="btn-primary" style="margin-top: 1rem;">Add First Category</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding: 1rem;">
            {{ $categories->links() }}
        </div>
    </div>
@endsection
