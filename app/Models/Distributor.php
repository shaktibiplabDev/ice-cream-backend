<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'address',
        'phone',
        'email',
        'gst_number',
        'business_type',
        'website',
        'description',
        'service_area',
        'delivery_capacity',
        'is_active',
        'timings',
        'social_media',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    // Scope for active distributors
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationships
    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Get products in stock at this distributor
    public function inStockProducts()
    {
        return $this->inventory()->where('quantity', '>', 0)->with('product');
    }

    // Get low stock items at this distributor
    public function lowStockItems()
    {
        return $this->inventory()->get()->filter(fn ($inv) => $inv->isLowStock());
    }
}