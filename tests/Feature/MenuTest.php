<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Topping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_menu_items(): void
    {
        $category = Category::factory()->create();
        MenuItem::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->menuItems);
    }

    public function test_menu_item_belongs_to_category(): void
    {
        $item = MenuItem::factory()->create();

        $this->assertInstanceOf(Category::class, $item->category);
    }

    public function test_menu_item_has_formatted_price(): void
    {
        $item = MenuItem::factory()->create(['base_price' => 12.50]);

        $this->assertSame('£12.50', $item->formatted_price);
    }

    public function test_menu_item_can_have_allergens(): void
    {
        $item     = MenuItem::factory()->create();
        $allergen = Allergen::factory()->create(['name' => 'Gluten']);
        $item->allergens()->attach($allergen);

        $this->assertCount(1, $item->allergens);
        $this->assertSame('Gluten', $item->allergens->first()->name);
    }

    public function test_topping_available_scope(): void
    {
        Topping::factory()->create(['is_available' => true]);
        Topping::factory()->create(['is_available' => false]);

        $this->assertCount(1, Topping::available()->get());
    }

    public function test_customer_menu_page_returns_200(): void
    {
        Category::factory()->create(['slug' => 'pizza', 'name' => 'Pizza']);

        $response = $this->get('/menu');

        $response->assertStatus(200);
    }
}
