<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuItemModelTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // MenuItem::isPizza()
    // -------------------------------------------------------------------------

    public function test_is_pizza_returns_true_when_category_slug_is_pizza(): void
    {
        $category = Category::factory()->create(['slug' => 'pizza']);
        $item = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($item->isPizza());
    }

    public function test_is_pizza_returns_false_when_category_slug_is_not_pizza(): void
    {
        $category = Category::factory()->create(['slug' => 'sides']);
        $item = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->assertFalse($item->isPizza());
    }

    public function test_is_pizza_returns_false_when_item_has_no_category(): void
    {
        // category_id is NOT NULL in the schema, so we create with a real category
        // then override the loaded relation to null — simulating what isPizza() sees
        // when $this->category is null (e.g. after eager loading with missing relation).
        $item = MenuItem::factory()->create();
        $item->setRelation('category', null);

        $this->assertFalse($item->isPizza());
    }

    // -------------------------------------------------------------------------
    // MenuItem::formattedPrice (getFormattedPriceAttribute)
    // -------------------------------------------------------------------------

    public function test_formatted_price_formats_whole_number_with_two_decimals(): void
    {
        $item = new MenuItem(['base_price' => 12.00]);

        $this->assertSame('£12.00', $item->formattedPrice);
    }

    public function test_formatted_price_formats_decimal_correctly(): void
    {
        $item = new MenuItem(['base_price' => 9.90]);

        $this->assertSame('£9.90', $item->formattedPrice);
    }

    public function test_formatted_price_formats_zero_price(): void
    {
        $item = new MenuItem(['base_price' => 0.00]);

        $this->assertSame('£0.00', $item->formattedPrice);
    }

    // -------------------------------------------------------------------------
    // MenuItem::hasStoredImage()
    // -------------------------------------------------------------------------

    public function test_has_stored_image_returns_false_when_image_path_is_null(): void
    {
        $item = new MenuItem(['image_path' => null]);

        $this->assertFalse($item->hasStoredImage());
    }

    public function test_has_stored_image_returns_false_when_file_does_not_exist_on_disk(): void
    {
        Storage::fake('public');

        $item = new MenuItem(['image_path' => 'menu/missing.jpg']);

        $this->assertFalse($item->hasStoredImage());
    }

    public function test_has_stored_image_returns_true_when_file_exists_on_disk(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('menu/test.jpg', '');

        $item = new MenuItem(['image_path' => 'menu/test.jpg']);

        $this->assertTrue($item->hasStoredImage());
    }
}
