<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'size',
        'price',
        'unit',
        'image',
        'is_active',
        'low_stock_threshold',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'low_stock_threshold' => 'integer',
    ];

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Get stock at a specific distributor
    public function stockAtDistributor(int $distributorId): ?Inventory
    {
        return $this->inventory()->where('distributor_id', $distributorId)->first();
    }

    // Get available quantity (stock - reserved) at distributor
    public function availableAtDistributor(int $distributorId): float
    {
        $inventory = $this->stockAtDistributor($distributorId);
        return $inventory ? $inventory->availableQuantity() : 0;
    }

    // Check if low stock at any distributor
    public function isLowStock(): bool
    {
        return $this->inventory()->get()->some(fn ($inv) => $inv->isLowStock());
    }

    // Scope active products
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
