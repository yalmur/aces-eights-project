<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OurMenuAndHomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    // ─── OurMenuController ────────────────────────────────────────────────────

    public function test_our_menu_only_shows_available_items(): void
    {
        $category = Category::factory()->create(['sort_order' => 1]);
        MenuItem::factory()->create([
            'category_id' => $category->id,
            'name'        => 'Visible Pizza',
            'is_available' => true,
        ]);
        MenuItem::factory()->create([
            'category_id' => $category->id,
            'name'        => 'Hidden Burger',
            'is_available' => false,
        ]);

        $response = $this->get('/our-menu');

        $response->assertStatus(200);
        $response->assertSee('Visible Pizza');
        $response->assertDontSee('Hidden Burger');
    }

    public function test_our_menu_filters_categories_with_no_available_items(): void
    {
        $visibleCategory = Category::factory()->create(['name' => 'Active Category', 'sort_order' => 1]);
        $emptyCategory   = Category::factory()->create(['name' => 'Empty Category', 'sort_order' => 2]);

        MenuItem::factory()->create([
            'category_id'  => $visibleCategory->id,
            'name'         => 'Available Item',
            'is_available' => true,
        ]);
        MenuItem::factory()->create([
            'category_id'  => $emptyCategory->id,
            'name'         => 'Unavailable Item',
            'is_available' => false,
        ]);

        $response = $this->get('/our-menu');

        $response->assertStatus(200);
        $response->assertSee('Active Category');
        $response->assertDontSee('Empty Category');
    }

    public function test_our_menu_includes_allergen_names_for_items(): void
    {
        $category = Category::factory()->create(['sort_order' => 1]);
        $item     = MenuItem::factory()->create([
            'category_id'  => $category->id,
            'name'         => 'Allergen Pizza',
            'is_available' => true,
        ]);

        $visibleAllergen   = Allergen::factory()->create(['name' => 'Gluten', 'is_visible' => true]);
        $invisibleAllergen = Allergen::factory()->create(['name' => 'Shellfish', 'is_visible' => false]);

        $item->allergens()->attach([$visibleAllergen->id, $invisibleAllergen->id]);

        $response = $this->get('/our-menu');

        $response->assertStatus(200);
        // Allergens are JSON-encoded in the @click attribute on the page
        $response->assertSee('Gluten');
        $response->assertDontSee('Shellfish');
    }

    public function test_our_menu_orders_categories_by_sort_order(): void
    {
        Category::factory()->create(['name' => 'Second Category', 'sort_order' => 20]);
        Category::factory()->create(['name' => 'First Category', 'sort_order' => 1]);

        // Each category needs at least one available item to appear
        MenuItem::factory()->create([
            'category_id'  => Category::where('name', 'Second Category')->first()->id,
            'is_available' => true,
        ]);
        MenuItem::factory()->create([
            'category_id'  => Category::where('name', 'First Category')->first()->id,
            'is_available' => true,
        ]);

        $response = $this->get('/our-menu');

        $response->assertStatus(200);
        $content  = $response->getContent();
        $posFirst  = strpos($content, 'First Category');
        $posSecond = strpos($content, 'Second Category');
        $this->assertNotFalse($posFirst, 'First Category not found in response');
        $this->assertNotFalse($posSecond, 'Second Category not found in response');
        $this->assertLessThan($posSecond, $posFirst, 'First Category should appear before Second Category');
    }

    // ─── HomeController ───────────────────────────────────────────────────────

    public function test_home_page_shows_featured_available_items(): void
    {
        $category = Category::factory()->create();

        MenuItem::factory()->create([
            'category_id'  => $category->id,
            'name'         => 'Featured Available',
            'is_featured'  => true,
            'is_available' => true,
        ]);
        MenuItem::factory()->create([
            'category_id'  => $category->id,
            'name'         => 'Featured Unavailable',
            'is_featured'  => true,
            'is_available' => false,
        ]);
        MenuItem::factory()->create([
            'category_id'  => $category->id,
            'name'         => 'Unfeatured Available',
            'is_featured'  => false,
            'is_available' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Featured Available');
        $response->assertDontSee('Featured Unavailable');
        $response->assertDontSee('Unfeatured Available');
    }

    public function test_home_page_does_not_exceed_six_featured_items(): void
    {
        $category = Category::factory()->create();

        // Create 8 featured+available items with distinct names
        $items = MenuItem::factory()->count(8)->create([
            'category_id'  => $category->id,
            'is_featured'  => true,
            'is_available' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        // Only the first 6 (by insertion order / DB default) should be shown.
        // Count how many of the 8 item names appear in the response.
        $shown = 0;
        foreach ($items as $item) {
            if (str_contains($response->getContent(), $item->name)) {
                $shown++;
            }
        }
        $this->assertLessThanOrEqual(6, $shown, "Home page should show at most 6 featured items, found {$shown}");
    }

    public function test_home_page_shows_custom_hero_text(): void
    {
        Setting::set('hero_text', 'Grand Opening');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('heroText', 'Grand Opening');
    }

    public function test_home_page_passes_story_text_from_settings(): void
    {
        Setting::set('story_text', 'Born in the heart of London.');

        $this->get('/')
            ->assertOk()
            ->assertViewHas('storyText', 'Born in the heart of London.');
    }

    public function test_home_page_passes_opening_hours_sun_thu_from_settings(): void
    {
        Setting::set('opening_sun_thu', '17:00 – 22:00');

        $this->get('/')
            ->assertOk()
            ->assertViewHas('openingSunThu', '17:00 – 22:00');
    }

    public function test_home_page_passes_opening_hours_fri_sat_from_settings(): void
    {
        Setting::set('opening_fri_sat', '17:00 – 23:30');

        $this->get('/')
            ->assertOk()
            ->assertViewHas('openingFriSat', '17:00 – 23:30');
    }

    public function test_home_page_uses_default_opening_hours_when_setting_absent(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertViewHas('openingSunThu', '16:00 – 22:45')
            ->assertViewHas('openingFriSat', '16:00 – 23:15');
    }

    public function test_home_page_passes_is_open_now_as_boolean(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $this->assertIsBool($response->viewData('isOpenNow'));
    }
}
