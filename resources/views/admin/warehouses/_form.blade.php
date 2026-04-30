<div class="form-grid">
    <div class="form-field">
        <label class="form-label">
            Warehouse Name
            <span class="required-label">Required</span>
        </label>
        <input type="text" name="name" class="form-input" value="{{ old('name', $warehouse->name ?? '') }}" required>
        @error('name')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            Warehouse Code
            <span class="required-label">Required</span>
        </label>
        <input type="text" name="code" class="form-input" value="{{ old('code', $warehouse->code ?? '') }}" placeholder="e.g., WH-MUM-01" required>
        <span class="form-help">Unique identifier for this warehouse</span>
        @error('code')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            Manager Name
            <span class="optional-label">Optional</span>
        </label>
        <input type="text" name="manager_name" class="form-input" value="{{ old('manager_name', $warehouse->manager_name ?? '') }}">
        @error('manager_name')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            Phone
            <span class="optional-label">Optional</span>
        </label>
        <input type="text" name="phone" class="form-input" value="{{ old('phone', $warehouse->phone ?? '') }}">
        @error('phone')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            Email
            <span class="optional-label">Optional</span>
        </label>
        <input type="email" name="email" class="form-input" value="{{ old('email', $warehouse->email ?? '') }}">
        @error('email')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            Status
        </label>
        <select name="is_active" class="form-select">
            <option value="1" {{ old('is_active', $warehouse->is_active ?? true) ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', $warehouse->is_active ?? true) === false ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('is_active')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field full">
        <label class="form-label">
            Address
            <span class="required-label">Required</span>
        </label>
        <textarea name="address" class="form-textarea" rows="2" required>{{ old('address', $warehouse->address ?? '') }}</textarea>
        @error('address')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            City
            <span class="required-label">Required</span>
        </label>
        <input type="text" name="city" class="form-input" value="{{ old('city', $warehouse->city ?? '') }}" required>
        @error('city')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            State
            <span class="optional-label">Optional</span>
        </label>
        <input type="text" name="state" class="form-input" value="{{ old('state', $warehouse->state ?? '') }}">
        @error('state')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            Country
            <span class="optional-label">Optional</span>
        </label>
        <input type="text" name="country" class="form-input" value="{{ old('country', $warehouse->country ?? '') }}">
        @error('country')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            Postal Code
            <span class="optional-label">Optional</span>
        </label>
        <input type="text" name="postal_code" class="form-input" value="{{ old('postal_code', $warehouse->postal_code ?? '') }}">
        @error('postal_code')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            Latitude
            <span class="optional-label">Optional</span>
        </label>
        <input type="number" name="latitude" class="form-input" step="any" value="{{ old('latitude', $warehouse->latitude ?? '') }}" min="-90" max="90">
        <span class="form-help">For map display (-90 to 90)</span>
        @error('latitude')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field">
        <label class="form-label">
            Longitude
            <span class="optional-label">Optional</span>
        </label>
        <input type="number" name="longitude" class="form-input" step="any" value="{{ old('longitude', $warehouse->longitude ?? '') }}" min="-180" max="180">
        <span class="form-help">For map display (-180 to 180)</span>
        @error('longitude')<span class="form-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-field full">
        <label class="form-label">
            Notes
            <span class="optional-label">Optional</span>
        </label>
        <textarea name="notes" class="form-textarea" rows="3">{{ old('notes', $warehouse->notes ?? '') }}</textarea>
        @error('notes')<span class="form-error">{{ $message }}</span>@enderror
    </div>
</div>
