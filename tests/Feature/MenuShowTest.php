<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_200_for_available_item(): void
    {
        $category = Category::factory()->create();
        $item = MenuItem::factory()->create([
            'category_id'  => $category->id,
            'is_available' => true,
        ]);

        $this->get('/menu/' . $item->slug)->assertStatus(200);
    }

    public function test_show_returns_404_for_unknown_slug(): void
    {
        $this->get('/menu/this-does-not-exist')->assertStatus(404);
    }

    public function test_show_returns_404_for_unavailable_item(): void
    {
        $category = Category::factory()->create();
        $item = MenuItem::factory()->unavailable()->create([
            'category_id' => $category->id,
        ]);

        $this->get('/menu/' . $item->slug)->assertStatus(404);
    }

    public function test_show_displays_item_name_in_page(): void
    {
        $category = Category::factory()->create();
        $item = MenuItem::factory()->create([
            'category_id'  => $category->id,
            'is_available' => true,
        ]);

        $this->get('/menu/' . $item->slug)->assertSee($item->name);
    }

    public function test_show_loads_related_items_from_same_category(): void
    {
        $category = Category::factory()->create();

        $itemA = MenuItem::factory()->create([
            'category_id'  => $category->id,
            'is_available' => true,
        ]);

        $itemB = MenuItem::factory()->create([
            'category_id'  => $category->id,
            'is_available' => true,
        ]);

        $this->get('/menu/' . $itemA->slug)
            ->assertViewHas('related', fn ($related) => $related->contains('id', $itemB->id))
            ->assertViewHas('related', fn ($related) => ! $related->contains('id', $itemA->id));
    }

    public function test_show_passes_item_and_title_to_view(): void
    {
        $category = Category::factory()->create();
        $item = MenuItem::factory()->create([
            'category_id'  => $category->id,
            'name'         => 'Truffle Deluxe',
            'is_available' => true,
        ]);

        $this->get('/menu/' . $item->slug)
            ->assertViewHas('item', fn ($i) => $i->id === $item->id)
            ->assertViewHas('title', 'Truffle Deluxe');
    }

    public function test_show_related_excludes_unavailable_items(): void
    {
        $category = Category::factory()->create();
        $main = MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true]);
        $available   = MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => true]);
        $unavailable = MenuItem::factory()->create(['category_id' => $category->id, 'is_available' => false]);

        $this->get('/menu/' . $main->slug)
            ->assertViewHas('related', fn ($r) =>  $r->contains('id', $available->id))
            ->assertViewHas('related', fn ($r) => !$r->contains('id', $unavailable->id));
    }
}
