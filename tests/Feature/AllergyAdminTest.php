<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllergyAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin    = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);
    }

    public function test_allergy_page_returns_200_with_db_allergens(): void
    {
        Allergen::factory()->create(['name' => 'Gluten', 'icon' => 'bakery_dining']);
        $response = $this->actingAs($this->admin)->get('/admin/allergy');
        $response->assertStatus(200)->assertSee('Gluten');
    }

    public function test_admin_can_toggle_allergen_visibility(): void
    {
        $allergen = Allergen::factory()->create(['is_visible' => true]);
        $this->actingAs($this->admin)->patch("/admin/allergens/{$allergen->id}/toggle");
        $this->assertDatabaseHas('allergens', ['id' => $allergen->id, 'is_visible' => false]);
    }

    public function test_admin_can_delete_allergen(): void
    {
        $allergen = Allergen::factory()->create();
        $this->actingAs($this->admin)->delete("/admin/allergens/{$allergen->id}");
        $this->assertDatabaseMissing('allergens', ['id' => $allergen->id]);
    }

    public function test_admin_can_save_menu_item_allergen_mapping(): void
    {
        $cat    = Category::factory()->create(['slug' => 'pizza']);
        $item   = MenuItem::factory()->create(['category_id' => $cat->id]);
        $gluten = Allergen::factory()->create(['name' => 'Gluten']);
        $dairy  = Allergen::factory()->create(['name' => 'Dairy']);

        $this->actingAs($this->admin)->post('/admin/allergy/map', [
            'menu_item_id' => $item->id,
            'allergens'    => [$gluten->id, $dairy->id],
        ]);

        $this->assertDatabaseHas('allergen_menu_item', ['menu_item_id' => $item->id, 'allergen_id' => $gluten->id]);
        $this->assertDatabaseHas('allergen_menu_item', ['menu_item_id' => $item->id, 'allergen_id' => $dairy->id]);
    }

    public function test_admin_can_save_allergy_settings(): void
    {
        $this->actingAs($this->admin)->post('/admin/allergy/settings', [
            'allergy_alerts_enabled' => '1',
            'checkout_disclaimer'    => 'Custom disclaimer text.',
        ]);
        $this->assertDatabaseHas('settings', ['key' => 'checkout_disclaimer', 'value' => 'Custom disclaimer text.']);
    }

    public function test_guest_redirected_from_admin_allergy_index(): void
    {
        $response = $this->get('/admin/allergy');
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_forbidden_from_admin_allergy_index(): void
    {
        $response = $this->actingAs($this->customer)->get('/admin/allergy');
        $response->assertForbidden();
    }

    public function test_guest_redirected_from_store_allergen(): void
    {
        $response = $this->post('/admin/allergens', ['name' => 'Soy']);
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_forbidden_from_store_allergen(): void
    {
        $response = $this->actingAs($this->customer)->post('/admin/allergens', ['name' => 'Soy']);
        $response->assertForbidden();
    }

    public function test_admin_can_create_allergen(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/allergens', [
            'name'       => 'Soy',
            'icon'       => 'eco',
            'sort_order' => 5,
        ]);
        $this->assertDatabaseHas('allergens', [
            'name'       => 'Soy',
            'icon'       => 'eco',
            'sort_order' => 5,
            'is_visible' => true,
        ]);
        $response->assertRedirect(route('admin.allergy.index'));
    }

    public function test_store_allergen_rejects_duplicate_name(): void
    {
        Allergen::factory()->create(['name' => 'Soy']);
        $response = $this->actingAs($this->admin)->post('/admin/allergens', ['name' => 'Soy']);
        $response->assertSessionHasErrors('name');
    }

    public function test_store_allergen_uses_default_icon_when_omitted(): void
    {
        $this->actingAs($this->admin)->post('/admin/allergens', ['name' => 'Mustard']);
        $this->assertDatabaseHas('allergens', ['name' => 'Mustard', 'icon' => 'warning']);
    }

    public function test_store_allergen_defaults_sort_order_to_zero(): void
    {
        $this->actingAs($this->admin)->post('/admin/allergens', ['name' => 'Celery']);
        $this->assertDatabaseHas('allergens', ['name' => 'Celery', 'sort_order' => 0]);
    }

    public function test_admin_can_disable_allergy_alerts(): void
    {
        $this->actingAs($this->admin)->post('/admin/allergy/settings', [
            'checkout_disclaimer' => 'Some text.',
            // allergy_alerts_enabled deliberately omitted → boolean(false) → '0'
        ]);

        $this->assertDatabaseHas('settings', ['key' => 'allergy_alerts_enabled', 'value' => '0']);
    }

    public function test_save_map_with_no_allergens_clears_all_existing_mappings(): void
    {
        $cat    = Category::factory()->create(['slug' => 'pasta']);
        $item   = MenuItem::factory()->create(['category_id' => $cat->id]);
        $gluten = Allergen::factory()->create(['name' => 'GlutenB']);

        $item->allergens()->attach($gluten->id);

        $this->actingAs($this->admin)->post('/admin/allergy/map', [
            'menu_item_id' => $item->id,
            // no 'allergens' key — controller passes [] to sync()
        ]);

        $this->assertDatabaseMissing('allergen_menu_item', [
            'menu_item_id' => $item->id,
            'allergen_id'  => $gluten->id,
        ]);
    }

    public function test_admin_can_toggle_allergen_visibility_back_on(): void
    {
        $allergen = Allergen::factory()->create(['is_visible' => false]);

        $this->actingAs($this->admin)->patch("/admin/allergens/{$allergen->id}/toggle");

        $this->assertDatabaseHas('allergens', ['id' => $allergen->id, 'is_visible' => true]);
    }

    public function test_save_map_clears_previous_allergens_on_sync(): void
    {
        $cat   = Category::factory()->create(['slug' => 'pizza']);
        $item  = MenuItem::factory()->create(['category_id' => $cat->id]);
        $gluten = Allergen::factory()->create(['name' => 'Gluten']);
        $dairy  = Allergen::factory()->create(['name' => 'Dairy']);
        $nuts   = Allergen::factory()->create(['name' => 'Nuts']);

        $item->allergens()->attach([$gluten->id, $dairy->id]);

        $this->actingAs($this->admin)->post('/admin/allergy/map', [
            'menu_item_id' => $item->id,
            'allergens'    => [$nuts->id],
        ]);

        $this->assertDatabaseMissing('allergen_menu_item', ['menu_item_id' => $item->id, 'allergen_id' => $gluten->id]);
        $this->assertDatabaseMissing('allergen_menu_item', ['menu_item_id' => $item->id, 'allergen_id' => $dairy->id]);
        $this->assertDatabaseHas('allergen_menu_item', ['menu_item_id' => $item->id, 'allergen_id' => $nuts->id]);
    }

    // -------------------------------------------------------------------------
    // Auth guards — toggle
    // -------------------------------------------------------------------------

    public function test_guest_redirected_from_toggle_allergen(): void
    {
        $allergen = \App\Models\Allergen::factory()->create();

        $this->patch("/admin/allergens/{$allergen->id}/toggle")
            ->assertRedirect('/login');
    }

    public function test_non_admin_cannot_toggle_allergen(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $allergen = \App\Models\Allergen::factory()->create();

        $this->actingAs($customer)
            ->patch("/admin/allergens/{$allergen->id}/toggle")
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Auth guards — destroyAllergen
    // -------------------------------------------------------------------------

    public function test_guest_redirected_from_destroy_allergen(): void
    {
        $allergen = \App\Models\Allergen::factory()->create();

        $this->delete("/admin/allergens/{$allergen->id}")
            ->assertRedirect('/login');
    }

    public function test_non_admin_cannot_destroy_allergen(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $allergen = \App\Models\Allergen::factory()->create();

        $this->actingAs($customer)
            ->delete("/admin/allergens/{$allergen->id}")
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Auth guards — saveSettings
    // -------------------------------------------------------------------------

    public function test_guest_redirected_from_save_allergy_settings(): void
    {
        $this->post('/admin/allergy/settings', ['allergy_alerts_enabled' => true])
            ->assertRedirect('/login');
    }

    public function test_non_admin_cannot_save_allergy_settings(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->post('/admin/allergy/settings', ['allergy_alerts_enabled' => true])
            ->assertStatus(403);
    }

    // -------------------------------------------------------------------------
    // Auth guards — saveMap
    // -------------------------------------------------------------------------

    public function test_guest_redirected_from_save_allergy_map(): void
    {
        $item = \App\Models\MenuItem::factory()->create();

        $this->post('/admin/allergy/map', ['menu_item_id' => $item->id, 'allergens' => []])
            ->assertRedirect('/login');
    }

    public function test_non_admin_cannot_save_allergy_map(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item     = \App\Models\MenuItem::factory()->create();

        $this->actingAs($customer)
            ->post('/admin/allergy/map', ['menu_item_id' => $item->id, 'allergens' => []])
            ->assertStatus(403);
    }
}
