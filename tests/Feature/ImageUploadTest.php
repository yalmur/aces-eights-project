<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('public');
    }

    public function test_admin_can_upload_image_when_creating_menu_item(): void
    {
        $category = Category::factory()->create();
        $image    = UploadedFile::fake()->image('pizza.jpg', 800, 600);

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'         => 'Test Pizza',
            'category_id'  => $category->id,
            'base_price'   => '12.50',
            'is_available' => '1',
            'image'        => $image,
        ]);

        $item = MenuItem::where('name', 'Test Pizza')->first();
        $this->assertNotNull($item->image_path);
        Storage::disk('public')->assertExists($item->image_path);
    }

    public function test_admin_can_upload_image_when_updating_menu_item(): void
    {
        $item  = MenuItem::factory()->create(['image_path' => null]);
        $image = UploadedFile::fake()->image('new.jpg', 800, 600);

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'         => $item->name,
            'category_id'  => $item->category_id,
            'base_price'   => $item->base_price,
            'is_available' => '1',
            'image'        => $image,
        ]);

        $item->refresh();
        $this->assertNotNull($item->image_path);
        Storage::disk('public')->assertExists($item->image_path);
    }

    public function test_old_image_deleted_when_new_image_uploaded(): void
    {
        Storage::disk('public')->put('menu/old.jpg', 'fake image');
        $item = MenuItem::factory()->create(['image_path' => 'menu/old.jpg']);

        $this->actingAs($this->admin)->put("/admin/menu/{$item->id}", [
            'name'         => $item->name,
            'category_id'  => $item->category_id,
            'base_price'   => $item->base_price,
            'is_available' => '1',
            'image'        => UploadedFile::fake()->image('replacement.jpg'),
        ]);

        Storage::disk('public')->assertMissing('menu/old.jpg');
        Storage::disk('public')->assertExists($item->fresh()->image_path);
    }

    public function test_image_is_optional(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)->post('/admin/menu', [
            'name'        => 'No Image Pizza',
            'category_id' => $category->id,
            'base_price'  => '10.00',
        ]);

        $item = MenuItem::where('name', 'No Image Pizza')->first();
        $this->assertNull($item?->image_path);
    }
}
