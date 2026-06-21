<?php

namespace Tests\Unit;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllergenModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_allergen_factory_creates_valid_record(): void
    {
        $allergen = Allergen::factory()->create();

        $this->assertDatabaseCount('allergens', 1);
        $this->assertNotEmpty($allergen->name);
        $this->assertNotEmpty($allergen->icon);
    }

    public function test_allergen_is_visible_defaults_to_true(): void
    {
        $allergen = Allergen::factory()->create();

        $this->assertTrue($allergen->is_visible);
        $this->assertDatabaseHas('allergens', ['id' => $allergen->id, 'is_visible' => true]);
    }

    public function test_allergens_ordered_by_sort_order(): void
    {
        Allergen::factory()->create(['name' => 'Dairy', 'sort_order' => 3]);
        Allergen::factory()->create(['name' => 'Gluten', 'sort_order' => 1]);
        Allergen::factory()->create(['name' => 'Nuts', 'sort_order' => 2]);

        $ordered = Allergen::orderBy('sort_order')->pluck('name')->toArray();

        $this->assertEquals(['Gluten', 'Nuts', 'Dairy'], $ordered);
    }

    public function test_allergen_belongs_to_many_menu_items(): void
    {
        $cat = Category::factory()->create(['slug' => 'pizza']);
        $item = MenuItem::factory()->create(['category_id' => $cat->id]);
        $allergen = Allergen::factory()->create();

        $item->allergens()->attach($allergen->id);

        $related = $allergen->menuItems;

        $this->assertEquals(1, $related->count());
        $this->assertEquals($item->id, $related->first()->id);
    }

    public function test_allergen_can_be_hidden(): void
    {
        $allergen = Allergen::factory()->create(['is_visible' => false]);

        $this->assertFalse($allergen->is_visible);
        $this->assertDatabaseHas('allergens', ['id' => $allergen->id, 'is_visible' => false]);
    }

    public function test_allergen_name_and_icon_are_stored_correctly(): void
    {
        $allergen = Allergen::factory()->create(['name' => 'Sesame', 'icon' => 'grain']);

        $this->assertEquals('Sesame', $allergen->name);
        $this->assertEquals('grain', $allergen->icon);
    }
}
