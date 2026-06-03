# Aces & Eights Pizza — Plan 4: Database Foundation & Menu Backend

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the full database schema, Eloquent models, real menu seeders, and wire the customer menu page + admin menu management to the database — replacing all hardcoded data with live DB queries.

**Architecture:** TDD throughout — every model, relationship, and controller method gets a test before implementation. Seeders load the real menu from `FINAL_FULL_MENU_COMPLETE.md` (starters, salads, pasta, 22 pizzas, tuna salads, feta salads, desserts, drinks). Customer menu page reads from DB via `MenuItem::with(['category','allergens'])`. Admin CRUD uses standard Laravel resource controller pattern with form validation. Alpine.js cart store's `baseIngredients` and `allToppings` will remain static for now — they get replaced with DB-driven API in Plan 5 (ordering backend).

**Tech Stack:** Laravel 11, MySQL 8, Eloquent ORM, PHPUnit/Pest, PHP 8.2 at `C:\xampp\php\php.exe`

---

## Schema Overview

```
categories              id, name, slug, sort_order
menu_items              id, category_id, name, slug, description, base_price, image_path, is_available, is_featured, sort_order
base_ingredients        id, menu_item_id, name, sort_order     ← per-pizza removable ingredients
toppings                id, name, price, is_available, sort_order  ← global add-on list
allergens               id, name, icon, is_visible, sort_order
allergen_menu_item      menu_item_id, allergen_id               ← pivot
delivery_zones          id, name, min_km, max_km, fee, is_active, sort_order
```

---

## File Map

| Action | File |
|--------|------|
| Create | `database/migrations/2026_06_03_000001_create_categories_table.php` |
| Create | `database/migrations/2026_06_03_000002_create_menu_items_table.php` |
| Create | `database/migrations/2026_06_03_000003_create_base_ingredients_table.php` |
| Create | `database/migrations/2026_06_03_000004_create_toppings_table.php` |
| Create | `database/migrations/2026_06_03_000005_create_allergens_table.php` |
| Create | `database/migrations/2026_06_03_000006_create_allergen_menu_item_table.php` |
| Create | `database/migrations/2026_06_03_000007_create_delivery_zones_table.php` |
| Create | `app/Models/Category.php` |
| Create | `app/Models/MenuItem.php` |
| Create | `app/Models/BaseIngredient.php` |
| Create | `app/Models/Topping.php` |
| Create | `app/Models/Allergen.php` |
| Create | `app/Models/DeliveryZone.php` |
| Create | `database/factories/CategoryFactory.php` |
| Create | `database/factories/MenuItemFactory.php` |
| Create | `database/factories/ToppingFactory.php` |
| Create | `database/seeders/CategorySeeder.php` |
| Create | `database/seeders/MenuItemSeeder.php` |
| Create | `database/seeders/ToppingSeeder.php` |
| Create | `database/seeders/AllergenSeeder.php` |
| Create | `database/seeders/DeliveryZoneSeeder.php` |
| Modify | `database/seeders/DatabaseSeeder.php` |
| Modify | `app/Http/Controllers/MenuController.php` |
| Modify | `app/Http/Controllers/Admin/MenuItemController.php` |
| Modify | `resources/views/menu/index.blade.php` |
| Modify | `resources/views/admin/menu/index.blade.php` |
| Modify | `resources/views/admin/menu/edit.blade.php` |
| Create | `tests/Feature/MenuTest.php` |
| Create | `tests/Feature/Admin/MenuItemTest.php` |

---

## Task 1: Migrations

**Files:** 7 migration files

- [ ] **Step 1: Create the migrations**

```bash
cd C:\AcesAndEightsPizza\webapp
& "C:\xampp\php\php.exe" artisan make:migration create_categories_table
& "C:\xampp\php\php.exe" artisan make:migration create_menu_items_table
& "C:\xampp\php\php.exe" artisan make:migration create_base_ingredients_table
& "C:\xampp\php\php.exe" artisan make:migration create_toppings_table
& "C:\xampp\php\php.exe" artisan make:migration create_allergens_table
& "C:\xampp\php\php.exe" artisan make:migration create_allergen_menu_item_table
& "C:\xampp\php\php.exe" artisan make:migration create_delivery_zones_table
```

- [ ] **Step 2: Write categories migration**

Find the newest `..._create_categories_table.php` in `database/migrations/`. Replace its contents:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

- [ ] **Step 3: Write menu_items migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('base_price', 8, 2);
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
```

- [ ] **Step 4: Write base_ingredients migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('base_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_ingredients');
    }
};
```

- [ ] **Step 5: Write toppings migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toppings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 5, 2);
            $table->boolean('is_available')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toppings');
    }
};
```

- [ ] **Step 6: Write allergens migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allergens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->default('warning');
            $table->boolean('is_visible')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allergens');
    }
};
```

- [ ] **Step 7: Write allergen_menu_item pivot migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allergen_menu_item', function (Blueprint $table) {
            $table->foreignId('allergen_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->primary(['allergen_id', 'menu_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allergen_menu_item');
    }
};
```

- [ ] **Step 8: Write delivery_zones migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('min_km', 5, 2)->default(0);
            $table->decimal('max_km', 5, 2);
            $table->decimal('fee', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
```

- [ ] **Step 9: Run all migrations**

```bash
& "C:\xampp\php\php.exe" artisan migrate
```

Expected: all 7 new tables created, ending with `Migrated` for each.

- [ ] **Step 10: Commit**

```bash
git add database/migrations/
git commit -m "feat: add full menu schema migrations"
```

---

## Task 2: Eloquent Models

**Files:** 6 model files

- [ ] **Step 1: Create models**

```bash
& "C:\xampp\php\php.exe" artisan make:model Category
& "C:\xampp\php\php.exe" artisan make:model MenuItem
& "C:\xampp\php\php.exe" artisan make:model BaseIngredient
& "C:\xampp\php\php.exe" artisan make:model Topping
& "C:\xampp\php\php.exe" artisan make:model Allergen
& "C:\xampp\php\php.exe" artisan make:model DeliveryZone
```

- [ ] **Step 2: Write Category model**

Replace `app/Models/Category.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'sort_order'];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    public function availableItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->where('is_available', true)
            ->orderBy('sort_order');
    }
}
```

- [ ] **Step 3: Write MenuItem model**

Replace `app/Models/MenuItem.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'base_price', 'image_path', 'is_available', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'base_price'    => 'decimal:2',
        'is_available'  => 'boolean',
        'is_featured'   => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function baseIngredients(): HasMany
    {
        return $this->hasMany(BaseIngredient::class)->orderBy('sort_order');
    }

    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class);
    }

    public function isPizza(): bool
    {
        return $this->category->slug === 'pizza';
    }

    public function getFormattedPriceAttribute(): string
    {
        return '£' . number_format($this->base_price, 2);
    }
}
```

- [ ] **Step 4: Write BaseIngredient model**

