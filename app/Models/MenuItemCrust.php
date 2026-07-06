<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItemCrust extends Model
{
    protected $fillable = [
        'menu_item_id', 'name', 'price_adjustment',
        'is_default', 'is_available', 'sort_order',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'is_default'       => 'boolean',
        'is_available'     => 'boolean',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
