<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'address',
        'phone',
        'email',
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
}