Replace `app/Models/BaseIngredient.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaseIngredient extends Model
{
    use HasFactory;

    protected $fillable = ['menu_item_id', 'name', 'sort_order'];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
```

- [ ] **Step 5: Write Topping model**

Replace `app/Models/Topping.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'is_available', 'sort_order'];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->orderBy('sort_order');
    }
}
```

- [ ] **Step 6: Write Allergen model**

Replace `app/Models/Allergen.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Allergen extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'is_visible', 'sort_order'];

    protected $casts = ['is_visible' => 'boolean'];

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class);
    }
}
```

- [ ] **Step 7: Write DeliveryZone model**

Replace `app/Models/DeliveryZone.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'min_km', 'max_km', 'fee', 'is_active', 'sort_order'];

    protected $casts = [
        'min_km'    => 'decimal:2',
        'max_km'    => 'decimal:2',
        'fee'       => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
```

- [ ] **Step 8: Commit**

```bash
git add app/Models/
git commit -m "feat: add Eloquent models with relationships"
```

---

## Task 3: Factories + Model Tests

**Files:** 3 factory files, 1 test file

- [ ] **Step 1: Write CategoryFactory**

Create `database/factories/CategoryFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        return [
            'name'       => ucfirst($name),
            'slug'       => Str::slug($name),
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
```

- [ ] **Step 2: Write MenuItemFactory**

Create `database/factories/MenuItemFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuItemFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        return [
            'category_id'  => Category::factory(),
            'name'         => ucwords($name),
            'slug'         => Str::slug($name),
            'description'  => $this->faker->sentence(),
            'base_price'   => $this->faker->randomFloat(2, 5, 25),
            'image_path'   => null,
            'is_available' => true,
            'is_featured'  => false,
            'sort_order'   => $this->faker->numberBetween(1, 50),
        ];
    }

    public function unavailable(): static
    {
        return $this->state(['is_available' => false]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
```

- [ ] **Step 3: Write ToppingFactory**

Create `database/factories/ToppingFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ToppingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'         => ucwords($this->faker->unique()->words(2, true)),
            'price'        => $this->faker->randomFloat(2, 1, 3),
            'is_available' => true,
            'sort_order'   => $this->faker->numberBetween(1, 30),
        ];
    }
}
```

- [ ] **Step 4: Write model tests**

Create `tests/Feature/MenuTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Topping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_menu_items(): void
    {
        $category = Category::factory()->create();
        MenuItem::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->menuItems);
    }

    public function test_menu_item_belongs_to_category(): void
    {
        $item = MenuItem::factory()->create();

        $this->assertInstanceOf(Category::class, $item->category);
    }

    public function test_menu_item_has_formatted_price(): void
    {
        $item = MenuItem::factory()->create(['base_price' => 12.50]);

        $this->assertSame('£12.50', $item->formatted_price);
    }

    public function test_menu_item_can_have_allergens(): void
    {
        $item = MenuItem::factory()->create();
        $allergen = Allergen::factory()->create(['name' => 'Gluten']);
        $item->allergens()->attach($allergen);

        $this->assertCount(1, $item->allergens);
        $this->assertSame('Gluten', $item->allergens->first()->name);
    }

    public function test_topping_available_scope(): void
    {
        Topping::factory()->create(['is_available' => true]);
        Topping::factory()->create(['is_available' => false]);

        $this->assertCount(1, Topping::available()->get());
    }

    public function test_customer_menu_page_returns_200(): void
    {
        Category::factory()->create(['slug' => 'pizza', 'name' => 'Pizza']);

        $response = $this->get('/menu');

        $response->assertStatus(200);
    }

    public function test_customer_menu_shows_available_items(): void
    {
        $category = Category::factory()->create(['slug' => 'pizza', 'name' => 'Pizza']);
        $available = MenuItem::factory()->create([
            'category_id' => $category->id,
            'name'        => 'Test Margherita',
            'is_available'=> true,
        ]);
        $unavailable = MenuItem::factory()->unavailable()->create([
            'category_id' => $category->id,
            'name'        => 'Hidden Pizza',
        ]);

        $response = $this->get('/menu');

        $response->assertSee('Test Margherita');
        $response->assertDontSee('Hidden Pizza');
    }
}
```

- [ ] **Step 5: Add Allergen factory**

Create `database/factories/AllergenFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AllergenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->unique()->word(),
            'icon'       => 'warning',
            'is_visible' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
```

