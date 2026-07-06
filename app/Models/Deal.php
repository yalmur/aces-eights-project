<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Deal extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'image_path', 'deal_type', 'custom_label',
        'price', 'discount_value', 'is_active', 'starts_at', 'ends_at', 'sort_order',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'discount_value' => 'decimal:2',
        'is_active'      => 'boolean',
        'starts_at'      => 'datetime',
        'ends_at'        => 'datetime',
    ];

    public function slots(): HasMany
    {
        return $this->hasMany(DealSlot::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function getTypeLabelAttribute(): string
    {
        if ($this->custom_label) {
            return $this->custom_label;
        }

        return match ($this->deal_type) {
            'bundle'         => 'Bundle — £' . number_format($this->price, 2),
            'bogo'           => 'Buy 1 Get 1 Free',
            'percentage_off' => $this->discount_value . '% Off',
            'fixed_off'      => '£' . number_format($this->discount_value, 2) . ' Off',
            default          => $this->deal_type,
        };
    }

    public function hasSlots(): bool
    {
        return in_array($this->deal_type, ['bundle', 'bogo']);
    }

    /** Payload sent to admin edit form (Alpine slot builder). */
    public function slotsAdminPayload(): array
    {
        return $this->slots->map(fn ($slot) => [
            'id'           => $slot->id,
            'label'        => $slot->label,
            'min_qty'      => $slot->min_qty,
            'max_qty'      => $slot->max_qty,
            'is_required'  => $slot->is_required,
            'is_free'      => $slot->is_free,
            'sort_order'   => $slot->sort_order,
            'item_ids'     => $slot->menuItems->pluck('id')->all(),
        ])->all();
    }

    /** Payload sent to frontend deal drawer. */
    public function toFrontendPayload(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'deal_type'   => $this->deal_type,
            'price'       => $this->price ? (float) $this->price : null,
            'image'       => $this->image_path ? asset('storage/' . $this->image_path) : null,
            'slots'       => $this->slots->map(fn ($slot) => [
                'id'          => $slot->id,
                'label'       => $slot->label,
                'min_qty'     => $slot->min_qty,
                'max_qty'     => $slot->max_qty,
                'is_required' => $slot->is_required,
                'is_free'     => $slot->is_free,
                'items'       => $slot->eligibleItems()->map(fn ($item) => [
                    'id'    => $item->slug,
                    'name'  => $item->name,
                    'price' => (float) $item->base_price,
                    'image' => $item->hasStoredImage()
                        ? asset('storage/' . $item->image_path)
                        : 'https://placehold.co/200x150/e4e2e1/1b1c1c?text=' . urlencode($item->name),
                ])->values()->all(),
            ])->values()->all(),
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Deal $deal) {
            if (empty($deal->slug)) {
                $deal->slug = Str::slug($deal->name);
            }
        });
    }
}
