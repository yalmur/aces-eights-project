<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'base_price', 'image_path', 'is_available', 'is_featured', 'sort_order',
        'is_vegetarian', 'is_vegan', 'is_customizable',
    ];

    public function hasStoredImage(): bool
    {
        return $this->image_path && Storage::disk('public')->exists($this->image_path);
    }

    protected $casts = [
        'base_price'    => 'decimal:2',
        'is_available'  => 'boolean',
        'is_featured'   => 'boolean',
        'is_vegetarian'   => 'boolean',
        'is_vegan'        => 'boolean',
        'is_customizable' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function baseIngredients(): HasMany
    {
        return $this->hasMany(BaseIngredient::class)->orderBy('sort_order');
    }

    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class);
    }

    public function relatedItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_related', 'menu_item_id', 'related_menu_item_id');
    }

    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class)->orderBy('sort_order');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(MenuItemSize::class)->orderBy('sort_order');
    }

    public function crusts(): HasMany
    {
        return $this->hasMany(MenuItemCrust::class)->orderBy('sort_order');
    }

    public function isPizza(): bool
    {
        return $this->category?->slug === 'pizza';
    }

    public function toppingsPayload(): array
    {
        return $this->toppings->where('is_available', true)
            ->map(fn (Topping $t) => ['name' => $t->name, 'price' => (float) $t->price])
            ->values()->all();
    }

    public function sizesPayload(): array
    {
        return $this->sizes->where('is_available', true)
            ->map(fn (MenuItemSize $s) => [
                'name'       => $s->name,
                'price_adj'  => (float) $s->price_adjustment,
                'is_default' => $s->is_default,
            ])
            ->values()->all();
    }

    public function crustsPayload(): array
    {
        return $this->crusts->where('is_available', true)
            ->map(fn (MenuItemCrust $c) => [
                'name'       => $c->name,
                'price_adj'  => (float) $c->price_adjustment,
                'is_default' => $c->is_default,
            ])
            ->values()->all();
    }

    public function relatedItemsPayload(): array
    {
        return $this->relatedItems->map(fn (self $r) => [
            'id'    => $r->slug,
            'name'  => $r->name,
            'price' => (float) $r->base_price,
            'image' => $r->hasStoredImage()
                ? asset('storage/' . $r->image_path)
                : 'https://placehold.co/64x64/e4e2e1/1b1c1c?text=+',
        ])->values()->all();
    }

    public function getFormattedPriceAttribute(): string
    {
        return '£' . number_format((float) $this->base_price, 2);
    }
}
