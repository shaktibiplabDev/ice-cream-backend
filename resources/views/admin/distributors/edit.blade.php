@extends('layouts.admin')

@section('title', 'Edit Distributor')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Edit Distributor: {{ $distributor->name }}</h3>
        </div>
        
        <form method="POST" action="{{ route('admin.distributors.update', $distributor->id) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Distributor Name *</label>
                <input type="text" name="name" value="{{ old('name', $distributor->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                <textarea name="address" id="address" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">{{ old('address', $distributor->address) }}</textarea>
                @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <!-- Map Style Selector -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Map Style</label>
                <select id="mapStyle" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                    <option value="positron">Light (Positron) - Clean & Modern</option>
                    <option value="dark_matter">Dark (Dark Matter) - Premium Look</option>
                    <option value="voyager">Voyager - Detailed Roads</option>
                    <option value="osm">OpenStreetMap - Standard</option>
                </select>
            </div>
            
            <!-- Map Section -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pin Location on Map *</label>
                <div id="map" style="height: 500px; width: 100%; border-radius: 0.5rem; z-index: 1;" class="border border-gray-300"></div>
                <p class="text-sm text-gray-500 mt-2">💡 Click on the map to update coordinates</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitude *</label>
                    <input type="number" step="any" name="latitude" id="latitude" required readonly value="{{ old('latitude', $distributor->latitude) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:border-purple-500">
                    @error('latitude') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitude *</label>
                    <input type="number" step="any" name="longitude" id="longitude" required readonly value="{{ old('longitude', $distributor->longitude) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:border-purple-500">
                    @error('longitude') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <!-- Search Address -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Address</label>
                <div class="flex gap-2">
                    <input type="text" id="searchAddress" placeholder="Enter address to search..." class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                    <button type="button" id="searchBtn" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Search</button>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">🎨 CartoDB Map Styles:</span>
                    </p>
                    <ul class="text-xs text-gray-600 mt-2 space-y-1">
                        <li>• <strong>Positron</strong> - Light background, perfect for data visualization</li>
                        <li>• <strong>Dark Matter</strong> - Dark premium theme, great for night mode</li>
                        <li>• <strong>Voyager</strong> - Balanced style with emphasis on roads</li>
                        <li>• <strong>OpenStreetMap</strong> - Standard OSM map tiles</li>
                    </ul>
                    <p class="text-xs text-gray-500 mt-2">
                        Map data © <a href="https://www.openstreetmap.org/copyright" target="_blank" class="text-purple-600">OpenStreetMap</a> contributors, 
                        CartoDB attribution required
                    </p>
                </div>
            </div>
            
            <div class="flex justify-end">
                <a href="{{ route('admin.distributors.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 mr-2">Cancel</a>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Update Distributor</button>
            </div>
        </form>
    </div>

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        /* Custom marker animation */
        .custom-marker {
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
            transition: all 0.3s ease;
        }
        .custom-marker:hover {
            transform: scale(1.1);
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.4));
        }
        
        /* Map control styling */
        .leaflet-control-attribution {
            font-size: 9px;
            background-color: rgba(255,255,255,0.8);
            padding: 2px 5px;
            border-radius: 3px;
        }
        
        /* Custom zoom control styling */
        .leaflet-control-zoom a {
            background-color: white;
            color: #7c3aed;
            font-weight: bold;
        }
        
        .leaflet-control-zoom a:hover {
            background-color: #7c3aed;
            color: white;
        }
        
        /* Current location button styling */
        .leaflet-control-current {
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.65);
            cursor: pointer;
        }
        .leaflet-control-current:hover {
            background-color: #f0f0f0;
        }
    </style>
    
    <script>
        // Map configuration
        let map;
        let marker;
        let currentTileLayer;
        
        // Get current coordinates from existing distributor
        var currentLat = parseFloat("{{ $distributor->latitude }}");
        var currentLng = parseFloat("{{ $distributor->longitude }}");
        var distributorName = "{{ $distributor->name }}";
        var distributorAddress = "{{ $distributor->address }}";
        
        // Style URLs for different map providers
        const mapStyles = {
            positron: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
            dark_matter: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
            voyager: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
            osm: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
        };
        
        const mapAttributions = {
            positron: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            dark_matter: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            voyager: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            osm: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        };
        
        // Custom Celesty marker icon
        function getCelestyIcon() {
            return L.divIcon({
                className: 'custom-marker',
                html: `<div style="
                    background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 3px solid white;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
                    transition: all 0.3s ease;
                    cursor: pointer;
                ">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white" stroke="none">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                    </svg>
                </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
        }
        
        // Initialize map
        function initMap(style = 'positron') {
            if (map) {
                map.remove();
            }
            
            // Create map centered on existing distributor location
            map = L.map('map').setView([currentLat, currentLng], 13);
            
            // Add tile layer with selected style
            currentTileLayer = L.tileLayer(mapStyles[style], {
                attribution: mapAttributions[style],
                maxZoom: 19,
                minZoom: 3
            }).addTo(map);
            
            // Add scale control
            L.control.scale({
                metric: true,
                imperial: false,
                position: 'bottomleft'
            }).addTo(map);
            
            // Add existing marker with custom icon
            marker = L.marker([currentLat, currentLng], { icon: getCelestyIcon() }).addTo(map);
            marker.bindPopup(`
                <div class="text-center">
                    <strong class="text-rose-600 text-base">${distributorName}</strong><br>
                    <span class="text-sm text-gray-600">${distributorAddress}</span><br>
                    <hr class="my-2">
                    <span class="text-xs text-gray-500">📍 ${currentLat.toFixed(6)}, ${currentLng.toFixed(6)}</span>
                </div>
            `).openPopup();
            
            // Handle map click to update marker and coordinates
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                
                // Remove existing marker
                if (marker) {
                    map.removeLayer(marker);
                }
                
                // Add new marker with custom icon
                marker = L.marker([lat, lng], { icon: getCelestyIcon() }).addTo(map);
                
                // Update form fields
                document.getElementById('latitude').value = lat.toFixed(7);
                document.getElementById('longitude').value = lng.toFixed(7);
                
                // Add popup
                marker.bindPopup(`
                    <div class="text-center">
                        <strong class="text-rose-600">Updated Location</strong><br>
                        <span class="text-sm">Lat: ${lat.toFixed(6)}</span><br>
                        <span class="text-sm">Lng: ${lng.toFixed(6)}</span>
                    </div>
                `).openPopup();
                
                // Update address using reverse geocoding
                updateAddressFromCoords(lat, lng);
            });
        }
        
        // Change map style dynamically
        function changeMapStyle(style) {
            if (currentTileLayer) {
                map.removeLayer(currentTileLayer);
            }
            
            currentTileLayer = L.tileLayer(mapStyles[style], {
                attribution: mapAttributions[style],
                maxZoom: 19,
                minZoom: 3
            }).addTo(map);
        }
        
        // Update address from coordinates
        function updateAddressFromCoords(lat, lng) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        const addressField = document.getElementById('address');
                        if (confirm('Update address to: ' + data.display_name.substring(0, 100) + '...?')) {
                            addressField.value = data.display_name;
                        }
                    }
                })
                .catch(error => console.error('Reverse geocoding error:', error));
        }
        
        // Search address functionality
        function searchAddress() {
            const address = document.getElementById('searchAddress').value;
            if (!address) {
                alert('Please enter an address to search');
                return;
            }
            
            const searchBtn = document.getElementById('searchBtn');
            searchBtn.textContent = 'Searching...';
            searchBtn.disabled = true;
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        const displayName = data[0].display_name;
                        
                        map.setView([lat, lng], 15);
                        
                        if (marker) {
                            map.removeLayer(marker);
                        }
                        
                        marker = L.marker([lat, lng], { icon: getCelestyIcon() }).addTo(map);
                        marker.bindPopup(`<b>${displayName}</b>`).openPopup();
                        document.getElementById('latitude').value = lat.toFixed(7);
                        document.getElementById('longitude').value = lng.toFixed(7);
                        document.getElementById('address').value = displayName;
                    } else {
                        alert('Address not found. Please try a different search term.');
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    alert('Error searching address. Please try again.');
                })
                .finally(() => {
                    searchBtn.textContent = 'Search';
                    searchBtn.disabled = false;
                });
        }
        
        // Add current location button
        function addCurrentLocationButton() {
            const currentLocationControl = L.Control.extend({
                options: { position: 'topleft' },
                onAdd: function(map) {
                    const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control-current');
                    container.style.width = '34px';
                    container.style.height = '34px';
                    container.style.display = 'flex';
                    container.style.alignItems = 'center';
                    container.style.justifyContent = 'center';
                    container.style.fontSize = '20px';
                    container.innerHTML = '📍';
                    container.title = 'Go to my location';
                    
                    container.onclick = function() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                const lat = position.coords.latitude;
                                const lng = position.coords.longitude;
                                map.setView([lat, lng], 15);
                                
                                if (marker) {
                                    map.removeLayer(marker);
                                }
                                
                                marker = L.marker([lat, lng], { icon: getCelestyIcon() }).addTo(map);
                                document.getElementById('latitude').value = lat.toFixed(7);
                                document.getElementById('longitude').value = lng.toFixed(7);
                                marker.bindPopup('<b>Your Location</b>').openPopup();
                                updateAddressFromCoords(lat, lng);
                            }, function() {
                                alert('Could not get your location. Please ensure location services are enabled.');
                            });
                        } else {
                            alert('Geolocation is not supported by this browser');
                        }
                    };
                    
                    return container;
                }
            });
            
            map.addControl(new currentLocationControl());
        }
        
        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap('positron');
            addCurrentLocationButton();
            
            // Map style change handler
            document.getElementById('mapStyle').addEventListener('change', function(e) {
                changeMapStyle(e.target.value);
            });
            
            // Search button handler
            document.getElementById('searchBtn').addEventListener('click', searchAddress);
            
            // Enter key support for search
            document.getElementById('searchAddress').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchAddress();
                }
            });
        });
    </script>
@endsection