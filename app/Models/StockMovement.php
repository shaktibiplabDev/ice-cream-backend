<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'distributor_id',
        'product_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // Scope by type
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Scope by date range
    public function scopeBetweenDates($query, string $start, string $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}
