@extends('layouts.admin')

@section('title', 'Distributor Details')

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Distributor Details: {{ $distributor->name }}</h3>
            <div>
                <a href="{{ route('admin.distributors.edit', $distributor->id) }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 mr-2">Edit</a>
                <a href="{{ route('admin.distributors.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Back</a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Basic Information</h4>
                    <div class="space-y-2">
                        <p><span class="font-medium text-gray-600">Name:</span> {{ $distributor->name }}</p>
                        <p><span class="font-medium text-gray-600">Contact Person:</span> {{ $distributor->contact_person ?? 'N/A' }}</p>
                        <p><span class="font-medium text-gray-600">Address:</span> {{ $distributor->address }}</p>
                        <p><span class="font-medium text-gray-600">Status:</span> 
                            <span class="px-2 py-1 text-xs rounded-full {{ $distributor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $distributor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Contact Information</h4>
                    <div class="space-y-2">
                        <p><span class="font-medium text-gray-600">Phone:</span> {{ $distributor->phone ?? 'N/A' }}</p>
                        <p><span class="font-medium text-gray-600">Email:</span> {{ $distributor->email ?? 'N/A' }}</p>
                        <p><span class="font-medium text-gray-600">Website:</span> {{ $distributor->website ?? 'N/A' }}</p>
                        <p><span class="font-medium text-gray-600">Social Media:</span> {{ $distributor->social_media ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <!-- Business Details -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Business Details</h4>
                    <div class="space-y-2">
                        <p><span class="font-medium text-gray-600">Service Area:</span> {{ $distributor->service_area ?? 'N/A' }}</p>
                        <p><span class="font-medium text-gray-600">Delivery Capacity:</span> {{ ucwords(str_replace('_', ' ', $distributor->delivery_capacity ?? 'N/A')) }}</p>
                        <p><span class="font-medium text-gray-600">Business Hours:</span> {{ $distributor->timings ?? 'N/A' }}</p>
                        <p><span class="font-medium text-gray-600">Description:</span></p>
                        <p class="text-gray-600">{{ $distributor->description ?? 'No description provided.' }}</p>
                    </div>
                </div>
                
                <!-- Location Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Location Information</h4>
                    <div class="space-y-2">
                        <p><span class="font-medium text-gray-600">Latitude:</span> {{ $distributor->latitude }}</p>
                        <p><span class="font-medium text-gray-600">Longitude:</span> {{ $distributor->longitude }}</p>
                        <p><span class="font-medium text-gray-600">Map Link:</span> 
                            <a href="https://www.google.com/maps?q={{ $distributor->latitude }},{{ $distributor->longitude }}" target="_blank" class="text-purple-600 hover:underline">
                                View on Google Maps →
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Map Preview -->
            <div class="mt-6">
                <h4 class="font-semibold text-gray-700 mb-3">Location Map</h4>
                <div id="map" style="height: 400px; width: 100%; border-radius: 0.5rem;"></div>
            </div>
        </div>
    </div>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        var map = L.map('map').setView([{{ $distributor->latitude }}, {{ $distributor->longitude }}], 13);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            maxZoom: 19
        }).addTo(map);
        
        var marker = L.marker([{{ $distributor->latitude }}, {{ $distributor->longitude }}]).addTo(map);
        marker.bindPopup('<b>{{ $distributor->name }}</b><br>{{ $distributor->address }}').openPopup();
    </script>
@endsection