<?php

namespace Tests\Unit;

use App\Models\BaseIngredient;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BaseIngredientModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_be_created_with_required_fields(): void
    {
        $item       = MenuItem::factory()->create();
        $ingredient = BaseIngredient::create([
            'menu_item_id' => $item->id,
            'name'         => 'Mozzarella',
            'sort_order'   => 1,
        ]);

        $this->assertDatabaseHas('base_ingredients', [
            'menu_item_id' => $item->id,
            'name'         => 'Mozzarella',
            'sort_order'   => 1,
        ]);
    }

    public function test_sort_order_defaults_to_zero(): void
    {
        $item       = MenuItem::factory()->create();
        $ingredient = BaseIngredient::create([
            'menu_item_id' => $item->id,
            'name'         => 'Tomato Sauce',
        ]);

        $this->assertSame(0, $ingredient->fresh()->sort_order);
    }

    public function test_menu_item_relationship_returns_correct_instance(): void
    {
        $item       = MenuItem::factory()->create(['name' => 'Margherita']);
        $ingredient = BaseIngredient::create([
            'menu_item_id' => $item->id,
            'name'         => 'Basil',
        ]);

        $this->assertSame($item->id, $ingredient->menuItem->id);
        $this->assertSame('Margherita', $ingredient->menuItem->name);
    }

    public function test_cascade_deletes_with_menu_item(): void
    {
        $item       = MenuItem::factory()->create();
        $ingredient = BaseIngredient::create([
            'menu_item_id' => $item->id,
            'name'         => 'Pepperoni',
        ]);

        $item->delete();

        $this->assertDatabaseMissing('base_ingredients', ['id' => $ingredient->id]);
    }
}
