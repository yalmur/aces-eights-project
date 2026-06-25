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

    public function test_update_regenerates_slug_when_name_changes(): void
    {
        $item = MenuItem::factory()->create(['name' => 'Old Pizza', 'slug' => 'old-pizza']);

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'        => 'New Signature Pizza',
            'category_id' => $item->category_id,
            'base_price'  => $item->base_price,
        ]);

        $this->assertDatabaseHas('menu_items', [
            'id'   => $item->id,
            'slug' => 'new-signature-pizza',
        ]);
    }

    public function test_store_slug_strips_special_characters_from_name(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'        => 'Triple-Cheese & Mushroom!',
            'category_id' => $category->id,
            'base_price'  => '13.00',
        ]);

        $this->assertDatabaseHas('menu_items', [
            'slug' => 'triple-cheese-mushroom',
        ]);
    }

    public function test_toggle_availability_sets_item_unavailable(): void
    {
        $item = MenuItem::factory()->create(['is_available' => true]);

        $response = $this->actingAs($this->admin)->patch("/admin/menu/{$item->id}/toggle");

        $response->assertStatus(200);
        $response->assertJson(['available' => false]);
        $this->assertDatabaseHas('menu_items', ['id' => $item->id, 'is_available' => false]);
    }

    public function test_toggle_availability_sets_item_available(): void
    {
        $item = MenuItem::factory()->create(['is_available' => false]);

        $response = $this->actingAs($this->admin)->patch("/admin/menu/{$item->id}/toggle");

        $response->assertStatus(200);
        $response->assertJson(['available' => true]);
        $this->assertDatabaseHas('menu_items', ['id' => $item->id, 'is_available' => true]);
    }

    public function test_update_syncs_allergens_replacing_old_ones(): void
    {
        $allergenA = Allergen::factory()->create(['name' => 'Gluten']);
        $allergenB = Allergen::factory()->create(['name' => 'Dairy']);
        $item = MenuItem::factory()->create();
        $item->allergens()->attach($allergenA->id);

        $response = $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'        => $item->name,
            'category_id' => $item->category_id,
            'base_price'  => $item->base_price,
            'allergens'   => [$allergenB->id],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue($item->fresh()->allergens->contains($allergenB->id));
        $this->assertFalse($item->fresh()->allergens->contains($allergenA->id));
    }

    public function test_update_with_new_image_deletes_old_image_and_stores_new(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('menu/old.jpg', 'old');
        $item = MenuItem::factory()->create(['image_path' => 'menu/old.jpg']);
        $newImage = UploadedFile::fake()->image('new.jpg');

        $response = $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'        => $item->name,
            'category_id' => $item->category_id,
            'base_price'  => $item->base_price,
            'image'       => $newImage,
        ]);

        $response->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing('menu/old.jpg');
        $this->assertNotNull($item->fresh()->image_path);
        $this->assertNotEquals('menu/old.jpg', $item->fresh()->image_path);
    }

    public function test_index_search_filters_items_by_name(): void
    {
        $category = Category::factory()->create();
        MenuItem::factory()->create(['name' => 'Margherita Special', 'category_id' => $category->id]);
        MenuItem::factory()->create(['name' => 'Calzone Delight', 'category_id' => $category->id]);

        $response = $this->actingAs($this->admin)->get('/admin/menu?search=Calzone');

        $response->assertStatus(200);
        $response->assertSee('Calzone Delight');
        $response->assertDontSee('Margherita Special');
    }

    public function test_index_category_filter_shows_only_items_in_that_category(): void
    {
        $pizzaCat = Category::factory()->create(['slug' => 'pizza', 'name' => 'Pizza']);
        $sidesCat = Category::factory()->create(['slug' => 'sides', 'name' => 'Sides']);
        MenuItem::factory()->create(['name' => 'Margherita',  'category_id' => $pizzaCat->id]);
        MenuItem::factory()->create(['name' => 'Garlic Bread', 'category_id' => $sidesCat->id]);

        $response = $this->actingAs($this->admin)->get('/admin/menu?category=pizza');

        $response->assertStatus(200);
        $response->assertSee('Margherita');
        $response->assertDontSee('Garlic Bread');
    }

    public function test_index_category_filter_combined_with_search(): void
    {
        $pizzaCat = Category::factory()->create(['slug' => 'pizza', 'name' => 'Pizza']);
        $sidesCat = Category::factory()->create(['slug' => 'sides', 'name' => 'Sides']);
        MenuItem::factory()->create(['name' => 'Spicy Pizza',  'category_id' => $pizzaCat->id]);
        MenuItem::factory()->create(['name' => 'Spicy Wedges', 'category_id' => $sidesCat->id]);

        $response = $this->actingAs($this->admin)->get('/admin/menu?category=pizza&search=Spicy');

        $response->assertStatus(200);
        $response->assertSee('Spicy Pizza');
        $response->assertDontSee('Spicy Wedges');
    }

    public function test_store_saves_base_ingredients_from_ingredients_field(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'        => 'Ingredient Pizza',
            'category_id' => $category->id,
            'base_price'  => '12.00',
            'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Basil'],
        ]);

        $item = MenuItem::where('name', 'Ingredient Pizza')->first();
        $this->assertNotNull($item);
        $this->assertDatabaseHas('base_ingredients', ['menu_item_id' => $item->id, 'name' => 'Tomato Sauce', 'sort_order' => 1]);
        $this->assertDatabaseHas('base_ingredients', ['menu_item_id' => $item->id, 'name' => 'Mozzarella',   'sort_order' => 2]);
        $this->assertDatabaseHas('base_ingredients', ['menu_item_id' => $item->id, 'name' => 'Basil',        'sort_order' => 3]);
    }

    public function test_update_replaces_base_ingredients_when_ingredients_field_sent(): void
    {
        $item = MenuItem::factory()->create();
        BaseIngredient::create(['menu_item_id' => $item->id, 'name' => 'Old Sauce', 'sort_order' => 1]);

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'        => $item->name,
            'category_id' => $item->category_id,
            'base_price'  => $item->base_price,
            'ingredients' => ['New Sauce', 'New Cheese'],
        ]);

        $this->assertDatabaseMissing('base_ingredients', ['menu_item_id' => $item->id, 'name' => 'Old Sauce']);
        $this->assertDatabaseHas('base_ingredients',    ['menu_item_id' => $item->id, 'name' => 'New Sauce', 'sort_order' => 1]);
        $this->assertDatabaseHas('base_ingredients',    ['menu_item_id' => $item->id, 'name' => 'New Cheese', 'sort_order' => 2]);
    }

    public function test_store_sets_is_vegetarian_and_is_vegan_flags(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'          => 'Vegan Delight',
            'category_id'   => $category->id,
            'base_price'    => '11.00',
            'is_vegetarian' => '1',
            'is_vegan'      => '1',
        ]);

        $this->assertDatabaseHas('menu_items', [
            'name'          => 'Vegan Delight',
            'is_vegetarian' => true,
            'is_vegan'      => true,
        ]);
    }

    public function test_store_syncs_related_items_via_pivot(): void
    {
        $category = Category::factory()->create();
        $related  = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'          => 'Main Pizza',
            'category_id'   => $category->id,
            'base_price'    => '13.00',
            'related_items' => [$related->id],
        ]);

        $main = MenuItem::where('name', 'Main Pizza')->first();
        $this->assertNotNull($main);
        $this->assertTrue($main->relatedItems->contains('id', $related->id));
    }
}
