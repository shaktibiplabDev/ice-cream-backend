<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Product;

class MapController extends Controller
{
    public function index()
    {
        // Get all distributors with coordinates
        $distributors = Distributor::select(['id', 'name', 'business_name', 'latitude', 'longitude', 'address', 'city', 'state', 'phone', 'email'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('is_active', true)
            ->get();

        // Get all warehouses with coordinates and their inventory
        $warehouses = Warehouse::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['inventory.product'])
            ->get();

        // Calculate stock status for each warehouse
        $warehouseData = $warehouses->map(function ($warehouse) {
            $totalItems = $warehouse->inventory->count();
            $lowStockCount = 0;
            $nearLowCount = 0;

            foreach ($warehouse->inventory as $inv) {
                if ($inv->product && $inv->isLowStock()) {
                    $lowStockCount++;
                } elseif ($inv->product && $inv->quantity <= ($inv->product->low_stock_threshold * 1.5)) {
                    $nearLowCount++;
                }
            }

            // Determine color based on stock status
            if ($totalItems > 0 && $lowStockCount > 0) {
                $status = 'critical'; // Red
                $color = '#ef4444';
                $fillColor = 'rgba(239, 68, 68, 0.3)';
            } elseif ($totalItems > 0 && $nearLowCount > 0) {
                $status = 'warning'; // Yellow
                $color = '#f59e0b';
                $fillColor = 'rgba(245, 158, 11, 0.3)';
            } else {
                $status = 'normal'; // Green
                $color = '#10b981';
                $fillColor = 'rgba(16, 185, 129, 0.3)';
            }

            return [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'code' => $warehouse->code,
                'latitude' => $warehouse->latitude,
                'longitude' => $warehouse->longitude,
                'address' => $warehouse->address,
                'city' => $warehouse->city,
                'state' => $warehouse->state,
                'manager_name' => $warehouse->manager_name,
                'phone' => $warehouse->phone,
                'email' => $warehouse->email,
                'status' => $status,
                'color' => $color,
                'fillColor' => $fillColor,
                'total_items' => $totalItems,
                'low_stock_count' => $lowStockCount,
                'near_low_count' => $nearLowCount,
                'is_active' => $warehouse->is_active,
            ];
        });

        // Format distributors for map
        $distributorData = $distributors->map(function ($distributor) {
            return [
                'id' => $distributor->id,
                'name' => $distributor->name,
                'business_name' => $distributor->business_name,
                'latitude' => $distributor->latitude,
                'longitude' => $distributor->longitude,
                'address' => $distributor->address,
                'city' => $distributor->city,
                'state' => $distributor->state,
                'phone' => $distributor->phone,
                'email' => $distributor->email,
                'type' => 'distributor',
            ];
        });

        return view('admin.map.index', [
            'warehouses' => $warehouseData,
            'distributors' => $distributorData,
            'hasLocations' => $warehouseData->count() > 0 || $distributorData->count() > 0,
        ]);
    }
}
