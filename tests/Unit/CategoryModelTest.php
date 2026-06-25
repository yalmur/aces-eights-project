<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_items_returns_only_available_menu_items(): void
    {
        $category = Category::factory()->create();
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true, 'sort_order' => 1]);
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true, 'sort_order' => 2]);
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => false, 'sort_order' => 3]);

        $result = $category->availableItems()->get();

        $this->assertCount(2, $result);
    }

    public function test_available_items_orders_by_sort_order(): void
    {
        $category = Category::factory()->create();
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true, 'sort_order' => 3]);
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true, 'sort_order' => 1]);
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true, 'sort_order' => 2]);

        $result = $category->availableItems()->get();

        $this->assertSame([1, 2, 3], $result->pluck('sort_order')->all());
    }

    public function test_available_items_returns_empty_when_all_items_unavailable(): void
    {
        $category = Category::factory()->create();
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => false]);
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => false]);

        $result = $category->availableItems()->get();

        $this->assertCount(0, $result);
    }

    public function test_menu_items_returns_all_items_regardless_of_availability(): void
    {
        $category = Category::factory()->create();
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true, 'sort_order' => 1]);
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true, 'sort_order' => 2]);
        MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => false, 'sort_order' => 3]);

        $result = $category->menuItems()->get();

        $this->assertCount(3, $result);
    }

    public function test_menu_items_relationship_includes_unavailable_items(): void
    {
        $category    = Category::factory()->create();
        $available   = MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true]);
        $unavailable = MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => false]);

        $ids = $category->menuItems()->pluck('id')->toArray();

        $this->assertContains($available->id, $ids);
        $this->assertContains($unavailable->id, $ids);
    }

    public function test_menu_items_relationship_orders_by_sort_order(): void
    {
        $category = Category::factory()->create();
        $second   = MenuItem::factory()->create(['category_id' => $category->id, 'sort_order' => 2]);
        $first    = MenuItem::factory()->create(['category_id' => $category->id, 'sort_order' => 1]);

        $ids = $category->menuItems()->pluck('id')->toArray();

        $this->assertSame([$first->id, $second->id], $ids);
    }

    public function test_slug_is_stored_correctly(): void
    {
        $category = Category::factory()->create(['slug' => 'hot-dogs']);

        $this->assertSame('hot-dogs', $category->fresh()->slug);
    }
}
