@extends('layouts.admin')

@section('title', 'Territory Map')

@section('content')
    <div class="page-header">
        <h1>
            <small>Distribution Network</small>
            Territory Map
        </h1>
        <div class="legend-bar">
            <span class="legend-item"><span class="legend-dot" style="background: #10b981;"></span> Normal Stock</span>
            <span class="legend-item"><span class="legend-dot" style="background: #f59e0b;"></span> Low Stock Warning</span>
            <span class="legend-item"><span class="legend-dot" style="background: #ef4444;"></span> Critical Stock</span>
            <span class="legend-item"><span class="legend-dot" style="background: #4f46e5;"></span> Distributor</span>
        </div>
    </div>

    @if($hasLocations)
        <div class="glass-card" style="padding: 0; overflow: hidden;">
            <div id="territory-map" style="height: 70vh; width: 100%;"></div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid" style="margin-top: 1.5rem;">
            <div class="stat-card mint">
                <div class="stat-top">
                    <div class="stat-icon mint">🏭</div>
                    <span class="stat-trend success">{{ $warehouses->count() }} Active</span>
                </div>
                <div class="stat-value">{{ $warehouses->count() }}</div>
                <div class="stat-label">Warehouses</div>
                <div class="stat-sub">Storage & Distribution Centers</div>
            </div>

            <div class="stat-card blue">
                <div class="stat-top">
                    <div class="stat-icon blue">🚚</div>
                    <span class="stat-trend info">{{ $distributors->count() }} Active</span>
                </div>
                <div class="stat-value">{{ $distributors->count() }}</div>
                <div class="stat-label">Distributors</div>
                <div class="stat-sub">Partner Network</div>
            </div>

            <div class="stat-card blush">
                <div class="stat-top">
                    <div class="stat-icon blush">⚠️</div>
                    @php
                        $criticalCount = $warehouses->where('status', 'critical')->count();
                    @endphp
                    <span class="stat-trend {{ $criticalCount > 0 ? 'danger' : 'success' }}">
                        {{ $criticalCount > 0 ? $criticalCount . ' Critical' : 'All Good' }}
                    </span>
                </div>
                <div class="stat-value">{{ $warehouses->sum('low_stock_count') }}</div>
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-sub">Require Attention</div>
            </div>

            <div class="stat-card lavender">
                <div class="stat-top">
                    <div class="stat-icon lavender">📦</div>
                    <span class="stat-trend success">{{ $warehouses->sum('total_items') }}</span>
                </div>
                <div class="stat-value">{{ $warehouses->sum('total_items') }}</div>
                <div class="stat-label">Total Inventory</div>
                <div class="stat-sub">Items Across Network</div>
            </div>
        </div>
    @else
        <div class="glass-card" style="text-align: center; padding: 4rem;">
            <div class="empty-state-icon">🗺️</div>
            <h2 style="margin-bottom: 1rem;">No Locations Available</h2>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                Add warehouses and distributors with coordinates to see them on the map.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="{{ route('admin.warehouses.create') }}" class="btn-primary">➕ Add Warehouse</a>
                <a href="{{ route('admin.distributors.create') }}" class="btn-secondary">➕ Add Distributor</a>
            </div>
        </div>
    @endif
@endsection

