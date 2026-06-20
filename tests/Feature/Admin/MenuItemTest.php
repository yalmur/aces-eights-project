<?php

namespace Tests\Feature\Admin;

use App\Models\Allergen;
use App\Models\BaseIngredient;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_create_form_defaults_is_available_checkbox_to_checked(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/menu/create');

        $response->assertSee('name="is_available" type="checkbox" value="1" checked', false);
    }

    public function test_edit_form_reflects_unavailable_item_as_unchecked(): void
    {
        $item = MenuItem::factory()->create(['is_available' => false]);

        $response = $this->actingAs($this->admin)->get("/admin/menu/{$item->id}/edit");

        $response->assertDontSee('name="is_available" type="checkbox" value="1" checked', false);
    }

    public function test_update_validation_errors_are_displayed_on_edit_form(): void
    {
        $item = MenuItem::factory()->create();

        $this->actingAs($this->admin)
            ->followingRedirects()
            ->from("/admin/menu/{$item->id}/edit")
            ->put("/admin/menu/{$item->id}", [
                'name'        => '',
                'category_id' => $item->category_id,
                'base_price'  => '15.00',
            ])
            ->assertSee('The name field is required.');
    }

    public function test_updating_menu_item_without_ingredients_field_preserves_base_ingredients(): void
    {
        $item = MenuItem::factory()->create(['name' => 'Old Name', 'base_price' => 12.00]);
        BaseIngredient::create(['menu_item_id' => $item->id, 'name' => 'Tomato Sauce', 'sort_order' => 1]);

        $response = $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'         => 'Updated Name',
            'category_id'  => $item->category_id,
            'base_price'   => '15.00',
            'is_available' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('base_ingredients', [
            'menu_item_id' => $item->id,
            'name'         => 'Tomato Sauce',
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

    public function test_admin_cannot_update_menu_item_to_duplicate_name(): void
    {
        MenuItem::factory()->create(['name' => 'Margherita']);
        $item2 = MenuItem::factory()->create(['name' => 'Pepperoni']);

        $response = $this->actingAs($this->admin)->put("/admin/menu/{$item2->id}", [
            'name'        => 'Margherita',
            'category_id' => $item2->category_id,
            'base_price'  => '12.00',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseHas('menu_items', ['id' => $item2->id, 'name' => 'Pepperoni']);
    }

    public function test_admin_can_update_menu_item_keeping_same_name(): void
    {
        $item = MenuItem::factory()->create(['name' => 'Margherita', 'base_price' => 12.00]);

        $response = $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'        => 'Margherita',
            'category_id' => $item->category_id,
            'base_price'  => '14.00',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('menu_items', ['id' => $item->id, 'base_price' => 14.00]);
    }

    public function test_deleting_menu_item_with_image_removes_file_from_storage(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('menu/pizza.jpg', 'fake');
        $item = MenuItem::factory()->create(['image_path' => 'menu/pizza.jpg']);

        $this->actingAs($this->admin)->delete("/admin/menu/{$item->id}");

        Storage::disk('public')->assertMissing('menu/pizza.jpg');
        $this->assertDatabaseMissing('menu_items', ['id' => $item->id]);
    }
}
