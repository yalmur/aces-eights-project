<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_order_amount',
        'max_uses', 'current_uses', 'is_active', 'expires_at',
    ];

    protected $casts = [
        'value'            => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active'        => 'boolean',
        'expires_at'       => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(fn ($q) => $q->whereNull('max_uses')->orWhereColumn('current_uses', '<', 'max_uses'));
    }

    public function isValid(float $subtotal): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->current_uses >= $this->max_uses) return false;
        if ($this->min_order_amount && $subtotal < $this->min_order_amount) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal, float $deliveryFee): float
    {
        return match($this->type) {
            'percentage'    => round($subtotal * ($this->value / 100), 2),
            'fixed_amount'  => min((float)$this->value, $subtotal),
            'free_delivery' => $deliveryFee,
            default         => 0,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'percentage'    => "{$this->value}% off",
            'fixed_amount'  => "£{$this->value} off",
            'free_delivery' => 'Free delivery',
            default         => $this->type,
        };
    }

    public function incrementUses(): void
    {
        $this->increment('current_uses');
    }
}
