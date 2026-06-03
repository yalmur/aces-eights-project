<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'min_km', 'max_km', 'fee', 'is_active', 'sort_order'];

    protected $casts = [
        'min_km'    => 'decimal:2',
        'max_km'    => 'decimal:2',
        'fee'       => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