- [ ] **Step 6: Run tests — expect failures on menu page test (MenuController not yet updated)**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/MenuTest.php
```

Expected failures: `test_customer_menu_page_returns_200` and `test_customer_menu_shows_available_items` may fail if MenuController does not yet use DB. The model relationship tests should PASS.

- [ ] **Step 7: Commit**

```bash
git add database/factories/ tests/Feature/MenuTest.php
git commit -m "test: add model factories and menu feature tests"
```

---

## Task 4: Seeders (Real Menu Data)

**Files:** 5 seeders + DatabaseSeeder update

- [ ] **Step 1: Write CategorySeeder**

Create `database/seeders/CategorySeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Starters',      'slug' => 'starters',      'sort_order' => 1],
            ['name' => 'Salads',        'slug' => 'salads',        'sort_order' => 2],
            ['name' => 'Pasta',         'slug' => 'pasta',         'sort_order' => 3],
            ['name' => 'Pizza',         'slug' => 'pizza',         'sort_order' => 4],
            ['name' => 'Tuna Salads',   'slug' => 'tuna-salads',   'sort_order' => 5],
            ['name' => 'Feta Salads',   'slug' => 'feta-salads',   'sort_order' => 6],
            ['name' => 'Desserts',      'slug' => 'desserts',      'sort_order' => 7],
            ['name' => 'Drinks',        'slug' => 'drinks',        'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
```

- [ ] **Step 2: Write AllergenSeeder**

Create `database/seeders/AllergenSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Allergen;
use Illuminate\Database\Seeder;

class AllergenSeeder extends Seeder
{
    public function run(): void
    {
        $allergens = [
            ['name' => 'Gluten',     'icon' => 'bakery_dining', 'sort_order' => 1],
            ['name' => 'Dairy',      'icon' => 'egg',           'sort_order' => 2],
            ['name' => 'Eggs',       'icon' => 'egg',           'sort_order' => 3],
            ['name' => 'Nuts',       'icon' => 'nutrition',     'sort_order' => 4],
            ['name' => 'Soy',        'icon' => 'grass',         'sort_order' => 5],
            ['name' => 'Shellfish',  'icon' => 'set_meal',      'sort_order' => 6],
            ['name' => 'Celery',     'icon' => 'eco',           'sort_order' => 7],
            ['name' => 'Sulphites',  'icon' => 'science',       'sort_order' => 8],
        ];

        foreach ($allergens as $a) {
            Allergen::updateOrCreate(['name' => $a['name']], $a);
        }
    }
}
```

- [ ] **Step 3: Write ToppingSeeder**

Create `database/seeders/ToppingSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;

class ToppingSeeder extends Seeder
{
    public function run(): void
    {
        $toppings = [
            ['name' => 'Aubergines',         'price' => 2.00, 'sort_order' => 1],
            ['name' => 'Mixed Peppers',       'price' => 1.50, 'sort_order' => 2],
            ['name' => 'Mushrooms',           'price' => 1.50, 'sort_order' => 3],
            ['name' => 'Regular Pepperoni',   'price' => 2.00, 'sort_order' => 4],
            ['name' => 'Nduja',               'price' => 2.00, 'sort_order' => 5],
            ['name' => 'Spicy Ground Beef',   'price' => 2.00, 'sort_order' => 6],
            ['name' => 'Broccoli',            'price' => 2.00, 'sort_order' => 7],
            ['name' => 'Parmesan',            'price' => 2.00, 'sort_order' => 8],
            ['name' => 'Pine Nuts',           'price' => 1.50, 'sort_order' => 9],
            ['name' => 'Garlic Oil',          'price' => 1.50, 'sort_order' => 10],
            ['name' => 'Mozzarella',          'price' => 2.00, 'sort_order' => 11],
            ['name' => 'Olive Oil',           'price' => 1.50, 'sort_order' => 12],
            ['name' => 'Smoky Pancetta',      'price' => 2.00, 'sort_order' => 13],
            ['name' => 'Tomato Sauce',        'price' => 1.00, 'sort_order' => 14],
            ['name' => 'Basil',              'price' => 0.50, 'sort_order' => 15],
            ['name' => 'Red Onion',           'price' => 1.50, 'sort_order' => 16],
            ['name' => 'Anchovies',           'price' => 2.00, 'sort_order' => 17],
            ['name' => 'Chilli Flakes',       'price' => 1.00, 'sort_order' => 18],
            ['name' => 'Whole Black Olives',  'price' => 1.50, 'sort_order' => 19],
            ['name' => 'Oregano',             'price' => 0.50, 'sort_order' => 20],
            ['name' => 'Vegan Mozzarella',    'price' => 2.50, 'sort_order' => 21],
            ['name' => 'Sicilian Sausage',    'price' => 2.00, 'sort_order' => 22],
            ['name' => 'Hot Honey',           'price' => 2.00, 'sort_order' => 23],
            ['name' => 'Speck Ham',           'price' => 2.00, 'sort_order' => 24],
            ['name' => 'Provolone Picante',   'price' => 1.50, 'sort_order' => 25],
        ];

        foreach ($toppings as $t) {
            Topping::updateOrCreate(['name' => $t['name']], $t);
        }
    }
}
```

- [ ] **Step 4: Write DeliveryZoneSeeder**

Create `database/seeders/DeliveryZoneSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Zone 1 — Local (0–1.5km)',  'min_km' => 0.00, 'max_km' => 1.50, 'fee' => 2.50, 'is_active' => true,  'sort_order' => 1],
            ['name' => 'Zone 2 — Near (1.5–3km)',   'min_km' => 1.50, 'max_km' => 3.00, 'fee' => 3.50, 'is_active' => true,  'sort_order' => 2],
            ['name' => 'Zone 3 — Extended (3–5km)', 'min_km' => 3.00, 'max_km' => 5.00, 'fee' => 4.50, 'is_active' => true,  'sort_order' => 3],
            ['name' => 'Zone 4 — Far (5–8km)',      'min_km' => 5.00, 'max_km' => 8.00, 'fee' => 5.50, 'is_active' => false, 'sort_order' => 4],
        ];

        foreach ($zones as $z) {
            DeliveryZone::updateOrCreate(['name' => $z['name']], $z);
        }
    }
}
```

- [ ] **Step 5: Write MenuItemSeeder**

Create `database/seeders/MenuItemSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Allergen;
use App\Models\BaseIngredient;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch category IDs
        $cats = Category::pluck('id', 'slug');

        // Fetch allergen IDs
        $allergenIds = Allergen::pluck('id', 'name');

        $items = [
            // ---- STARTERS ----
            ['category' => 'starters', 'name' => 'Olives',              'desc' => 'Sicilian green olives.',                                                    'price' => 4.50, 'allergens' => []],
            ['category' => 'starters', 'name' => 'Basket of Bread',     'desc' => 'Fresh homemade focaccia with olive oil.',                                   'price' => 4.50, 'allergens' => ['Gluten']],
            ['category' => 'starters', 'name' => 'Garlic Bread',        'desc' => 'Tomato sauce and garlic.',                                                  'price' => 5.00, 'allergens' => ['Gluten']],
            ['category' => 'starters', 'name' => 'Antipasto Italiano',  'desc' => 'Premium Italian cheeses, cured meats and bread.',                          'price' => 9.50, 'allergens' => ['Gluten', 'Dairy']],
            ['category' => 'starters', 'name' => 'Caprese',             'desc' => 'Buffalo mozzarella, heritage tomato and fresh basil.',                     'price' => 8.50, 'allergens' => ['Dairy']],
            ['category' => 'starters', 'name' => 'Bresaola',            'desc' => 'Sliced dry beef fillet, rocket, parmesan and lemon.',                      'price' => 9.00, 'allergens' => ['Dairy']],
            ['category' => 'starters', 'name' => 'Tricolore',           'desc' => 'Buffalo mozzarella, tomato, avocado, basil and extra virgin olive oil.',   'price' => 8.00, 'allergens' => ['Dairy']],

            // ---- SALADS ----
            ['category' => 'salads', 'name' => 'Insalata Verde',         'desc' => 'Mixed leaves, avocado, fennel, cucumber.',                                          'price' => 7.50, 'allergens' => []],
            ['category' => 'salads', 'name' => 'Insalata Mista',         'desc' => 'Mixed leaves, tomato, red onion, peppers.',                                         'price' => 7.00, 'allergens' => []],
            ['category' => 'salads', 'name' => 'Rucola e Parmigiana',    'desc' => 'Rocket, cherry tomatoes, parmesan.',                                                'price' => 8.00, 'allergens' => ['Dairy']],
            ['category' => 'salads', 'name' => 'Chicken Avocado Salad',  'desc' => 'Grilled chicken, avocado, mixed leaves, croutons and parmesan.',                   'price' => 11.00, 'allergens' => ['Gluten', 'Dairy']],
            ['category' => 'salads', 'name' => 'Insalata di Carciofi',   'desc' => 'Artichoke salad, baby spinach, parmesan, balsamic vinegar, hazelnut, pomegranate.','price' => 10.50, 'allergens' => ['Dairy', 'Nuts']],
            ['category' => 'salads', 'name' => 'Insalata Mediterranea',  'desc' => 'Avocado, cherry tomato, baby gem, radicchio lettuce, fennel and lemon dressing.',  'price' => 8.50, 'allergens' => []],
            ['category' => 'salads', 'name' => 'Insalata Cesare',        'desc' => 'Slow roast chicken, baby gem, boiled egg, anchovies, fried capers, Caesar dressing.','price' => 11.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs', 'Shellfish']],

            // ---- PASTA ----
            ['category' => 'pasta', 'name' => 'Lasagna (Beef)',              'desc' => 'Layers of pasta with rich beef ragù, béchamel sauce and cheese.',           'price' => 13.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'pasta', 'name' => 'Cannelloni (Spinach & Ricotta)', 'desc' => 'Pasta tubes filled with spinach and ricotta.',                           'price' => 13.00, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'pasta', 'name' => 'Melanzane Parmigiana',        'desc' => 'Layers of aubergine, tomato sauce, parmesan and mozzarella.',               'price' => 12.50, 'allergens' => ['Dairy']],

            // ---- PIZZA ----
            ['category' => 'pizza', 'name' => 'Garlic Bread Pizza',  'desc' => 'Tomato sauce and garlic.',                                                                           'price' => 10.50, 'allergens' => ['Gluten'], 'ingredients' => ['Tomato Sauce', 'Garlic', 'Olive Oil']],
            ['category' => 'pizza', 'name' => 'Vegan Pizza',         'desc' => 'Tomato sauce, garlic, avocado, onion, rocket and lemon juice.',                                      'price' => 13.50, 'allergens' => ['Gluten'], 'ingredients' => ['Tomato Sauce', 'Garlic', 'Avocado', 'Red Onion', 'Rocket']],
            ['category' => 'pizza', 'name' => 'Margherita',          'desc' => 'Tomato sauce, mozzarella.',                                                                          'price' => 12.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella']],
            ['category' => 'pizza', 'name' => 'Honey Mushroom',      'desc' => 'Tomato sauce, mozzarella, mushroom, spinach, blue cheese, honey and olive oil.',                    'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Mushroom', 'Spinach', 'Blue Cheese', 'Honey']],
            ['category' => 'pizza', 'name' => 'Quattro Formaggi',    'desc' => 'Tomato sauce, mozzarella, gorgonzola piccante, dolcelatte and parmesan.',                           'price' => 15.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Gorgonzola', 'Dolcelatte', 'Parmesan']],
            ['category' => 'pizza', 'name' => 'Veggie',              'desc' => 'Tomato sauce, mozzarella, aubergine, courgette, onion, provolone cheese and garlic.',               'price' => 14.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Aubergine', 'Courgette', 'Red Onion', 'Provolone']],
            ['category' => 'pizza', 'name' => 'Caprino',             'desc' => 'Tomato sauce, mozzarella, goats cheese, asparagus and parmesan.',                                   'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Goats Cheese', 'Asparagus', 'Parmesan']],
            ['category' => 'pizza', 'name' => 'Squash',              'desc' => 'Tomato sauce, mozzarella, squash, feta cheese and garlic.',                                         'price' => 14.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Squash', 'Feta Cheese', 'Garlic']],
            ['category' => 'pizza', 'name' => 'Aces',                'desc' => 'Tomato sauce, mozzarella, goats cheese, peppers and rocket.',                                       'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Goats Cheese', 'Mixed Peppers', 'Rocket']],
            ['category' => 'pizza', 'name' => 'Vegetariana',         'desc' => 'Tomato sauce, mozzarella, olives, mushrooms, peppers and spinach.',                                 'price' => 14.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Olives', 'Mushrooms', 'Mixed Peppers', 'Spinach']],
            ['category' => 'pizza', 'name' => 'Saporita',            'desc' => 'Tomato sauce, mozzarella, buffalo mozzarella, gorgonzola, rocket, pesto, cherry tomatoes.',        'price' => 16.50, 'allergens' => ['Gluten', 'Dairy', 'Nuts'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Buffalo Mozzarella', 'Gorgonzola', 'Rocket', 'Pesto']],
            ['category' => 'pizza', 'name' => 'Ferrari',             'desc' => 'Tomato sauce, mozzarella, pepperoni and mushroom.',                                                 'price' => 14.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pepperoni', 'Mushrooms']],
            ['category' => 'pizza', 'name' => 'Napoli',              'desc' => 'Tomato sauce, mozzarella, anchovies, capers and olives.',                                           'price' => 14.50, 'allergens' => ['Gluten', 'Dairy', 'Shellfish'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Anchovies', 'Capers', 'Olives']],
            ['category' => 'pizza', 'name' => 'Burrata',             'desc' => 'Cherry tomatoes, garlic, basil, burrata and balsamic glaze.',                                       'price' => 16.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Cherry Tomatoes', 'Garlic', 'Basil', 'Burrata', 'Balsamic Glaze']],
            ['category' => 'pizza', 'name' => 'Hawaii',              'desc' => 'Tomato sauce, mozzarella, ham and pineapple.',                                                      'price' => 13.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Ham', 'Pineapple']],
            ['category' => 'pizza', 'name' => 'Punta Luzzi',         'desc' => 'Tomato sauce, mozzarella, pear, ham and gorgonzola.',                                               'price' => 15.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pear', 'Ham', 'Gorgonzola']],
            ['category' => 'pizza', 'name' => 'Eights',              'desc' => 'Tomato sauce, mozzarella, peppers, onions, chorizo and mushroom.',                                  'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Mixed Peppers', 'Red Onion', 'Chorizo', 'Mushrooms']],
            ['category' => 'pizza', 'name' => 'Quattro Stagioni',    'desc' => 'Tomato sauce, mozzarella, pepperoni, ham, olives and mushroom.',                                   'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pepperoni', 'Ham', 'Olives', 'Mushrooms']],
            ['category' => 'pizza', 'name' => 'Piccante',            'desc' => 'Tomato sauce, mozzarella, pepperoni, jalapeño chillies and ham.',                                   'price' => 14.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pepperoni', 'Jalapeño Chillies', 'Ham']],
            ['category' => 'pizza', 'name' => 'Pancetta',            'desc' => 'Tomato sauce, mozzarella, roasted peppers, onion and pancetta.',                                    'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Roasted Peppers', 'Red Onion', 'Pancetta']],
            ['category' => 'pizza', 'name' => 'Il Bacio',            'desc' => 'Tomato sauce, mozzarella, Parma ham, asparagus, rocket and parmesan.',                              'price' => 16.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Parma Ham', 'Asparagus', 'Rocket', 'Parmesan']],
            ['category' => 'pizza', 'name' => 'Meat Lover',          'desc' => 'Tomato sauce, mozzarella, pepperoni, ham, chorizo, nduja and pancetta.',                            'price' => 17.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pepperoni', 'Ham', 'Chorizo', 'Nduja', 'Pancetta']],

            // ---- TUNA SALADS ----
            ['category' => 'tuna-salads', 'name' => 'Italian Tuna Salad',       'desc' => 'Tuna, mixed leaves, cherry tomatoes, red onion, olives and cucumber.',      'price' => 10.50, 'allergens' => ['Shellfish']],
            ['category' => 'tuna-salads', 'name' => 'Mediterranean Tuna Salad', 'desc' => 'Tuna, baby gem lettuce, avocado, cherry tomato and fennel.',                'price' => 10.50, 'allergens' => ['Shellfish']],
            ['category' => 'tuna-salads', 'name' => 'Sicilian Tuna Salad',      'desc' => 'Tuna, orange segments, fennel, rocket and olives.',                        'price' => 10.50, 'allergens' => ['Shellfish']],
            ['category' => 'tuna-salads', 'name' => 'Tuna & Mozzarella',        'desc' => 'Tuna, buffalo mozzarella, tomato and basil.',                              'price' => 11.00, 'allergens' => ['Dairy', 'Shellfish']],
            ['category' => 'tuna-salads', 'name' => 'Tuna Caesar',              'desc' => 'Tuna, baby gem lettuce, parmesan, croutons and Caesar dressing.',         'price' => 11.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs', 'Shellfish']],

            // ---- FETA SALADS ----
            ['category' => 'feta-salads', 'name' => 'Greek Feta Salad',             'desc' => 'Feta cheese, cucumber, cherry tomatoes, red onion and olives.',     'price' => 9.50,  'allergens' => ['Dairy']],
            ['category' => 'feta-salads', 'name' => 'Feta & Avocado Salad',         'desc' => 'Feta cheese, avocado, mixed leaves and cucumber.',                  'price' => 9.50,  'allergens' => ['Dairy']],
            ['category' => 'feta-salads', 'name' => 'Mediterranean Feta Salad',     'desc' => 'Feta cheese, rocket, cherry tomatoes, fennel and olives.',          'price' => 9.50,  'allergens' => ['Dairy']],
            ['category' => 'feta-salads', 'name' => 'Watermelon & Feta Salad',      'desc' => 'Fresh watermelon, feta cheese, mint and rocket.',                   'price' => 10.00, 'allergens' => ['Dairy']],
            ['category' => 'feta-salads', 'name' => 'Roasted Beetroot & Feta Salad','desc' => 'Roasted beetroot, feta cheese, rocket and walnuts.',               'price' => 10.00, 'allergens' => ['Dairy', 'Nuts']],

            // ---- DESSERTS ----
            ['category' => 'desserts', 'name' => 'Sammontana Ice Cream (500g)',   'desc' => 'Premium Italian ice cream.',                              'price' => 7.00, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Neapolitan Baba',              'desc' => 'Traditional rum flavour sponge (no alcohol).',            'price' => 6.50, 'allergens' => ['Gluten', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Sicilian Cannoli',             'desc' => 'Filled pastry with ricotta cream.',                       'price' => 6.50, 'allergens' => ['Gluten', 'Dairy']],
            ['category' => 'desserts', 'name' => 'Tiramisù',                     'desc' => 'Coffee-soaked sponge, mascarpone and cocoa.',             'price' => 7.00, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Gluten Free Tiramisù',         'desc' => 'Gluten free tiramisù.',                                   'price' => 7.50, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Torta Della Nonna',            'desc' => 'Traditional custard tart.',                              'price' => 6.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Strawberry Cheesecake',        'desc' => 'Gluten free cheesecake.',                                'price' => 6.50, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Salted Caramel Cheesecake',    'desc' => 'Gluten free cheesecake.',                                'price' => 6.50, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Ricotta & Pistachio Cake',     'desc' => 'Italian ricotta cake.',                                  'price' => 7.00, 'allergens' => ['Dairy', 'Eggs', 'Nuts']],
            ['category' => 'desserts', 'name' => 'Apple & Frangipane Cake',      'desc' => 'Apple almond cake.',                                     'price' => 6.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs', 'Nuts']],
            ['category' => 'desserts', 'name' => 'Sicilian Pistachio Mousse',    'desc' => 'Rich pistachio mousse.',                                 'price' => 6.50, 'allergens' => ['Dairy', 'Nuts']],
            ['category' => 'desserts', 'name' => 'Delizia Limone',               'desc' => 'Italian lemon dessert.',                                 'price' => 6.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Lemon Tart',                   'desc' => 'Gluten free lemon tart.',                                'price' => 6.50, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Milk Chocolate Profiteroles',  'desc' => 'Chocolate profiteroles.',                                'price' => 7.00, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],

            // ---- DRINKS ----
            ['category' => 'drinks', 'name' => 'Coca-Cola',                       'desc' => 'Classic sparkling drink.',             'price' => 3.00, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Diet Coca-Cola',                  'desc' => 'Sugar-free cola.',                     'price' => 3.00, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Coca-Cola Zero',                  'desc' => 'Zero sugar cola.',                     'price' => 3.00, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'San Pellegrino Orange',           'desc' => 'Italian sparkling orange drink.',      'price' => 3.50, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'San Pellegrino Lemon',            'desc' => 'Italian sparkling lemon drink.',       'price' => 3.50, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Tomarchio Peach & Melon Tea',     'desc' => 'Italian iced tea.',                    'price' => 3.50, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Tomarchio Lemon & Tangerine Tea', 'desc' => 'Italian iced tea.',                    'price' => 3.50, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Zuegg Juice',                     'desc' => 'Orange, peach, pear, apricot, apple or blood orange.',  'price' => 3.50, 'allergens' => []],
        ];

        $sortByCategory = [];
        foreach ($items as $data) {
            $catSlug = $data['category'];
            if (!isset($sortByCategory[$catSlug])) $sortByCategory[$catSlug] = 1;

            $item = MenuItem::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['name'])],
                [
                    'category_id'  => $cats[$catSlug],
                    'name'         => $data['name'],
                    'slug'         => \Illuminate\Support\Str::slug($data['name']),
                    'description'  => $data['desc'],
                    'base_price'   => $data['price'],
                    'is_available' => true,
                    'sort_order'   => $sortByCategory[$catSlug]++,
                ]
            );

            // Allergens
            if (!empty($data['allergens'])) {
                $ids = collect($data['allergens'])
                    ->map(fn ($n) => $allergenIds[$n] ?? null)
                    ->filter()
                    ->values()
                    ->all();
                $item->allergens()->sync($ids);
            }

            // Base ingredients (pizza only)
            if (!empty($data['ingredients'])) {
                $item->baseIngredients()->delete();
                foreach ($data['ingredients'] as $i => $ingName) {
                    BaseIngredient::create([
                        'menu_item_id' => $item->id,
                        'name'         => $ingName,
                        'sort_order'   => $i + 1,
                    ]);
                }
            }
        }
    }
}
```

- [ ] **Step 6: Update DatabaseSeeder**

Open `database/seeders/DatabaseSeeder.php`. Replace `run()` with:

```php
public function run(): void
{
    $this->call([
        AdminUserSeeder::class,
        CategorySeeder::class,
        AllergenSeeder::class,
        ToppingSeeder::class,
        DeliveryZoneSeeder::class,
        MenuItemSeeder::class,
    ]);
}
```

- [ ] **Step 7: Run seeders**

```bash
& "C:\xampp\php\php.exe" artisan db:seed
```

Expected output: `INFO  Seeding database.` with no errors.

- [ ] **Step 8: Verify data**

```bash
& "C:\xampp\php\php.exe" artisan tinker --execute="echo App\Models\MenuItem::count() . ' items, ' . App\Models\Category::count() . ' categories, ' . App\Models\Topping::count() . ' toppings';"
```

Expected: `65 items, 8 categories, 25 toppings` (approximately — count may vary).

- [ ] **Step 9: Commit**

```bash
git add database/seeders/
git commit -m "feat: add full menu seeders with real data (65 items, 8 categories, 25 toppings)"
```

---

## Task 5: Customer MenuController — Wire to DB

**Files:**
- Modify: `app/Http/Controllers/MenuController.php`

- [ ] **Step 1: Rewrite MenuController**

Replace `app/Http/Controllers/MenuController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Topping;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $categories = Category::with(['availableItems.allergens', 'availableItems.baseIngredients'])
            ->orderBy('sort_order')
            ->get();

        $toppings = Topping::available()->get();

        return view('menu.index', [
            'title'      => 'Order Now',
            'categories' => $categories,
            'toppings'   => $toppings,
        ]);
    }

    public function show(string $slug): View
    {
        $item = MenuItem::with(['category', 'allergens', 'baseIngredients'])
            ->where('slug', $slug)
            ->where('is_available', true)
            ->firstOrFail();

        return view('menu.show', [
            'title' => $item->name,
            'item'  => $item,
        ]);
    }
}
```

- [ ] **Step 2: Run failing MenuTest tests**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/MenuTest.php --filter=test_customer_menu
```

Expected: tests pass now that controller uses DB.

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/MenuController.php
git commit -m "feat: wire customer menu controller to database"
```

---

## Task 6: Update Customer Menu Blade View

**Files:**
- Modify: `resources/views/menu/index.blade.php`

The current menu view has hardcoded items in PHP arrays. Update it to use `$categories` and `$toppings` from the controller.

- [ ] **Step 1: Update the menu grid section**

Read the current `resources/views/menu/index.blade.php`. Keep the hero section, search/filter bar, and view toggle exactly as-is. Replace the hardcoded section blocks (Pizza section, Starters section, etc.) with a dynamic `@foreach` loop.

Replace everything from `{{-- Pizza items --}}` to the closing `</div>{{-- end x-data --}}` with:

```blade
{{-- Dynamic menu sections --}}
@foreach($categories as $category)
<section x-show="active === 'all' || active === '{{ $category->slug }}'"
         class="px-6 md:px-margin-desktop py-12 max-w-container-max mx-auto {{ !$loop->first ? 'border-t border-surface-variant' : '' }}">

  <h2 class="menu-section-heading" x-show="active === 'all'">{{ $category->name }}</h2>

  <div :class="view === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' : view === 'list' ? 'flex flex-col gap-3' : 'grid grid-cols-2 lg:grid-cols-4 gap-4'">

    @foreach($category->availableItems as $item)
    <div :class="view === 'list' ? 'flex flex-row' : 'flex flex-col'"
         class="group bg-surface-container-low border border-surface-variant hover:border-primary-container/30 transition-all duration-300 overflow-hidden shadow-sm">
      <div :class="view === 'list' ? 'w-32 h-auto flex-shrink-0' : view === 'compact' ? 'h-40 overflow-hidden' : 'h-64 overflow-hidden'"
           class="overflow-hidden">
        <img alt="{{ $item->name }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
             src="{{ $item->image_path ? asset('storage/' . $item->image_path) : 'https://placehold.co/400x300/e4e2e1/1b1c1c?text=' . urlencode($item->name) }}"/>
      </div>
      <div class="p-6 flex flex-col flex-1">
        <div class="flex justify-between items-start mb-2">
          <h3 class="font-headline-md text-headline-md text-on-surface">{{ $item->name }}</h3>
          <span class="font-label-bold text-headline-md text-primary-container">{{ $item->formatted_price }}</span>
        </div>
        <p class="font-body-md text-body-md text-on-surface-variant mb-6 flex-1">{{ $item->description }}</p>
        <div class="flex items-center justify-between mt-auto">
          <div class="flex gap-2 flex-wrap">
            @foreach($item->allergens as $allergen)
              <span class="font-label-sm text-label-sm px-2 py-1 bg-surface-container-high text-on-surface-variant border border-surface-variant uppercase">
                {{ strtoupper(substr($allergen->name, 0, 5)) }}
              </span>
            @endforeach
          </div>
          <button @click="$store.cart.openDrawer({
                    id: '{{ $item->slug }}',
                    name: '{{ addslashes($item->name) }}',
                    category: '{{ $item->category->slug }}',
                    basePrice: {{ $item->base_price }}
                  })"
                  class="btn-add w-12 h-12 flex items-center justify-center touch-manipulation">
            <span class="material-symbols-outlined text-white text-[20px]">add</span>
          </button>
        </div>
      </div>
    </div>
    @endforeach

  </div>
</section>
@endforeach

</div>{{-- end x-data --}}
```

Also update the category filter chips in the search bar to be dynamic. Find the hardcoded chips:

```blade
<button @click="active = 'all'"     :class="{ 'active': active === 'all' }"     class="chip whitespace-nowrap">All</button>
<button @click="active = 'pizzas'"  :class="{ 'active': active === 'pizzas' }"  class="chip whitespace-nowrap">Pizza</button>
...
```

Replace them with:

```blade
<button @click="active = 'all'" :class="{ 'active': active === 'all' }" class="chip whitespace-nowrap">All</button>
@foreach($categories as $category)
<button @click="active = '{{ $category->slug }}'"
        :class="{ 'active': active === '{{ $category->slug }}' }"
        class="chip whitespace-nowrap">{{ $category->name }}</button>
@endforeach
```

Also update the Alpine.js `x-data` to pass toppings from the controller into the store. Add a `x-init` on the outer div:

```blade
<div x-data="{ active: 'all', view: 'grid' }"
     x-init="
       $store.cart.allToppings = {{ $toppings->map(fn($t) => ['name' => $t->name, 'price' => (float)$t->price])->toJson() }};
     ">
```

This ensures the cart drawer always shows the live toppings from DB.

- [ ] **Step 2: Build assets and verify**

```bash
npm run build
```

Visit `http://localhost:8000/menu` (must be logged in if testing locally, or open in an unauthenticated browser — the menu route has no auth middleware). Verify:
- All 8 category chips show
- Menu items load from DB with real names and prices
- Clicking `+` opens customisation drawer with real item name and price

- [ ] **Step 3: Run tests**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/MenuTest.php
```

Expected: all tests pass.

- [ ] **Step 4: Commit**

```bash
git add resources/views/menu/index.blade.php
git commit -m "feat: wire customer menu page to database with dynamic categories and toppings"
```

---

## Task 7: Admin MenuItemController — Full CRUD

**Files:**
- Modify: `app/Http/Controllers/Admin/MenuItemController.php`

- [ ] **Step 1: Rewrite admin MenuItemController**

Replace `app/Http/Controllers/Admin/MenuItemController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\BaseIngredient;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(): View
    {
        $items = MenuItem::with(['category', 'allergens'])
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.menu.index', [
            'title' => 'Menu Management',
            'items' => $items,
        ]);
    }

    public function create(): View
    {
        return view('admin.menu.edit', [
            'title'     => 'Add Menu Item',
            'item'      => null,
            'categories'=> Category::orderBy('sort_order')->get(),
            'allergens' => Allergen::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'description'  => 'nullable|string',
            'base_price'   => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'is_featured'  => 'boolean',
            'allergens'    => 'nullable|array',
            'allergens.*'  => 'exists:allergens,id',
            'ingredients'  => 'nullable|array',
            'ingredients.*'=> 'string|max:100',
        ]);

        $item = MenuItem::create([
            'category_id'  => $data['category_id'],
            'name'         => $data['name'],
            'slug'         => Str::slug($data['name']),
            'description'  => $data['description'] ?? null,
            'base_price'   => $data['base_price'],
            'is_available' => $request->boolean('is_available'),
            'is_featured'  => $request->boolean('is_featured'),
            'sort_order'   => MenuItem::max('sort_order') + 1,
        ]);

        $item->allergens()->sync($data['allergens'] ?? []);
        $this->syncIngredients($item, $data['ingredients'] ?? []);

        return redirect()->route('admin.menu.index')
            ->with('success', "'{$item->name}' added to menu.");
    }

    public function edit(string $item): View
    {
        $menuItem = MenuItem::with(['allergens', 'baseIngredients'])->findOrFail($item);

        return view('admin.menu.edit', [
            'title'     => 'Edit: ' . $menuItem->name,
            'item'      => $menuItem,
            'categories'=> Category::orderBy('sort_order')->get(),
            'allergens' => Allergen::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, string $item): RedirectResponse
    {
        $menuItem = MenuItem::findOrFail($item);

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'description'  => 'nullable|string',
            'base_price'   => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'is_featured'  => 'boolean',
            'allergens'    => 'nullable|array',
            'allergens.*'  => 'exists:allergens,id',
            'ingredients'  => 'nullable|array',
            'ingredients.*'=> 'string|max:100',
        ]);

        $menuItem->update([
            'category_id'  => $data['category_id'],
            'name'         => $data['name'],
            'slug'         => Str::slug($data['name']),
            'description'  => $data['description'] ?? null,
            'base_price'   => $data['base_price'],
            'is_available' => $request->boolean('is_available'),
            'is_featured'  => $request->boolean('is_featured'),
        ]);

        $menuItem->allergens()->sync($data['allergens'] ?? []);
        $this->syncIngredients($menuItem, $data['ingredients'] ?? []);

        return redirect()->route('admin.menu.index')
            ->with('success', "'{$menuItem->name}' updated.");
    }

    public function destroy(string $item): RedirectResponse
    {
        $menuItem = MenuItem::findOrFail($item);
        $name = $menuItem->name;
        $menuItem->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', "'{$name}' removed from menu.");
    }

    private function syncIngredients(MenuItem $item, array $ingredients): void
    {
        $item->baseIngredients()->delete();
        foreach (array_values(array_filter($ingredients)) as $i => $name) {
            BaseIngredient::create([
                'menu_item_id' => $item->id,
                'name'         => $name,
                'sort_order'   => $i + 1,
            ]);
        }
    }
}
```

- [ ] **Step 2: Add store/update/destroy routes to web.php**

Open `routes/web.php`. Find the admin menu routes:

```php
    // Menu
    Route::get('/menu', [MenuItemController::class, 'index'])->name('menu.index');
    Route::get('/menu/create', [MenuItemController::class, 'create'])->name('menu.create');
    Route::get('/menu/{item}/edit', [MenuItemController::class, 'edit'])->name('menu.edit');
```

Replace with:

```php
    // Menu
    Route::get('/menu',             [MenuItemController::class, 'index'])->name('menu.index');
    Route::get('/menu/create',      [MenuItemController::class, 'create'])->name('menu.create');
    Route::post('/menu',            [MenuItemController::class, 'store'])->name('menu.store');
    Route::get('/menu/{item}/edit', [MenuItemController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{item}',      [MenuItemController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{item}',   [MenuItemController::class, 'destroy'])->name('menu.destroy');
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/MenuItemController.php routes/web.php
git commit -m "feat: admin menu CRUD with store/update/destroy"
```

---

## Task 8: Update Admin Menu Views

**Files:**
- Modify: `resources/views/admin/menu/index.blade.php`
- Modify: `resources/views/admin/menu/edit.blade.php`

- [ ] **Step 1: Update admin menu list view**

The current list uses hardcoded `@foreach` over a PHP array. Replace the hardcoded `@foreach` items section with a DB-driven one.

Open `resources/views/admin/menu/index.blade.php`. Find the `@foreach` loop that iterates over the hardcoded array and replace it with:

```blade
@foreach($items as $item)
<tr class="{{ !$item->is_available ? 'opacity-60' : '' }} hover:bg-surface-container-low transition-colors">
  <td class="px-6 py-4">
    <div class="flex items-center gap-4">
      <div class="w-14 h-14 bg-surface-container industrial-border overflow-hidden flex-shrink-0">
        <img src="{{ $item->image_path ? asset('storage/'.$item->image_path) : 'https://placehold.co/56x56/e4e2e1/1b1c1c?text=+' }}"
             alt="{{ $item->name }}" class="w-full h-full object-cover">
      </div>
      <div>
        <p class="font-mono text-xs font-bold text-on-surface">{{ $item->name }}</p>
        <p class="font-sans text-xs text-on-surface-variant">{{ $item->category->name }}</p>
      </div>
    </div>
  </td>
  <td class="px-6 py-4 font-sans text-sm">{{ $item->category->name }}</td>
  <td class="px-6 py-4 font-mono text-sm font-bold">{{ $item->formatted_price }}</td>
  <td class="px-6 py-4">
    <div class="flex justify-center" x-data="{ on: {{ $item->is_available ? 'true' : 'false' }} }">
      <label class="flex items-center cursor-pointer">
        <div class="relative">
          <input @change="on = $event.target.checked" :checked="on" class="sr-only" type="checkbox"/>
          <div :class="on ? 'bg-primary' : 'bg-[#2B2B2B]/30'" class="block w-10 h-6 rounded-full transition-colors">
            <div :class="on ? 'translate-x-4' : 'translate-x-1'" class="absolute top-1 left-0 bg-white w-4 h-4 rounded-full transition-transform"></div>
          </div>
        </div>
      </label>
    </div>
  </td>
  <td class="px-6 py-4 text-right">
    <div class="flex justify-end gap-2">
      <a href="{{ route('admin.menu.edit', $item->id) }}" class="p-2 hover:bg-surface-container rounded transition-colors" title="Edit">
        <span class="material-symbols-outlined text-on-surface-variant">edit</span>
      </a>
      <form method="POST" action="{{ route('admin.menu.destroy', $item->id) }}" onsubmit="return confirm('Delete {{ addslashes($item->name) }}?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="p-2 hover:bg-brand-error/10 rounded transition-colors" title="Delete">
          <span class="material-symbols-outlined text-brand-error">delete</span>
        </button>
      </form>
    </div>
  </td>
</tr>
@endforeach
```

Also update the "Showing X of Y" footer to use paginator:
```blade
<p>Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} menu items</p>
{{ $items->links() }}
```

Also add flash success message at the top of the page (after `@section('content')`):
```blade
@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">
    {{ session('success') }}
  </div>
@endif
```

- [ ] **Step 2: Update admin menu edit view**

The current edit form posts to `action="#"`. Update it to use real routes.

Open `resources/views/admin/menu/edit.blade.php`.

Find the `<form` tag and update:
- For create: `action="{{ route('admin.menu.store') }}" method="POST"`
- For edit: `action="{{ route('admin.menu.update', $item->id) }}" method="POST"` with `@method('PUT')` inside

Add this logic to the form opening:
```blade
<form class="grid grid-cols-1 lg:grid-cols-12 gap-gutter"
      method="POST"
      action="{{ $item ? route('admin.menu.update', $item->id) : route('admin.menu.store') }}"
      enctype="multipart/form-data">
  @csrf
  @if($item) @method('PUT') @endif
```

Update the name input to show current value:
```blade
<input ... value="{{ old('name', $item?->name) }}" name="name">
```

Update the category select to show current value:
```blade
<select name="category_id" ...>
  @foreach($categories as $cat)
    <option value="{{ $cat->id }}" {{ $item && $item->category_id == $cat->id ? 'selected' : '' }}>
      {{ $cat->name }}
    </option>
  @endforeach
</select>
```

Update price input:
```blade
<input ... name="base_price" value="{{ old('base_price', $item?->base_price) }}">
```

Update description textarea:
```blade
<textarea name="description" ...>{{ old('description', $item?->description) }}</textarea>
```

Update allergy checkboxes to use real allergens from DB:
```blade
{{-- Replace hardcoded allergy checkboxes with: --}}
@foreach($allergens as $allergen)
<label class="flex items-center gap-3 cursor-pointer group">
  <input type="checkbox" name="allergens[]" value="{{ $allergen->id }}"
         {{ $item && $item->allergens->contains($allergen->id) ? 'checked' : '' }}
         class="w-5 h-5 border-2 border-industrial-gray text-oxblood-red focus:ring-oxblood-red rounded-sm"/>
  <span class="font-body-md group-hover:text-oxblood-red transition-colors">{{ $allergen->name }}</span>
</label>
@endforeach
```

Update the visible-on-menu toggle:
```blade
<input type="checkbox" name="is_available" {{ $item?->is_available ? 'checked' : '' }} class="sr-only peer"/>
```

Update the featured toggle:
```blade
<input type="checkbox" name="is_featured" {{ $item?->is_featured ? 'checked' : '' }} class="sr-only peer"/>
```

For base ingredients (pizza), add a dynamic section using Alpine.js below the allergy checkboxes:
```blade
{{-- Base Ingredients (pizza only) --}}
<section class="industrial-border p-8 bg-surface-container-lowest" x-data="{
  ingredients: {{ $item ? $item->baseIngredients->pluck('name')->toJson() : '[]' }}
}">
  <h3 class="font-headline-md text-headline-md mb-6 flex items-center gap-3">
    <span class="material-symbols-outlined">restaurant</span> Base Ingredients
  </h3>
  <p class="font-sans text-xs text-on-surface-variant mb-4">Enter base ingredients (pizza only). Customers can uncheck these to remove them.</p>
  <div class="space-y-3">
    <template x-for="(ing, i) in ingredients" :key="i">
      <div class="flex items-center gap-3">
        <input :name="'ingredients[' + i + ']'" :value="ing"
               @input="ingredients[i] = $event.target.value"
               class="flex-1 industrial-border-b font-sans text-sm py-2 focus:outline-none"
               placeholder="e.g. Tomato Sauce"/>
        <button type="button" @click="ingredients.splice(i, 1)"
                class="text-brand-error hover:opacity-70 transition-opacity">
          <span class="material-symbols-outlined text-sm">remove_circle</span>
        </button>
      </div>
    </template>
  </div>
  <button type="button" @click="ingredients.push('')"
          class="mt-4 text-primary font-mono text-xs font-bold flex items-center gap-1 uppercase hover:underline">
    <span class="material-symbols-outlined text-sm">add</span> Add Ingredient
  </button>
</section>
```

- [ ] **Step 3: Build and verify**

```bash
npm run build
```

Log in as admin, go to `http://localhost:8000/admin/menu`. Verify:
- Real menu items show (65 items across 8 categories)
- Edit a pizza → form pre-filled with name, category, price, allergens
- Toggle availability toggle updates the UI
- Delete an item → confirmation dialog, then redirect with success message

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/menu/
git commit -m "feat: wire admin menu views to database CRUD"
```

---

## Task 9: Admin MenuItemController Tests + Full Suite

**Files:**
- Create: `tests/Feature/Admin/MenuItemTest.php`

- [ ] **Step 1: Create test directory and test file**

Create `tests/Feature/Admin/MenuItemTest.php`:

```php
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
        $this->assertDatabaseHas('menu_items', ['name' => 'New Test Pizza', 'slug' => 'new-test-pizza']);
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
            'name'        => 'Updated Name',
            'category_id' => $item->category_id,
            'base_price'  => '15.00',
            'is_available'=> '1',
        ]);

        $response->assertRedirect('/admin/menu');
        $this->assertDatabaseHas('menu_items', ['id' => $item->id, 'name' => 'Updated Name', 'base_price' => 15.00]);
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
        $category  = Category::factory()->create();
        $allergen  = Allergen::factory()->create(['name' => 'Gluten']);

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
```

- [ ] **Step 2: Run the full test suite**

```bash
& "C:\xampp\php\php.exe" artisan test
```

Expected: all tests pass (original 29 + new model tests + admin CRUD tests = ~45+ total).

If any test fails, read the error and fix the specific issue before continuing.

- [ ] **Step 3: Final production build**

```bash
npm run build
```

Expected: `✓ built in Xms` — no errors.

- [ ] **Step 4: Final commit**

```bash
git add tests/Feature/Admin/MenuItemTest.php
git commit -m "test: add admin menu CRUD feature tests"

git add .
git commit -m "feat: Plan 4 complete — full database schema, models, real menu data, CRUD wired"
```
