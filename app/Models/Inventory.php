<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'warehouse_id',
        'distributor_id',
        'product_id',
        'quantity',
        'reserved_quantity',
        'location',
        'last_stock_check',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
        'last_stock_check' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return StockMovement::where('warehouse_id', $this->warehouse_id)
            ->where('product_id', $this->product_id);
    }

    // Available quantity (total - reserved)
    public function availableQuantity(): float
    {
        return (float) $this->quantity - (float) $this->reserved_quantity;
    }

    // Check if stock is below threshold
    public function isLowStock(): bool
    {
        return $this->availableQuantity() <= $this->product->low_stock_threshold;
    }

    // Add stock
    public function addStock(float $amount, string $referenceType = null, int $referenceId = null, string $notes = null, int $adminId = null): StockMovement
    {
        $quantityBefore = $this->quantity;
        $this->quantity += $amount;
        $this->save();

        return $this->stockMovements()->create([
            'warehouse_id' => $this->warehouse_id,
            'distributor_id' => $this->distributor_id,
            'product_id' => $this->product_id,
            'type' => 'in',
            'quantity' => $amount,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $this->quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by' => $adminId,
        ]);
    }

    // Remove stock
    public function removeStock(float $amount, string $referenceType = null, int $referenceId = null, string $notes = null, int $adminId = null): ?StockMovement
    {
        if (bccomp($this->availableQuantity(), $amount, 2) < 0) {
            return null; // Insufficient stock
        }

        $quantityBefore = $this->quantity;
        $this->quantity -= $amount;
        $this->save();

        return $this->stockMovements()->create([
            'warehouse_id' => $this->warehouse_id,
            'distributor_id' => $this->distributor_id,
            'product_id' => $this->product_id,
            'type' => 'out',
            'quantity' => $amount,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $this->quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by' => $adminId,
        ]);
    }

    // Adjust stock (for corrections)
    public function adjustStock(float $newQuantity, string $notes = null, int $adminId = null): StockMovement
    {
        $quantityBefore = $this->quantity;
        $this->quantity = $newQuantity;
        $this->save();

        return $this->stockMovements()->create([
            'warehouse_id' => $this->warehouse_id,
            'distributor_id' => $this->distributor_id,
            'product_id' => $this->product_id,
            'type' => 'adjustment',
            'quantity' => $newQuantity - $quantityBefore,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $this->quantity,
            'notes' => $notes,
            'created_by' => $adminId,
        ]);
    }
}
