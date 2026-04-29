@extends('layouts.admin')

@section('title', 'Add Distributor')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Add New Distributor</h3>
        </div>
        
        <form method="POST" action="{{ route('admin.distributors.store') }}" class="p-6" id="distributorForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="border-b pb-4 mb-4">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Basic Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Distributor Name *</label>
                        <input type="text" name="name" id="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                        @error('contact_person') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="border-b pb-4 mb-4">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Contact Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" id="phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                        @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="url" name="website" id="website" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500" placeholder="https://">
                        @error('website') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Social Media</label>
                        <input type="text" name="social_media" id="social_media" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500" placeholder="Instagram/Facebook/LinkedIn">
                        @error('social_media') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            
            <!-- Business Details -->
            <div class="border-b pb-4 mb-4">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Business Details</h4>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                    <textarea name="address" id="address" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500"></textarea>
                    @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service Area</label>
                        <input type="text" name="service_area" id="service_area" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500" placeholder="e.g., Mumbai, Pune, Navi Mumbai">
                        @error('service_area') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Capacity</label>
                        <select name="delivery_capacity" id="delivery_capacity" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                            <option value="">Select capacity</option>
                            <option value="up_to_500">Up to 500 units/day</option>
                            <option value="500_to_1000">500 - 1000 units/day</option>
                            <option value="1000_to_5000">1000 - 5000 units/day</option>
                            <option value="5000_plus">5000+ units/day</option>
                        </select>
                        @error('delivery_capacity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Hours</label>
                        <input type="text" name="timings" id="timings" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500" placeholder="Mon-Fri: 9 AM - 7 PM">
                        @error('timings') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="is_active" id="is_active" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('is_active') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description / Notes</label>
                    <textarea name="description" id="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500" placeholder="Additional information about the distributor..."></textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <!-- Map Section -->
            <div class="mb-4">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Location on Map</h4>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Map Style</label>
                    <select id="mapStyle" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500">
                        <option value="positron">Light (Positron) - Clean & Modern</option>
                        <option value="dark_matter">Dark (Dark Matter) - Premium Look</option>
                        <option value="voyager">Voyager - Detailed Roads</option>
                        <option value="osm">OpenStreetMap - Standard</option>
                    </select>
                </div>
                
                <div id="map" style="height: 400px; width: 100%; border-radius: 0.5rem; z-index: 1;" class="border border-gray-300"></div>
                <p class="text-sm text-gray-500 mt-2">💡 Click on the map to set coordinates</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitude *</label>
                    <input type="number" step="any" name="latitude" id="latitude" required readonly class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:border-purple-500" placeholder="Click on map">
                    @error('latitude') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitude *</label>
                    <input type="number" step="any" name="longitude" id="longitude" required readonly class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:border-purple-500" placeholder="Click on map">
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
                </div>
            </div>
            
            <div class="flex justify-end">
                <a href="{{ route('admin.distributors.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 mr-2">Cancel</a>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Add Distributor</button>
            </div>
        </form>
    </div>

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Map configuration (keep the same map script from previous version)
        let map;
        let marker;
        let currentTileLayer;
        
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
        
        function initMap(style = 'positron') {
            if (map) map.remove();
            map = L.map('map').setView([20.5937, 78.9629], 5);
            
            currentTileLayer = L.tileLayer(mapStyles[style], {
                attribution: mapAttributions[style],
                maxZoom: 19
            }).addTo(map);
            
            L.control.scale({ metric: true, imperial: false, position: 'bottomleft' }).addTo(map);
            
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                
                if (marker) map.removeLayer(marker);
                marker = L.marker([lat, lng]).addTo(map);
                
                document.getElementById('latitude').value = lat.toFixed(7);
                document.getElementById('longitude').value = lng.toFixed(7);
                
                marker.bindPopup(`<b>Selected Location</b><br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`).openPopup();
            });
        }
        
        function changeMapStyle(style) {
            if (currentTileLayer) map.removeLayer(currentTileLayer);
            currentTileLayer = L.tileLayer(mapStyles[style], {
                attribution: mapAttributions[style],
                maxZoom: 19
            }).addTo(map);
        }
        
        document.getElementById('mapStyle').addEventListener('change', function(e) {
            changeMapStyle(e.target.value);
        });
        
        document.getElementById('searchBtn').addEventListener('click', function() {
            const address = document.getElementById('searchAddress').value;
            if (!address) return alert('Please enter an address');
            
            this.textContent = 'Searching...';
            this.disabled = true;
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        map.setView([lat, lng], 15);
                        if (marker) map.removeLayer(marker);
                        marker = L.marker([lat, lng]).addTo(map);
                        document.getElementById('latitude').value = lat.toFixed(7);
                        document.getElementById('longitude').value = lng.toFixed(7);
                        document.getElementById('address').value = data[0].display_name;
                    } else {
                        alert('Address not found');
                    }
                })
                .catch(error => alert('Error searching address'))
                .finally(() => {
                    this.textContent = 'Search';
                    this.disabled = false;
                });
        });
        
        initMap('positron');
    </script>
@endsection