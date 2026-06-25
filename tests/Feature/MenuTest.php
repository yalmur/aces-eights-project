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

    public function test_menu_index_passes_categories_to_view(): void
    {
        \Illuminate\Support\Facades\Cache::flush();
        $cat = Category::factory()->create(['name' => 'Pizzas', 'sort_order' => 1]);
        MenuItem::factory()->create(['category_id' => $cat->id, 'is_available' => true]);

        $response = $this->get('/menu');

        $response->assertViewHas('categories', fn ($cats) => $cats->contains('name', 'Pizzas'));
    }

    public function test_menu_index_passes_available_toppings_to_view(): void
    {
        \Illuminate\Support\Facades\Cache::flush();
        Topping::factory()->create(['name' => 'Olives', 'is_available' => true]);
        Topping::factory()->create(['name' => 'Anchovy', 'is_available' => false]);

        $response = $this->get('/menu');

        $toppings = $response->viewData('toppings');
        $this->assertTrue($toppings->contains('name', 'Olives'));
        $this->assertFalse($toppings->contains('name', 'Anchovy'));
    }

    public function test_menu_index_categories_ordered_by_sort_order(): void
    {
        \Illuminate\Support\Facades\Cache::flush();
        $c2 = Category::factory()->create(['name' => 'Sides',  'sort_order' => 2]);
        $c1 = Category::factory()->create(['name' => 'Pizzas', 'sort_order' => 1]);
        MenuItem::factory()->create(['category_id' => $c1->id, 'is_available' => true]);
        MenuItem::factory()->create(['category_id' => $c2->id, 'is_available' => true]);

        $names = $this->get('/menu')->viewData('categories')->pluck('name')->all();

        $this->assertSame(['Pizzas', 'Sides'], $names);
    }
}
