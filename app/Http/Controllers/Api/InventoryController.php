<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Get all products with optional filtering
    public function products(Request $request)
    {
        $query = Product::where('is_active', true);

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->get('search') . '%');
        }

        // Get unique categories for filtering
        $categories = Product::where('is_active', true)->distinct()->pluck('category');

        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'categories' => $categories,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    // Get single product details
    public function product($id)
    {
        $product = Product::findOrFail($id);

        // Get stock at all distributors
        $stock = $product->inventory()
            ->with('distributor')
            ->whereHas('distributor', function ($q) {
                $q->where('is_active', true);
            })
            ->get()
            ->map(function ($inv) {
                return [
                    'distributor_id' => $inv->distributor_id,
                    'distributor_name' => $inv->distributor->name,
                    'distributor_address' => $inv->distributor->address,
                    'latitude' => $inv->distributor->latitude,
                    'longitude' => $inv->distributor->longitude,
                    'quantity' => $inv->quantity,
                    'available' => $inv->availableQuantity(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'stock_at_distributors' => $stock,
            ],
        ]);
    }

    // Get inventory for a specific distributor
    public function distributorInventory($distributorId)
    {
        $distributor = Distributor::with(['inventory.product' => function ($query) {
            $query->where('is_active', true);
        }])->findOrFail($distributorId);

        $inventory = $distributor->inventory->map(function ($inv) {
            return [
                'product_id' => $inv->product_id,
                'product_name' => $inv->product->name,
                'product_sku' => $inv->product->sku,
                'category' => $inv->product->category,
                'size' => $inv->product->size,
                'price' => $inv->product->price,
                'unit' => $inv->product->unit,
                'image' => $inv->product->image ? asset('storage/' . $inv->product->image) : null,
                'quantity' => $inv->quantity,
                'available' => $inv->availableQuantity(),
                'is_low_stock' => $inv->isLowStock(),
            ];
        })->filter(function ($item) {
            return $item['quantity'] > 0;
        })->values();

        return response()->json([
            'success' => true,
            'distributor' => [
                'id' => $distributor->id,
                'name' => $distributor->name,
                'address' => $distributor->address,
                'phone' => $distributor->phone,
                'latitude' => $distributor->latitude,
                'longitude' => $distributor->longitude,
            ],
            'inventory' => $inventory,
        ]);
    }

    // Check stock availability at nearby distributors
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
            'quantity' => 'nullable|numeric|min:0',
        ]);

        $productId = $request->get('product_id');
        $lat = $request->get('latitude');
        $lng = $request->get('longitude');
        $radius = $request->get('radius', 10);
        $requiredQty = $request->get('quantity', 1);

        $product = Product::findOrFail($productId);

        // Find distributors with stock within radius
        $availableDistributors = \App\Models\Inventory::with('distributor')
            ->where('product_id', $productId)
            ->whereRaw("(
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(distributors.latitude)) *
                    cos(radians(distributors.longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(distributors.latitude))
                )
            ) <= ?", [$lat, $lng, $lat, $radius])
            ->join('distributors', 'inventory.distributor_id', '=', 'distributors.id')
            ->where('distributors.is_active', true)
            ->where('inventory.quantity', '>=', $requiredQty)
            ->select('inventory.*')
            ->orderByRaw("(
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(distributors.latitude)) *
                    cos(radians(distributors.longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(distributors.latitude))
                )
            )", [$lat, $lng, $lat])
            ->get()
            ->map(function ($inv) use ($product) {
                return [
                    'distributor_id' => $inv->distributor_id,
                    'distributor_name' => $inv->distributor->name,
                    'distributor_address' => $inv->distributor->address,
                    'phone' => $inv->distributor->phone,
                    'latitude' => $inv->distributor->latitude,
                    'longitude' => $inv->distributor->longitude,
                    'available_quantity' => $inv->availableQuantity(),
                    'product_price' => $product->price,
                ];
            });

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'unit' => $product->unit,
            ],
            'requested_quantity' => $requiredQty,
            'nearby_distributors_with_stock' => $availableDistributors,
            'total_available' => $availableDistributors->sum('available_quantity'),
        ]);
    }

    // Get all categories
    public function categories()
    {
        $categories = Product::where('is_active', true)
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
