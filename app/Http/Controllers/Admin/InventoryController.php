<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Show inventory for a specific warehouse
    public function index(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $warehouses = Warehouse::where('is_active', true)->get();

        $inventory = collect();
        $selectedWarehouse = null;

        if ($warehouseId) {
            $selectedWarehouse = Warehouse::findOrFail($warehouseId);
            $inventory = Inventory::with('product')
                ->where('warehouse_id', $warehouseId)
                ->latest()
                ->paginate(20);
        }

        return view('admin.inventory.index', compact('inventory', 'warehouses', 'selectedWarehouse'));
    }

    // Show form to add stock
    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('admin.inventory.create', compact('warehouses', 'products'));
    }

    // Add stock to warehouse
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $inventory = Inventory::firstOrCreate(
            [
                'warehouse_id' => $validated['warehouse_id'],
                'product_id' => $validated['product_id'],
            ],
            [
                'quantity' => 0,
                'location' => $validated['location'] ?? null,
            ]
        );

        $inventory->addStock(
            $validated['quantity'],
            'manual',
            null,
            $validated['notes'] ?? 'Initial stock entry',
            auth('admin')->id()
        );

        return redirect()->route('admin.inventory.index', ['warehouse_id' => $validated['warehouse_id']])
            ->with('success', 'Stock added successfully');
    }

    // Show stock movement form (add/remove/adjust)
    public function edit($id)
    {
        $inventory = Inventory::with(['warehouse', 'product'])->findOrFail($id);
        return view('admin.inventory.edit', compact('inventory'));
    }

    // Handle stock operations
    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $validated = $request->validate([
            'operation' => 'required|in:add,remove,adjust',
            'quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $notes = $validated['notes'] ?? null;
        $adminId = auth('admin')->id();

        switch ($validated['operation']) {
            case 'add':
                $inventory->addStock($validated['quantity'], 'manual', null, $notes, $adminId);
                $message = 'Stock added successfully';
                break;

            case 'remove':
                $result = $inventory->removeStock($validated['quantity'], 'manual', null, $notes, $adminId);
                if (!$result) {
                    return back()->with('error', 'Insufficient stock available');
                }
                $message = 'Stock removed successfully';
                break;

            case 'adjust':
                $inventory->adjustStock($validated['quantity'], $notes, $adminId);
                $message = 'Stock adjusted successfully';
                break;
        }

        return redirect()->route('admin.inventory.index', ['warehouse_id' => $inventory->warehouse_id])
            ->with('success', $message);
    }

    // Show stock movement history
    public function history(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $productId = $request->get('product_id');

        $query = \App\Models\StockMovement::with(['warehouse', 'product', 'creator'])
            ->latest();

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $movements = $query->paginate(30);
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('admin.inventory.history', compact('movements', 'warehouses', 'products'));
    }

    // Low stock alerts
    public function lowStock()
    {
        $lowStockItems = Inventory::with(['warehouse', 'product'])
            ->get()
            ->filter(fn ($inv) => $inv->isLowStock());

        return view('admin.inventory.low-stock', compact('lowStockItems'));
    }
}
