<?php

namespace Tests\Feature\Admin;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_menu_index_returns_200(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/menu');

        $response->assertStatus(200);
    }

    public function test_admin_menu_index_shows_items_from_db(): void
    {
        $category = Category::factory()->create(['name' => 'Pizza', 'slug' => 'pizza']);
        MenuItem::factory()->create(['category_id' => $category->id, 'name' => 'Test Pizza']);

        $response = $this->actingAs($this->admin)->get('/admin/menu');

        $response->assertSee('Test Pizza');
    }

    public function test_admin_can_create_menu_item(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->post('/admin/menu', [
            'name'         => 'New Test Pizza',
            'category_id'  => $category->id,
            'description'  => 'A great pizza.',
            'base_price'   => '14.50',
            'is_available' => '1',
        ]);

        $response->assertRedirect('/admin/menu');
        $this->assertDatabaseHas('menu_items', [
            'name' => 'New Test Pizza',
            'slug' => 'new-test-pizza',
        ]);
    }

    public function test_create_menu_item_requires_name_and_price(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/menu', []);

        $response->assertSessionHasErrors(['name', 'category_id', 'base_price']);
    }

    public function test_admin_can_update_menu_item(): void
    {
        $item = MenuItem::factory()->create(['name' => 'Old Name', 'base_price' => 12.00]);

        $response = $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'         => 'Updated Name',
            'category_id'  => $item->category_id,
            'base_price'   => '15.00',
            'is_available' => '1',
        ]);

        $response->assertRedirect('/admin/menu');
        $this->assertDatabaseHas('menu_items', [
            'id'         => $item->id,
            'name'       => 'Updated Name',
            'base_price' => 15.00,
        ]);
    }

    public function test_admin_can_delete_menu_item(): void
    {
        $item = MenuItem::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/menu/{$item->id}");

        $response->assertRedirect('/admin/menu');
        $this->assertDatabaseMissing('menu_items', ['id' => $item->id]);
    }

    public function test_admin_can_attach_allergens_to_menu_item(): void
    {
        $category = Category::factory()->create();
        $allergen = Allergen::factory()->create(['name' => 'Gluten']);

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'        => 'Gluten Pizza',
            'category_id' => $category->id,
            'base_price'  => '13.00',
            'allergens'   => [$allergen->id],
        ]);

        $item = MenuItem::where('name', 'Gluten Pizza')->first();
        $this->assertTrue($item->allergens->contains($allergen->id));
    }

    public function test_customer_cannot_access_admin_menu(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/admin/menu');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_from_admin_menu(): void
    {
        $response = $this->get('/admin/menu');

        $response->assertRedirect('/login');
    }
}
