<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DealSlot extends Model
{
    protected $fillable = ['deal_id', 'label', 'min_qty', 'max_qty', 'is_required', 'is_free', 'sort_order'];

    protected $casts = ['is_required' => 'boolean', 'is_free' => 'boolean'];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'deal_slot_category');
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'deal_slot_menu_item');
    }

    /** Returns eligible items: specific items if set, else all from allowed categories. */
    public function eligibleItems(): Collection
    {
        $specific = $this->menuItems()->where('is_available', true)->orderBy('sort_order')->get();
        if ($specific->isNotEmpty()) {
            return $specific;
        }

        $categoryIds = $this->categories()->pluck('categories.id');
        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return MenuItem::where('is_available', true)
            ->whereIn('category_id', $categoryIds)
            ->orderBy('sort_order')
            ->get();
    }
}
