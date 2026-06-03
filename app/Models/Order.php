<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'status', 'subtotal', 'delivery_fee', 'total',
        'customer_name', 'customer_email', 'customer_phone',
        'delivery_address', 'delivery_city', 'delivery_postcode',
        'stripe_session_id', 'stripe_payment_intent_id', 'notes',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_payment'  => 'Awaiting Payment',
            'accepted'         => 'Accepted',
            'cooking'          => 'Cooking',
            'ready'            => 'Ready',
            'out_for_delivery' => 'Out for Delivery',
            'collected'        => 'Collected',
            'delivered'        => 'Delivered',
            'cancelled'        => 'Cancelled',
            default            => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending_payment'           => 'text-on-surface-variant',
            'accepted', 'cooking'       => 'text-primary',
            'ready', 'out_for_delivery' => 'text-green-700',
            'delivered', 'collected'    => 'text-[#2B2B2B]',
            'cancelled'                 => 'text-brand-error',
            default                     => 'text-on-surface',
        };
    }

    public function isDelivery(): bool
    {
        return $this->type === 'delivery';
    }

    public function isPaid(): bool
    {
        return $this->status !== 'pending_payment';
    }
}
