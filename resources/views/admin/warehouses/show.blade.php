@extends('layouts.admin')

@section('title', $warehouse->name)

@section('content')
    <div class="page-header">
        <h1>
            <small>Warehouse Details</small>
            {{ $warehouse->name }}
        </h1>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.warehouses.index') }}" style="text-decoration: none;"><span class="btn-secondary">← Back</span></a>
            <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" style="text-decoration: none;"><span class="btn-primary">✏️ Edit</span></a>
        </div>
    </div>

    @php
        $hasCoordinates = !empty($warehouse->latitude) && !empty($warehouse->longitude);
    @endphp

    <div class="detail-grid">
        <!-- Warehouse Info Card -->
        <div class="glass-card" style="grid-column: 1 / -1;">
            <div class="card-head">
                <h2>🏭 Warehouse Information</h2>
                <span class="status-badge {{ $warehouse->is_active ? 'status-active' : 'status-inactive' }}">
                    {{ $warehouse->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="form-panel-body">
                <div class="detail-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 1.5rem;">
                    <div class="detail-item">
                        <span class="detail-label">Code</span>
                        <span class="detail-value">{{ $warehouse->code }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Manager</span>
                        <span class="detail-value">{{ $warehouse->manager_name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value">{{ $warehouse->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">{{ $warehouse->email ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">📍 Address</span>
                        <span class="detail-value">{{ $warehouse->address ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">📍 Full Address</span>
                        <span class="detail-value">{{ $warehouse->full_address }}</span>
                    </div>
                </div>

                <!-- Map Section -->
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-subtle);">
                    <div class="detail-label" style="margin-bottom: 0.75rem;">🗺️ Location Map</div>

                    @if($hasCoordinates)
                        <div id="map" style="height: 400px; width: 100%; border-radius: var(--radius-lg); margin-bottom: 0.75rem;"></div>
                        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                            <a href="https://www.google.com/maps?q={{ $warehouse->latitude }},{{ $warehouse->longitude }}" target="_blank" style="text-decoration: none;">
                                <span class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.75rem;">
                                    <span>🗺️</span>
                                    Open in Google Maps
                                    <span>→</span>
                                </span>
                            </a>
                        </div>
                    @else
                        <div style="height: 300px; width: 100%; border-radius: var(--radius-lg); background: rgba(0,0,0,0.3); border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 1rem;">
                            <div style="font-size: 3rem; opacity: 0.5;">�️</div>
                            <div style="color: var(--text-muted); text-align: center;">
                                No location coordinates available<br>
                                <span style="font-size: 0.75rem;">Please add latitude & longitude in edit mode to display map</span>
                            </div>
                            <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" style="text-decoration: none;">
                                <span class="btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">✏️ Add Location</span>
                            </a>
                        </div>
                    @endif
                </div>

                @if($warehouse->notes)
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-subtle);">
                        <span class="detail-label">Notes</span>
                        <p style="margin-top: 0.5rem; color: var(--text-secondary);">{{ $warehouse->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Current Inventory -->
        <div class="glass-card">
            <div class="card-head">
                <h2>📦 Current Inventory</h2>
                <a href="{{ route('admin.inventory.index', ['warehouse_id' => $warehouse->id]) }}" style="text-decoration: none;">
                    <span class="action-btn action-view">View All</span>
                </a>
            </div>
            <div class="table-wrap" style="padding: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouse->inventory->take(5) as $inv)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    @if($inv->product->image)
                                        <img src="{{ asset('storage/' . $inv->product->image) }}" alt="" style="width: 30px; height: 30px; border-radius: 6px; object-fit: cover;">
                                    @else
                                        <div style="width: 30px; height: 30px; border-radius: 6px; background: var(--accent-gradient); display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">🍦</div>
                                    @endif
                                    {{ $inv->product->name }}
                                </div>
                            </td>
                            <td style="font-weight: 600;">{{ $inv->quantity }}</td>
                            <td>
                                @if($inv->isLowStock())
                                    <span class="status-badge status-inactive">Low</span>
                                @else
                                    <span class="status-badge status-active">OK</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="empty-state" style="padding: 2rem;">
                                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📦</div>
                                <div>No inventory</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-card">
            <div class="card-head">
                <h2>⚡ Quick Actions</h2>
            </div>
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="{{ route('admin.inventory.create', ['warehouse_id' => $warehouse->id]) }}" style="text-decoration: none;">
                    <span class="btn-primary" style="display: block; text-align: center;">➕ Add Stock</span>
                </a>
                <a href="{{ route('admin.inventory.history', ['warehouse_id' => $warehouse->id]) }}" style="text-decoration: none;">
                    <span class="btn-secondary" style="display: block; text-align: center;">📜 View History</span>
                </a>
                <form action="{{ route('admin.warehouses.destroy', $warehouse->id) }}" method="POST" onsubmit="return confirm('Delete this warehouse?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" style="width: 100%;">🗑️ Delete Warehouse</button>
                </form>
            </div>
        </div>
    </div>

    @if($hasCoordinates)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize map with coordinates
        var map = L.map('map').setView([{{ $warehouse->latitude }}, {{ $warehouse->longitude }}], 13);

        // Use dark map tiles to match the theme
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions" target="_blank">CARTO</a>',
            maxZoom: 19,
            subdomains: 'abcd'
        }).addTo(map);

        // Add custom marker with W for warehouse
        var customIcon = L.divIcon({
            html: '<div style="background: linear-gradient(135deg, #ff6b6b, #4ecdc4); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.3); color: white; font-weight: bold;">W</div>',
            iconSize: [32, 32],
            popupAnchor: [0, -16],
            className: 'custom-marker'
        });

        var marker = L.marker([{{ $warehouse->latitude }}, {{ $warehouse->longitude }}], { icon: customIcon }).addTo(map);

        // Popup content with warehouse info
        var popupContent = `
            <div style="background: #1a1d24; padding: 8px 12px; border-radius: 8px; max-width: 250px;">
                <strong style="color: white;">{{ addslashes($warehouse->name) }}</strong><br>
                <span style="color: #9ca3af; font-size: 12px;">{{ addslashes($warehouse->address ?? 'Address not available') }}</span>
                @if($warehouse->phone)
                    <br><span style="color: #6b7280; font-size: 11px;">📞 {{ $warehouse->phone }}</span>
                @endif
                @if($warehouse->manager_name)
                    <br><span style="color: #6b7280; font-size: 11px;">👤 {{ $warehouse->manager_name }}</span>
                @endif
            </div>
        `;

        marker.bindPopup(popupContent).openPopup();

        // Add scale control
        L.control.scale({ metric: true, imperial: false, position: 'bottomleft' }).addTo(map);
    </script>

    <style>
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
    </style>
    @endif
@endsection
