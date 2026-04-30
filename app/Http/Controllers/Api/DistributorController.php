<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    // Get distributors with pagination, filtering, and location search
    public function index(Request $request)
    {
        $query = Distributor::where('is_active', true);

        // Search by name or address
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('service_area', 'like', "%{$search}%");
            });
        }

        // Filter by service area
        if ($request->has('service_area')) {
            $query->where('service_area', 'like', "%{$request->get('service_area')}%");
        }

        // Location-based search within radius (km)
        if ($request->has(['latitude', 'longitude', 'radius'])) {
            $lat = $request->get('latitude');
            $lng = $request->get('longitude');
            $radius = $request->get('radius', 10); // default 10km

            // Haversine formula for distance calculation
            $query->selectRaw("*, (
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )
            ) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
        } else {
            $query->latest();
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $distributors = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $distributors->items(),
            'meta' => [
                'current_page' => $distributors->currentPage(),
                'last_page' => $distributors->lastPage(),
                'per_page' => $distributors->perPage(),
                'total' => $distributors->total(),
            ],
            'links' => [
                'first' => $distributors->url(1),
                'last' => $distributors->url($distributors->lastPage()),
                'prev' => $distributors->previousPageUrl(),
                'next' => $distributors->nextPageUrl(),
            ],
        ]);
    }

    // Get single distributor with inventory
    public function show($id)
    {
        $distributor = Distributor::with(['inventory.product' => function ($query) {
            $query->where('is_active', true);
        }])->findOrFail($id);

        // Filter only in-stock items
        $distributor->inventory = $distributor->inventory->filter(function ($inv) {
            return $inv->availableQuantity() > 0;
        })->values();

        return response()->json([
            'success' => true,
            'data' => $distributor,
        ]);
    }

    // Get nearby distributors based on location
    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
        ]);

        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        $radius = $request->get('radius', 10);

        $distributors = Distributor::selectRaw("*, (
            6371 * acos(
                cos(radians(?)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(latitude))
            )
        ) AS distance", [$lat, $lng, $lat])
        ->where('is_active', true)
        ->having('distance', '<=', $radius)
        ->orderBy('distance')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $distributors,
            'search_params' => [
                'latitude' => $lat,
                'longitude' => $lng,
                'radius_km' => $radius,
            ],
        ]);
    }
}