@push('styles')
<style>
    .legend-bar {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        box-shadow: 0 0 8px currentColor;
    }

    /* Leaflet Popup Styling - High Contrast White */
    .leaflet-popup-content-wrapper {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        border: 2px solid #000000;
    }

    .leaflet-popup-tip {
        background: #ffffff;
        border: 2px solid #000000;
    }

    .leaflet-popup-content {
        margin: 0;
        padding: 0;
    }

    .popup-content {
        padding: 1rem;
        min-width: 200px;
        max-width: 280px;
        color: #000000;
    }

    .popup-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .popup-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: var(--accent-gradient);
    }

    .popup-icon.distributor {
        background: linear-gradient(135deg, #4f46e5, #818cf8);
    }

    .popup-title {
        flex: 1;
    }

    .popup-title h3 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        color: #000000;
    }

    .popup-title span {
        font-size: 0.75rem;
        color: #4b5563;
    }

    .popup-body {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .popup-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.8125rem;
    }

    .popup-row .label {
        color: #4b5563;
        font-weight: 500;
    }

    .popup-row .value {
        color: #000000;
        font-weight: 600;
    }

    .status-badge {
        display: inline-flex;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.normal {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
    }

    .status-badge.warning {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
    }

    .status-badge.critical {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
    }

    .popup-actions {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--border-subtle);
        display: flex;
        gap: 0.5rem;
    }

    .popup-actions a {
        flex: 1;
        text-align: center;
        padding: 0.5rem;
        background: #000000;
        border-radius: 6px;
        font-size: 0.75rem;
        color: #ffffff;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    .popup-actions a:hover {
        background: #374151;
        color: #ffffff;
    }

    /* Territory polygon styles */
    .territory-polygon {
        fill-opacity: 0.2;
        stroke-width: 2;
        stroke-opacity: 0.6;
    }

    /* Light Theme Support for Map */
    [data-theme="light"] .leaflet-popup-content-wrapper {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    [data-theme="light"] .leaflet-popup-tip {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .popup-content {
        color: #1a1a1a;
    }

    [data-theme="light"] .popup-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .popup-title h3 {
        color: #1a1a1a;
    }

    [data-theme="light"] .popup-title span {
        color: #6b7280;
    }

    [data-theme="light"] .popup-row .label {
        color: #6b7280;
    }

    [data-theme="light"] .popup-row .value {
        color: #1a1a1a;
    }

    [data-theme="light"] .popup-actions {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .popup-actions a {
        background: #1a1a1a;
        color: #ffffff;
    }

    [data-theme="light"] .popup-actions a:hover {
        background: #374151;
        color: #ffffff;
    }

    [data-theme="light"] .status-badge.normal {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    [data-theme="light"] .status-badge.warning {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    [data-theme="light"] .status-badge.critical {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    /* Light theme map tiles */
    [data-theme="light"] #territory-map {
        filter: none;
    }
</style>
@endpush

@push('scripts')
@if($hasLocations)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize map
    const warehouses = @json($warehouses);
    const distributors = @json($distributors);

    // Calculate center point
    let allLats = [];
    let allLngs = [];

    warehouses.forEach(w => {
        allLats.push(parseFloat(w.latitude));
        allLngs.push(parseFloat(w.longitude));
    });

    distributors.forEach(d => {
        allLats.push(parseFloat(d.latitude));
        allLngs.push(parseFloat(d.longitude));
    });

    const centerLat = allLats.length > 0 ? allLats.reduce((a,b) => a+b) / allLats.length : 20.5937;
    const centerLng = allLngs.length > 0 ? allLngs.reduce((a,b) => a+b) / allLngs.length : 78.9629;

    const map = L.map('territory-map').setView([centerLat, centerLng], 5);

    // Theme-aware map tiles with dynamic switching
    let currentTileLayer = null;

    function getTileUrl() {
        const isLight = localStorage.getItem('admin-theme') === 'light';
        return isLight
            ? 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png'
            : 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
    }

    function updateMapTiles() {
        const newUrl = getTileUrl();
        if (currentTileLayer) {
            map.removeLayer(currentTileLayer);
        }
        currentTileLayer = L.tileLayer(newUrl, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            maxZoom: 19,
            subdomains: 'abcd'
        }).addTo(map);
    }

    // Initial tile load
    updateMapTiles();

    // Listen for theme toggle clicks and update tiles immediately
    document.addEventListener('themeChanged', function() {
        updateMapTiles();
    });

    // Create territory polygons using Voronoi-like approach
    // We'll create circles that represent service areas
    const warehouseMarkers = [];
    const territoryCircles = [];

    // Add warehouse markers with territory circles
    warehouses.forEach(warehouse => {
        const lat = parseFloat(warehouse.latitude);
        const lng = parseFloat(warehouse.longitude);

        // Create territory circle (50km radius)
        const circle = L.circle([lat, lng], {
            color: warehouse.color,
            fillColor: warehouse.fillColor,
            fillOpacity: 0.3,
            radius: 50000, // 50km in meters
            weight: 2,
            className: 'territory-circle'
        }).addTo(map);

        territoryCircles.push(circle);

        // Create warehouse marker
        const icon = L.divIcon({
            html: `
                <div style="
                    background: ${warehouse.color};
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 18px;
                    border: 3px solid white;
                    box-shadow: 0 2px 12px rgba(0,0,0,0.4);
                ">🏭</div>
            `,
            iconSize: [36, 36],
            popupAnchor: [0, -18],
            className: 'warehouse-marker'
        });

        const marker = L.marker([lat, lng], { icon }).addTo(map);
        warehouseMarkers.push(marker);

        // Build popup content
        const popupContent = `
            <div class="popup-content">
                <div class="popup-header">
                    <div class="popup-icon">🏭</div>
                    <div class="popup-title">
                        <h3>${escapeHtml(warehouse.name)}</h3>
                        <span>${escapeHtml(warehouse.code)}</span>
                    </div>
                </div>
                <div class="popup-body">
                    <div class="popup-row">
                        <span class="label">📍 Address</span>
                    </div>
                    <div style="font-size: 0.8125rem; color: #374151; margin-bottom: 0.5rem; font-weight: 500;">
                        ${escapeHtml(warehouse.address || 'N/A')}, ${escapeHtml(warehouse.city || 'N/A')}
                    </div>
                    ${warehouse.manager_name ? `
                    <div class="popup-row">
                        <span class="label">👤 Manager</span>
                        <span class="value">${escapeHtml(warehouse.manager_name)}</span>
                    </div>` : ''}
                    <div class="popup-row">
                        <span class="label">📦 Total Items</span>
                        <span class="value">${warehouse.total_items}</span>
                    </div>
                    <div class="popup-row">
                        <span class="label">⚠️ Low Stock</span>
                        <span class="value" style="color: ${warehouse.low_stock_count > 0 ? '#f87171' : '#34d399'}">${warehouse.low_stock_count}</span>
                    </div>
                    <div class="popup-row">
                        <span class="label">Status</span>
                        <span class="status-badge ${warehouse.status}">${warehouse.status}</span>
                    </div>
                </div>
                <div class="popup-actions">
                    <a href="/admin/warehouses/${warehouse.id}">View Details</a>
                    <a href="/admin/inventory?warehouse_id=${warehouse.id}">Inventory</a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);
    });

    // Add distributor markers
    const distributorMarkers = [];

    distributors.forEach(distributor => {
        const lat = parseFloat(distributor.latitude);
        const lng = parseFloat(distributor.longitude);

        const icon = L.divIcon({
            html: `
                <div style="
                    background: linear-gradient(135deg, #4f46e5, #818cf8);
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 14px;
                    border: 2px solid white;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
                ">🚚</div>
            `,
            iconSize: [32, 32],
            popupAnchor: [0, -16],
            className: 'distributor-marker'
        });

        const marker = L.marker([lat, lng], { icon }).addTo(map);
        distributorMarkers.push(marker);

        const popupContent = `
            <div class="popup-content">
                <div class="popup-header">
                    <div class="popup-icon distributor">🚚</div>
                    <div class="popup-title">
                        <h3>${escapeHtml(distributor.name)}</h3>
                        <span>${escapeHtml(distributor.contact_person || 'Distributor')}</span>
                    </div>
                </div>
                <div class="popup-body">
                    <div class="popup-row">
                        <span class="label">📍 Address</span>
                    </div>
                    <div style="font-size: 0.8125rem; color: #374151; margin-bottom: 0.5rem; font-weight: 500;">
                        ${escapeHtml(distributor.address || 'Address not available')}
                    </div>
                    ${distributor.phone ? `
                    <div class="popup-row">
                        <span class="label">📞 Phone</span>
                        <span class="value">${escapeHtml(distributor.phone)}</span>
                    </div>` : ''}
                    ${distributor.email ? `
                    <div class="popup-row">
                        <span class="label">✉️ Email</span>
                        <span class="value">${escapeHtml(distributor.email)}</span>
                    </div>` : ''}
                </div>
                <div class="popup-actions">
                    <a href="/admin/distributors/${distributor.id}">View Details</a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);
    });

    // Fit map to show all markers
    if (warehouseMarkers.length > 0 || distributorMarkers.length > 0) {
        const allMarkers = [...warehouseMarkers, ...distributorMarkers];
        const group = new L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.1));
    }

    // Add scale control
    L.control.scale({ metric: true, imperial: false, position: 'bottomleft' }).addTo(map);

    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endif
@endpush
