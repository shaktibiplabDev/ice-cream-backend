<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Show POS interface
     */
    public function index()
    {
        $distributors = Distributor::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $companySettings = CompanySetting::getSettings();

        return view('admin.pos.index', compact('distributors', 'products', 'warehouses', 'companySettings'));
    }

    /**
     * Get nearest warehouse for a distributor
     */
    public function nearestWarehouse(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
        ]);

        $distributor = Distributor::findOrFail($request->distributor_id);

        if (!$distributor->latitude || !$distributor->longitude) {
            // Return first active warehouse if distributor has no coordinates
            $warehouse = Warehouse::where('is_active', true)->first();
            return response()->json([
                'warehouse' => $warehouse,
                'distance' => null,
                'message' => 'Distributor has no location, using default warehouse',
            ]);
        }

        $nearest = $this->calculateNearestWarehouse(
            $distributor->latitude,
            $distributor->longitude
        );

        return response()->json($nearest);
    }

    /**
     * Get available inventory for a product at a warehouse
     */
    public function checkInventory(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'distributor_id' => 'nullable|exists:distributors,id',
        ]);

        $inventory = Inventory::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->first();

        $product = Product::find($request->product_id);
        $distributor = $request->distributor_id ? Distributor::find($request->distributor_id) : null;
        $price = $product->getPriceForDistributor($distributor);

        return response()->json([
            'available' => $inventory ? $inventory->quantity : 0,
            'reserved' => $inventory ? $inventory->reserved_quantity : 0,
            'available_for_sale' => $inventory ? $inventory->quantity - $inventory->reserved_quantity : 0,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'mrp_price' => $product->mrp_price,
                'distributor_price' => $product->distributor_price,
                'retailer_price' => $product->retailer_price,
                'price' => $price, // Use distributor-specific price
                'unit' => $product->unit,
                'image' => $product->getImageUrl(),
            ],
            'distributor_discount' => $distributor ? $distributor->discount_percentage : null,
        ]);
    }

    /**
     * Process a sale
     */
    public function store(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,credit,upi,bank_transfer,cheque',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Verify stock availability
            foreach ($request->items as $item) {
                $inventory = Inventory::where('warehouse_id', $request->warehouse_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                $available = $inventory ? $inventory->quantity - $inventory->reserved_quantity : 0;

                if ($available < $item['quantity']) {
                    throw new \Exception('Insufficient stock for product: ' . Product::find($item['product_id'])->name);
                }
            }

            // Get company settings for GST
            $companySettings = CompanySetting::getSettings();

            // Calculate totals
            $subtotal = 0;
            $totalDiscount = 0;

            foreach ($request->items as $item) {
                $qty = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $discountPercent = $item['discount_percent'] ?? 0;

                $itemSubtotal = $qty * $unitPrice;
                $itemDiscount = $itemSubtotal * ($discountPercent / 100);

                $subtotal += $itemSubtotal;
                $totalDiscount += $itemDiscount;
            }

            $discountAmount = $request->discount_amount ?? 0;
            $taxableAmount = $subtotal - $totalDiscount - $discountAmount;

            // Calculate GST based on company settings
            $cgst = 0;
            $sgst = 0;
            $igst = 0;
            $totalTax = 0;

            if ($companySettings->isGstEnabled()) {
                if ($companySettings->isB2B()) {
                    $igst = $taxableAmount * ($companySettings->igst_percentage / 100);
                    $totalTax = $igst;
                } else {
                    $cgst = $taxableAmount * ($companySettings->cgst_percentage / 100);
                    $sgst = $taxableAmount * ($companySettings->sgst_percentage / 100);
                    $totalTax = $cgst + $sgst;
                }
            }

            $totalAmount = $taxableAmount + $totalTax;

            // Create sale
            $sale = Sale::create([
                'distributor_id' => $request->distributor_id,
                'warehouse_id' => $request->warehouse_id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'sale_date' => now(),
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => $igst,
                'discount_amount' => $totalDiscount + $discountAmount,
                'total_amount' => max(0, $totalAmount),
                'status' => 'completed',
                'payment_status' => $request->payment_method === 'credit' ? 'pending' : 'paid',
                'payment_method' => $request->payment_method ?? 'cash',
                'notes' => $request->notes,
                'created_by' => auth('admin')->id(),
            ]);

            // Create sale items and deduct inventory
            foreach ($request->items as $item) {
                $qty = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $discountPercent = $item['discount_percent'] ?? 0;
                $taxPercent = $item['tax_percent'] ?? 0;

                $itemSubtotal = $qty * $unitPrice;
                $itemDiscount = $itemSubtotal * ($discountPercent / 100);
                $itemTaxable = $itemSubtotal - $itemDiscount;
                $itemTax = $itemTaxable * ($taxPercent / 100);
                $itemTotal = $itemTaxable + $itemTax;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $itemDiscount,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $itemTax,
                    'total_price' => $itemTotal,
                ]);

                // Deduct from inventory
                $inventory = Inventory::where('warehouse_id', $request->warehouse_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($inventory) {
                    $quantityBefore = $inventory->quantity;
                    $inventory->quantity -= $qty;
                    $inventory->last_stock_check = now();
                    $inventory->save();

                    // Create stock movement
                    StockMovement::create([
                        'warehouse_id' => $request->warehouse_id,
                        'product_id' => $item['product_id'],
                        'type' => 'out',
                        'quantity' => $qty,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $inventory->quantity,
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'notes' => 'Sale to distributor: ' . $sale->distributor->name,
                        'created_by' => auth('admin')->id(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => $sale->load(['items.product', 'distributor', 'warehouse']),
                'redirect_url' => route('admin.pos.bill', $sale->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show bill/invoice
     */
    public function bill(Sale $sale)
    {
        $sale->load(['items.product', 'distributor', 'warehouse', 'creator']);

        return view('admin.pos.bill', compact('sale'));
    }

    /**
     * List all sales
     */
    public function history()
    {
        $sales = Sale::with(['distributor', 'warehouse'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.pos.history', compact('sales'));
    }

    /**
     * Calculate nearest warehouse using Haversine formula
     */
    private function calculateNearestWarehouse($lat, $lng)
    {
        $warehouses = Warehouse::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('is_active', true)
            ->get();

        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($warehouses as $warehouse) {
            $distance = $this->haversineDistance(
                $lat,
                $lng,
                $warehouse->latitude,
                $warehouse->longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $warehouse;
            }
        }

        return [
            'warehouse' => $nearest,
            'distance' => $nearest ? round($minDistance, 2) : null,
            'distance_formatted' => $nearest ? $this->formatDistance($minDistance) : null,
        ];
    }

    /**
     * Haversine distance calculation
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Format distance for display
     */
    private function formatDistance($km)
    {
        if ($km < 1) {
            return round($km * 1000, 0) . ' m';
        }
        return round($km, 1) . ' km';
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        // Get count of sales today
        $count = Sale::whereDate('created_at', today())->count() + 1;

        return "{$prefix}-{$year}{$month}{$day}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
