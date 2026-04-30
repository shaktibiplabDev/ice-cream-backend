@extends('layouts.admin')

@section('title', 'Distributor Details')

@section('content')
    <div class="page-header">
        <h1>
            <small>Distributor Details</small>
            {{ $distributor->name }}
        </h1>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.distributors.edit', $distributor->id) }}" class="btn-primary">✏️ Edit</a>
            <a href="{{ route('admin.distributors.index') }}" class="btn-secondary">← Back</a>
        </div>
    </div>

    <div class="glass-card">
        <div class="card-head">
            <div>
                <h2>Complete Information</h2>
                <p>All details about this distributor</p>
            </div>
            <span class="status-badge {{ $distributor->is_active ? 'status-active' : 'status-inactive' }}">
                {{ $distributor->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        
        <div class="form-panel-body">
            <div class="detail-grid">
                <!-- Basic Information -->
                <div class="detail-item">
                    <div class="detail-label">🏢 Business Name</div>
                    <div class="detail-value">{{ $distributor->name }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">👤 Contact Person</div>
                    <div class="detail-value">{{ $distributor->contact_person ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">📍 Address</div>
                    <div class="detail-value">{{ $distributor->address ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">📞 Phone</div>
                    <div class="detail-value">{{ $distributor->phone ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">📧 Email</div>
                    <div class="detail-value">
                        @if($distributor->email)
                            <a href="mailto:{{ $distributor->email }}" class="action-link">{{ $distributor->email }}</a>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">🌐 Website</div>
                    <div class="detail-value">
                        @if($distributor->website)
                            <a href="{{ $distributor->website }}" target="_blank" class="action-link">{{ $distributor->website }}</a>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">📱 Social Media</div>
                    <div class="detail-value">{{ $distributor->social_media ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">🗺️ Service Area</div>
                    <div class="detail-value">{{ $distributor->service_area ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">🚚 Delivery Capacity</div>
                    <div class="detail-value">{{ ucwords(str_replace('_', ' ', $distributor->delivery_capacity ?? 'N/A')) }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">⏰ Business Hours</div>
                    <div class="detail-value">{{ $distributor->timings ?? 'N/A' }}</div>
                </div>
                
                <div class="detail-item full-width">
                    <div class="detail-label">📝 Description</div>
                    <div class="detail-value">{{ $distributor->description ?? 'No description provided.' }}</div>
                </div>
            </div>
            
            <!-- Map Section - Shows map instead of lat/long text -->
            @php
                $hasCoordinates = !empty($distributor->latitude) && !empty($distributor->longitude);
                $mapCenter = $hasCoordinates 
                    ? $distributor->latitude . ', ' . $distributor->longitude 
                    : '20.5937, 78.9629'; // Default to center of India
                $mapZoom = $hasCoordinates ? 13 : 5;
            @endphp
            
            <div style="margin-top: 1.5rem;">
                <div class="detail-label" style="margin-bottom: 0.75rem;">📍 Location Map</div>
                
                @if($hasCoordinates)
                    <div id="map" style="height: 400px; width: 100%; border-radius: var(--radius-lg); margin-bottom: 0.75rem;"></div>
                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <a href="https://www.google.com/maps?q={{ $distributor->latitude }},{{ $distributor->longitude }}" target="_blank" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.75rem; text-decoration: none;">
                            <span>🗺️</span>
                            Open in Google Maps
                            <span>→</span>
                        </a>
                    </div>
                @else
                    <div style="height: 300px; width: 100%; border-radius: var(--radius-lg); background: rgba(0,0,0,0.3); border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 1rem;">
                        <div style="font-size: 3rem; opacity: 0.5;">🗺️</div>
                        <div style="color: var(--text-muted); text-align: center;">
                            No location coordinates available<br>
                            <span style="font-size: 0.75rem;">Please add latitude & longitude in edit mode to display map</span>
                        </div>
                        <a href="{{ route('admin.distributors.edit', $distributor->id) }}" class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">✏️ Add Location</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    @if($hasCoordinates)
    <script>
        // Initialize map with coordinates
        var map = L.map('map').setView([{{ $distributor->latitude }}, {{ $distributor->longitude }}], 13);
        
        // Use dark map tiles to match the theme
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions" target="_blank">CARTO</a>',
            maxZoom: 19,
            subdomains: 'abcd'
        }).addTo(map);
        
        // Add custom marker
        var customIcon = L.divIcon({
            html: '<div style="background: linear-gradient(135deg, #4f46e5, #7c3aed); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">📍</div>',
            iconSize: [32, 32],
            popupAnchor: [0, -16],
            className: 'custom-marker'
        });
        
        var marker = L.marker([{{ $distributor->latitude }}, {{ $distributor->longitude }}], { icon: customIcon }).addTo(map);
        
        // Popup content with business info
        var popupContent = `
            <div style="background: #1a1d24; padding: 8px 12px; border-radius: 8px; max-width: 250px;">
                <strong style="color: white;">{{ addslashes($distributor->name) }}</strong><br>
                <span style="color: #9ca3af; font-size: 12px;">{{ addslashes($distributor->address ?? 'Address not available') }}</span>
                @if($distributor->phone)
                    <br><span style="color: #6b7280; font-size: 11px;">📞 {{ $distributor->phone }}</span>
                @endif
                @if($distributor->service_area)
                    <br><span style="color: #6b7280; font-size: 11px;">📍 {{ $distributor->service_area }}</span>
                @endif
            </div>
        `;
        
        marker.bindPopup(popupContent).openPopup();
        
        // Add scale control
        L.control.scale({ metric: true, imperial: false, position: 'bottomleft' }).addTo(map);
    </script>
    @endif
@endsection

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }
    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .full-width {
        grid-column: span 2;
    }
    
    /* Custom marker styling */
    .custom-marker {
        background: transparent;
        border: none;
    }
    
    /* Leaflet popup dark theme */
    .leaflet-popup-content-wrapper {
        background: #1a1d24;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
    }
    .leaflet-popup-tip {
        background: #1a1d24;
    }
    .leaflet-popup-close-button {
        color: #9ca3af !important;
    }
    
    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .full-width {
            grid-column: span 1;
        }
    }
</style>
@endpush