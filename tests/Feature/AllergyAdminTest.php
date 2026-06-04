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

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
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
}
