<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category_id',
        'size',
        'mrp_price',
        'distributor_price',
        'retailer_price',
        'unit',
        'image',
        'is_active',
        'low_stock_threshold',
    ];

    protected $casts = [
        'mrp_price' => 'decimal:2',
        'distributor_price' => 'decimal:2',
        'retailer_price' => 'decimal:2',
        'is_active' => 'boolean',
        'low_stock_threshold' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Get stock at a specific warehouse
    public function stockAtWarehouse(int $warehouseId): ?Inventory
    {
        return $this->inventory()->where('warehouse_id', $warehouseId)->first();
    }

    // Get available quantity (stock - reserved) at warehouse
    public function availableAtWarehouse(int $warehouseId): float
    {
        $inventory = $this->stockAtWarehouse($warehouseId);
        return $inventory ? $inventory->quantity - $inventory->reserved_quantity : 0;
    }

    // Check if low stock at any warehouse
    public function isLowStock(): bool
    {
        return $this->inventory()->get()->some(fn ($inv) => $inv->isLowStock());
    }

    // Get price based on customer type
    public function getPriceFor(string $type = 'distributor'): float
    {
        return match($type) {
            'celesty', 'mrp', 'customer' => $this->mrp_price,
            'distributor' => $this->distributor_price,
            'retailer' => $this->retailer_price,
            default => $this->distributor_price,
        };
    }

    // Scope active products
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope by category
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Get image URL
    public function getImageUrl(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
