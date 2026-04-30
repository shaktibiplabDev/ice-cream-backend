@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
    <div class="page-header">
        <h1>
            <small>Catalog</small>
            Edit Category
        </h1>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="form-shell">
        @csrf
        @method('PUT')

        <div class="form-panel">
            <div class="form-panel-head">
                <div>
                    <h2>Category Details</h2>
                    <p>Update category information.</p>
                </div>
                <span class="chip">Editing</span>
            </div>

            <div class="form-panel-body">
                <div class="form-grid">
                    <div class="form-field">
                        <label class="form-label" for="name">
                            Category Name
                            <span class="required-label">Required</span>
                        </label>
                        <input class="form-input" type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label" for="slug">
                            Slug
                            <span class="optional-label">Optional</span>
                        </label>
                        <input class="form-input" type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}">
                        @error('slug') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label" for="icon">
                            Icon (Emoji)
                            <span class="optional-label">Optional</span>
                        </label>
                        <input class="form-input" type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}" placeholder="🏷️">
                        @error('icon') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label" for="sort_order">
                            Sort Order
                            <span class="optional-label">Optional</span>
                        </label>
                        <input class="form-input" type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
                        @error('sort_order') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-field">
                        <label class="form-label" for="is_active">
                            Status
                            <span class="required-label">Required</span>
                        </label>
                        <select class="form-select" name="is_active" id="is_active">
                            <option value="1" {{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $category->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('is_active') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-field full">
                        <label class="form-label" for="description">
                            Description
                            <span class="optional-label">Optional</span>
                        </label>
                        <textarea class="form-textarea" name="description" id="description" rows="3">{{ old('description', $category->description) }}</textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-field full">
                        <label class="form-label" for="image">
                            Category Image
                            <span class="optional-label">Optional</span>
                        </label>
                        @if($category->image)
                            <div style="margin-bottom: 0.5rem;">
                                <img src="{{ Storage::url($category->image) }}" alt="" style="max-height: 100px; border-radius: var(--radius-md);">
                            </div>
                        @endif
                        <input class="form-input" type="file" name="image" id="image" accept="image/*">
                        @error('image') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Category</button>
        </div>
    </form>
@endsection
