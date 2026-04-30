<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Show inventory for a specific distributor
    public function index(Request $request)
    {
        $distributorId = $request->get('distributor_id');
        $distributors = Distributor::active()->get();

        $inventory = collect();
        $selectedDistributor = null;

        if ($distributorId) {
            $selectedDistributor = Distributor::findOrFail($distributorId);
            $inventory = Inventory::with('product')
                ->where('distributor_id', $distributorId)
                ->latest()
                ->paginate(20);
        }

        return view('admin.inventory.index', compact('inventory', 'distributors', 'selectedDistributor'));
    }

    // Show form to add stock
    public function create()
    {
        $distributors = Distributor::active()->get();
        $products = Product::active()->get();
        return view('admin.inventory.create', compact('distributors', 'products'));
    }

    // Add stock to distributor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $inventory = Inventory::firstOrCreate(
            [
                'distributor_id' => $validated['distributor_id'],
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

        return redirect()->route('admin.inventory.index', ['distributor_id' => $validated['distributor_id']])
            ->with('success', 'Stock added successfully');
    }

    // Show stock movement form (add/remove/adjust)
    public function edit($id)
    {
        $inventory = Inventory::with(['distributor', 'product'])->findOrFail($id);
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

        return redirect()->route('admin.inventory.index', ['distributor_id' => $inventory->distributor_id])
            ->with('success', $message);
    }

    // Show stock movement history
    public function history(Request $request)
    {
        $distributorId = $request->get('distributor_id');
        $productId = $request->get('product_id');

        $query = \App\Models\StockMovement::with(['distributor', 'product', 'creator'])
            ->latest();

        if ($distributorId) {
            $query->where('distributor_id', $distributorId);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $movements = $query->paginate(30);
        $distributors = Distributor::active()->get();
        $products = Product::active()->get();

        return view('admin.inventory.history', compact('movements', 'distributors', 'products'));
    }

    // Low stock alerts
    public function lowStock()
    {
        $lowStockItems = Inventory::with(['distributor', 'product'])
            ->get()
            ->filter(fn ($inv) => $inv->isLowStock());

        return view('admin.inventory.low-stock', compact('lowStockItems'));
    }
}
