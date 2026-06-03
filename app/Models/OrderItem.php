<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'menu_item_id', 'name', 'qty', 'unit_price',
        'size', 'crust', 'size_extra', 'crust_extra',
        'added_toppings', 'removed_ingredients', 'instructions', 'line_total',
    ];

    protected $casts = [
        'unit_price'          => 'decimal:2',
        'size_extra'          => 'decimal:2',
        'crust_extra'         => 'decimal:2',
        'line_total'          => 'decimal:2',
        'added_toppings'      => 'array',
        'removed_ingredients' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getCustomisationSummaryAttribute(): string
    {
        $parts = [];
        if ($this->size && $this->size !== '12" Standard') $parts[] = $this->size;
        if ($this->crust && $this->crust !== '48hr Sourdough') $parts[] = $this->crust;
        if ($this->removed_ingredients) {
            foreach ($this->removed_ingredients as $ing) $parts[] = 'no ' . $ing;
        }
        if ($this->added_toppings) {
            foreach ($this->added_toppings as $t) $parts[] = '+' . $t['name'];
        }
        if ($this->instructions) $parts[] = $this->instructions;
        return implode(' · ', $parts) ?: 'No extras';
    }
}
