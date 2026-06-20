<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'base_price', 'image_path', 'is_available', 'is_featured', 'sort_order',
        'is_vegetarian', 'is_vegan',
    ];

    public function hasStoredImage(): bool
    {
        return $this->image_path && Storage::disk('public')->exists($this->image_path);
    }

    protected $casts = [
        'base_price'    => 'decimal:2',
        'is_available'  => 'boolean',
        'is_featured'   => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_vegan'      => 'boolean',
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

    public function isPizza(): bool
    {
        return $this->category?->slug === 'pizza';
    }

    public function getFormattedPriceAttribute(): string
    {
        return '£' . number_format((float) $this->base_price, 2);
    }
}
