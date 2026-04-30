@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $capacityOptions = [
        'up_to_500' => 'Up to 500 units/day',
        '500_to_1000' => '500 - 1000 units/day',
        '1000_to_5000' => '1,000 - 5,000 units/day',
        '5000_plus' => '5,000+ units/day',
    ];
    $selectedCapacity = old('delivery_capacity', $distributor->delivery_capacity ?? '');
    $selectedStatus = (string) old('is_active', isset($distributor) ? (int) $distributor->is_active : 1);
    $latitude = old('latitude', $distributor->latitude ?? '');
    $longitude = old('longitude', $distributor->longitude ?? '');
@endphp

<div class="form-shell">
    <div class="form-panel">
        <div class="form-panel-head">
            <div>
                <h2>Distributor Profile</h2>
                <p>The basics your team will scan first when handling operations.</p>
            </div>
            <span class="chip">{{ $isEdit ? 'Editing' : 'New partner' }}</span>
        </div>
        <div class="form-panel-body">
            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label" for="name">
                        Distributor name
                        <span class="required-label">Required</span>
                    </label>
                    <input class="form-input" type="text" name="name" id="name" value="{{ old('name', $distributor->name ?? '') }}" required placeholder="Celesty Downtown">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="contact_person">
                        Contact person
                        <span class="optional-label">Optional</span>
                    </label>
                    <input class="form-input" type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $distributor->contact_person ?? '') }}" placeholder="Aisha Mehta">
                    @error('contact_person') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="delivery_capacity">
                        Delivery capacity
                        <span class="optional-label">Optional</span>
                    </label>
                    <select class="form-select" name="delivery_capacity" id="delivery_capacity">
                        <option value="">Not set yet</option>
                        @foreach($capacityOptions as $value => $label)
                            <option value="{{ $value }}" @selected($selectedCapacity === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('delivery_capacity') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="is_active">
                        Status
                        <span class="required-label">Required</span>
                    </label>
                    <select class="form-select" name="is_active" id="is_active" required>
                        <option value="1" @selected($selectedStatus === '1')>Active</option>
                        <option value="0" @selected($selectedStatus === '0')>Inactive</option>
                    </select>
                    @error('is_active') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-panel-head">
            <div>
                <h2>Contact Details</h2>
                <p>Add only what you have. Empty optional fields stay out of the way.</p>
            </div>
        </div>
        <div class="form-panel-body">
            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label" for="phone">
                        Phone number
                        <span class="optional-label">Optional</span>
                    </label>
                    <input class="form-input" type="tel" name="phone" id="phone" value="{{ old('phone', $distributor->phone ?? '') }}" placeholder="+91 98765 43210">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="email">
                        Email address
                        <span class="optional-label">Optional</span>
                    </label>
                    <input class="form-input" type="email" name="email" id="email" value="{{ old('email', $distributor->email ?? '') }}" placeholder="partner@example.com">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="website">
                        Website
                        <span class="optional-label">Optional</span>
                    </label>
                    <input class="form-input" type="url" name="website" id="website" value="{{ old('website', $distributor->website ?? '') }}" placeholder="https://example.com">
                    @error('website') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="social_media">
                        Social media
                        <span class="optional-label">Optional</span>
                    </label>
                    <input class="form-input" type="text" name="social_media" id="social_media" value="{{ old('social_media', $distributor->social_media ?? '') }}" placeholder="Instagram, Facebook, LinkedIn">
                    @error('social_media') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-panel-head">
            <div>
                <h2>Coverage</h2>
                <p>Useful context for dispatch planning and customer handoffs.</p>
            </div>
        </div>
        <div class="form-panel-body">
            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label" for="service_area">
                        Service area
                        <span class="optional-label">Optional</span>
                    </label>
                    <input class="form-input" type="text" name="service_area" id="service_area" value="{{ old('service_area', $distributor->service_area ?? '') }}" placeholder="Mumbai, Pune, Navi Mumbai">
                    @error('service_area') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="timings">
                        Business hours
                        <span class="optional-label">Optional</span>
                    </label>
                    <input class="form-input" type="text" name="timings" id="timings" value="{{ old('timings', $distributor->timings ?? '') }}" placeholder="Mon-Fri, 9 AM - 7 PM">
                    @error('timings') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-field full">
                    <label class="form-label" for="description">
                        Notes
                        <span class="optional-label">Optional</span>
                    </label>
                    <textarea class="form-textarea" name="description" id="description" rows="4" placeholder="Delivery preferences, special handling notes, internal comments...">{{ old('description', $distributor->description ?? '') }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-panel-head">
            <div>
                <h2>Location</h2>
                <p>Search an address or click the map. Latitude and longitude fill automatically.</p>
            </div>
        </div>
        <div class="form-panel-body">
            <div class="form-field" style="margin-bottom:16px;">
                <label class="form-label" for="address">
                    Address
                    <span class="required-label">Required</span>
                </label>
                <textarea class="form-textarea" name="address" id="address" rows="3" required placeholder="Full pickup or distribution address">{{ old('address', $distributor->address ?? '') }}</textarea>
                @error('address') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="location-tools" style="margin-bottom:16px;">
                <div class="form-field">
                    <label class="form-label" for="searchAddress">Find address faster</label>
                    <input class="form-input" type="search" id="searchAddress" placeholder="Search area, building, city...">
                </div>
                <button type="button" id="searchBtn" class="btn-soft">Search</button>
                <button type="button" id="currentLocationBtn" class="btn-secondary">Use my location</button>
            </div>

            <div class="form-grid three" style="margin-bottom:16px;">
                <div class="form-field">
                    <label class="form-label" for="mapStyle">Map style</label>
                    <select id="mapStyle" class="form-select">
                        <option value="positron">Light</option>
                        <option value="voyager">Road detail</option>
                        <option value="osm">OpenStreetMap</option>
                    </select>
                </div>
                <div class="form-field">
                    <label class="form-label" for="latitude">
                        Latitude
                        <span class="required-label">Required</span>
                    </label>
                    <input class="form-input" type="number" step="any" name="latitude" id="latitude" value="{{ $latitude }}" required readonly placeholder="Search or click map">
                    @error('latitude') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label class="form-label" for="longitude">
                        Longitude
                        <span class="required-label">Required</span>
                    </label>
                    <input class="form-input" type="number" step="any" name="longitude" id="longitude" value="{{ $longitude }}" required readonly placeholder="Search or click map">
                    @error('longitude') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div id="map" class="map-canvas"></div>
            <p class="form-help" style="margin-top:10px;">Tip: search first for a close match, then click the map once to fine-tune the pin.</p>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('admin.distributors.index') }}" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'Update Distributor' : 'Add Distributor' }}</button>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        .leaflet-control-attribution {
            font-size: 9px;
        }

        .leaflet-control-zoom a {
            color: #1d7ab5;
            font-weight: 800;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const defaultCenter = [20.5937, 78.9629];
            const initialLat = @json($latitude !== '' ? (float) $latitude : null);
            const initialLng = @json($longitude !== '' ? (float) $longitude : null);
            const initialName = @json(old('name', $distributor->name ?? 'Selected location'));
            const initialAddress = @json(old('address', $distributor->address ?? ''));
            const mapStyles = {
                positron: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
                voyager: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
                osm: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
            };
            const mapAttributions = {
                positron: '&copy; OpenStreetMap contributors &copy; CARTO',
                voyager: '&copy; OpenStreetMap contributors &copy; CARTO',
                osm: '&copy; OpenStreetMap contributors'
            };
            let map;
            let marker;
            let tileLayer;

            function markerIcon() {
                return L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#c90000,#f4c542);border:3px solid white;box-shadow:0 3px 12px rgba(0,0,0,.28);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;">C</div>',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
            }

            function setCoords(lat, lng, label = 'Selected location') {
                document.getElementById('latitude').value = lat.toFixed(7);
                document.getElementById('longitude').value = lng.toFixed(7);

                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker([lat, lng], { icon: markerIcon() }).addTo(map);
                marker.bindPopup(`<strong>${label}</strong><br>${lat.toFixed(6)}, ${lng.toFixed(6)}`).openPopup();
            }

            function setMapStyle(style) {
                if (tileLayer) {
                    map.removeLayer(tileLayer);
                }

                tileLayer = L.tileLayer(mapStyles[style], {
                    attribution: mapAttributions[style],
                    maxZoom: 19
                }).addTo(map);
            }

            function maybeFillAddress(lat, lng) {
                const addressField = document.getElementById('address');

                if (addressField.value.trim()) {
                    return;
                }

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.display_name && !addressField.value.trim()) {
                            addressField.value = data.display_name;
                        }
                    })
                    .catch(() => {});
            }

            function searchAddress() {
                const input = document.getElementById('searchAddress');
                const searchBtn = document.getElementById('searchBtn');
                const query = input.value.trim();

                if (!query) {
                    input.focus();
                    return;
                }

                searchBtn.textContent = 'Searching...';
                searchBtn.disabled = true;

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&addressdetails=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data || data.length === 0) {
                            alert('Address not found. Try a nearby landmark or shorter address.');
                            return;
                        }

                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        const displayName = data[0].display_name;
                        map.setView([lat, lng], 15);
                        setCoords(lat, lng, 'Search result');
                        document.getElementById('address').value = displayName;
                    })
                    .catch(() => alert('Could not search that address right now.'))
                    .finally(() => {
                        searchBtn.textContent = 'Search';
                        searchBtn.disabled = false;
                    });
            }

            map = L.map('map').setView(
                initialLat !== null && initialLng !== null ? [initialLat, initialLng] : defaultCenter,
                initialLat !== null && initialLng !== null ? 13 : 5
            );
            setMapStyle('positron');
            L.control.scale({ metric: true, imperial: false, position: 'bottomleft' }).addTo(map);

            if (initialLat !== null && initialLng !== null) {
                setCoords(initialLat, initialLng, initialName || initialAddress || 'Current location');
            }

            map.on('click', function(event) {
                setCoords(event.latlng.lat, event.latlng.lng);
                maybeFillAddress(event.latlng.lat, event.latlng.lng);
            });

            document.getElementById('mapStyle').addEventListener('change', function(event) {
                setMapStyle(event.target.value);
            });

            document.getElementById('searchBtn').addEventListener('click', searchAddress);
            document.getElementById('searchAddress').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchAddress();
                }
            });

            document.getElementById('currentLocationBtn').addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Your browser does not support location access.');
                    return;
                }

                this.textContent = 'Locating...';
                this.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    position => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        map.setView([lat, lng], 15);
                        setCoords(lat, lng, 'Your location');
                        maybeFillAddress(lat, lng);
                        this.textContent = 'Use my location';
                        this.disabled = false;
                    },
                    () => {
                        alert('Could not get your location. Please allow location access or search manually.');
                        this.textContent = 'Use my location';
                        this.disabled = false;
                    }
                );
            });
        });
    </script>
@endpush
