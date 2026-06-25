<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MenuCacheTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->admin    = User::factory()->create(['role' => 'admin']);
        $this->category = Category::factory()->create();
    }

    public function test_store_invalidates_public_menu_cache(): void
    {
        $this->get('/menu')->assertOk();
        $this->assertTrue(Cache::has('public.menu.index'));

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'        => 'Cache Buster Pizza',
            'category_id' => $this->category->id,
            'base_price'  => '12.00',
        ]);

        $this->assertFalse(Cache::has('public.menu.index'));
    }

    public function test_update_invalidates_public_menu_cache(): void
    {
        $item = MenuItem::factory()->create(['category_id' => $this->category->id]);

        $this->get('/menu')->assertOk();
        $this->assertTrue(Cache::has('public.menu.index'));

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'        => 'Updated Name',
            'category_id' => $this->category->id,
            'base_price'  => '14.00',
        ]);

        $this->assertFalse(Cache::has('public.menu.index'));
    }

    public function test_destroy_invalidates_public_menu_cache(): void
    {
        $item = MenuItem::factory()->create(['category_id' => $this->category->id]);

        $this->get('/menu')->assertOk();
        $this->assertTrue(Cache::has('public.menu.index'));

        $this->actingAs($this->admin)->delete("/admin/menu/{$item->id}");

        $this->assertFalse(Cache::has('public.menu.index'));
    }

    public function test_toggle_availability_invalidates_public_menu_cache(): void
    {
        $item = MenuItem::factory()->create(['category_id' => $this->category->id, 'is_available' => true]);

        $this->get('/menu')->assertOk();
        $this->assertTrue(Cache::has('public.menu.index'));

        $this->actingAs($this->admin)->patch("/admin/menu/{$item->id}/toggle");

        $this->assertFalse(Cache::has('public.menu.index'));
    }

    public function test_update_invalidates_our_menu_sections_cache(): void
    {
        $item = MenuItem::factory()->create(['category_id' => $this->category->id]);

        $this->get('/our-menu')->assertOk();
        $this->assertTrue(Cache::has('public.our-menu.sections'));

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'        => 'Updated Name',
            'category_id' => $this->category->id,
            'base_price'  => '14.00',
        ]);

        $this->assertFalse(Cache::has('public.our-menu.sections'));
    }

    public function test_destroy_invalidates_home_featured_cache(): void
    {
        $item = MenuItem::factory()->create(['category_id' => $this->category->id, 'is_featured' => true]);

        $this->get('/')->assertOk();
        $this->assertTrue(Cache::has('public.home.featured'));

        $this->actingAs($this->admin)->delete("/admin/menu/{$item->id}");

        $this->assertFalse(Cache::has('public.home.featured'));
    }
}
