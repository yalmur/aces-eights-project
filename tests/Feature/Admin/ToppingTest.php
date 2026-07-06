<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Topping;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToppingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_toppings_index_returns_200(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/toppings');

        $response->assertOk();
    }

    public function test_customer_cannot_access_admin_toppings(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/admin/toppings')->assertForbidden();
    }

    public function test_admin_can_create_topping(): void
    {
        $this->actingAs($this->admin)->post('/admin/toppings', [
            'name'  => 'Jalapenos',
            'price' => '1.50',
        ]);

        $this->assertDatabaseHas('toppings', [
            'name'         => 'Jalapenos',
            'price'        => 1.50,
            'is_available' => true,
        ]);
    }

    public function test_create_topping_requires_unique_name(): void
    {
        Topping::factory()->create(['name' => 'Mushrooms']);

        $response = $this->actingAs($this->admin)->post('/admin/toppings', [
            'name'  => 'Mushrooms',
            'price' => '1.00',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_topping_price(): void
    {
        $topping = Topping::factory()->create(['name' => 'Olives', 'price' => 1.00]);

        $this->actingAs($this->admin)->put("/admin/toppings/{$topping->id}", [
            'name'  => 'Olives',
            'price' => '2.50',
        ]);

        $this->assertDatabaseHas('toppings', [
            'id'    => $topping->id,
            'price' => 2.50,
        ]);
    }

    public function test_toggle_sets_topping_unavailable(): void
    {
        $topping = Topping::factory()->create(['is_available' => true]);

        $this->actingAs($this->admin)->patch("/admin/toppings/{$topping->id}/toggle");

        $this->assertDatabaseHas('toppings', ['id' => $topping->id, 'is_available' => false]);
    }

    public function test_admin_can_delete_topping(): void
    {
        $topping = Topping::factory()->create();

        $this->actingAs($this->admin)->delete("/admin/toppings/{$topping->id}");

        $this->assertDatabaseMissing('toppings', ['id' => $topping->id]);
    }

    public function test_admin_can_assign_toppings_to_menu_item(): void
    {
        $category = Category::factory()->create();
        $item     = MenuItem::factory()->create(['category_id' => $category->id]);
        $topping  = Topping::factory()->create();

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'        => $item->name,
            'category_id' => $item->category_id,
            'base_price'  => $item->base_price,
            'toppings'    => [$topping->id],
        ]);

        $this->assertTrue($item->fresh()->toppings->contains('id', $topping->id));
    }

    public function test_update_replaces_menu_item_toppings(): void
    {
        $category = Category::factory()->create();
        $item     = MenuItem::factory()->create(['category_id' => $category->id]);
        $old      = Topping::factory()->create();
        $new      = Topping::factory()->create();
        $item->toppings()->attach($old->id);

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'        => $item->name,
            'category_id' => $item->category_id,
            'base_price'  => $item->base_price,
            'toppings'    => [$new->id],
        ]);

        $item->refresh();
        $this->assertFalse($item->toppings->contains('id', $old->id));
        $this->assertTrue($item->toppings->contains('id', $new->id));
    }
}
