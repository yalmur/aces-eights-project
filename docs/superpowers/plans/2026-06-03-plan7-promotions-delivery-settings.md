# Aces & Eights Pizza — Plan 7: Promotions, Delivery Zones & Settings

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Wire three remaining admin subsystems — promo code discounts that customers can apply at checkout, a live delivery zones manager so admin can set fees, and a persistent settings store so the owner can update their store info without a code deploy.

**Architecture:** Three independent features sharing the same pattern: new migration → Eloquent model → admin CRUD → customer-facing integration. Promotions get a `promotions` table and are validated/applied in `CheckoutController`. Delivery zones already have a table (`delivery_zones`) and just need admin CRUD plus `CheckoutController` reading the fee from DB instead of hardcoding £3.50. Settings get a `settings` key-value table with a static `Setting::get/set` helper used in footer and admin settings form.

**Tech Stack:** Laravel 11, Eloquent ORM, Alpine.js 3, MySQL 8, PHP 8.2 at `C:\xampp\php\php.exe`

---

## Schema additions

```
promotions          id, code(unique), name, type(enum), value, min_order_amount(null),
                    max_uses(null), current_uses default 0, is_active bool, expires_at(null)

settings            id, key(unique), value(text null)

orders (add cols)   promo_code(null), discount_amount decimal 5,2 default 0
```

`delivery_zones` table already exists from Plan 4.

---

## File Map

| Action | File |
|--------|------|
| Create | `database/migrations/..._create_promotions_table.php` |
| Create | `database/migrations/..._add_promo_cols_to_orders_table.php` |
| Create | `database/migrations/..._create_settings_table.php` |
| Create | `app/Models/Promotion.php` |
| Create | `app/Models/Setting.php` |
| Create | `database/factories/PromotionFactory.php` |
| Create | `database/seeders/SettingSeeder.php` |
| Modify | `database/seeders/DatabaseSeeder.php` |
| Modify | `app/Http/Controllers/Admin/PromotionController.php` |
| Modify | `app/Http/Controllers/Admin/DeliveryController.php` |
| Modify | `app/Http/Controllers/Admin/SettingsController.php` |
| Modify | `app/Http/Controllers/CheckoutController.php` |
| Modify | `app/Models/Order.php` |
| Create | `resources/views/admin/promotions/index.blade.php` |
| Create | `resources/views/admin/promotions/edit.blade.php` |
| Modify | `resources/views/admin/delivery/index.blade.php` |
| Modify | `resources/views/admin/settings/index.blade.php` |
| Modify | `resources/views/checkout/index.blade.php` |
| Modify | `resources/views/layouts/app.blade.php` |
| Add    | `routes/web.php` — promo CRUD + delivery CRUD + settings POST |
| Create | `tests/Feature/PromotionTest.php` |
| Create | `tests/Feature/DeliveryZoneTest.php` |
| Create | `tests/Feature/SettingsTest.php` |

---

## Task 1: Migrations

**Files:** 3 migration files

- [ ] **Step 1: Create stubs**

```bash
cd C:\AcesAndEightsPizza\webapp
& "C:\xampp\php\php.exe" artisan make:migration create_promotions_table
& "C:\xampp\php\php.exe" artisan make:migration add_promo_cols_to_orders_table --table=orders
& "C:\xampp\php\php.exe" artisan make:migration create_settings_table
```

- [ ] **Step 2: Write promotions migration**

Replace the newest `..._create_promotions_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['percentage', 'fixed_amount', 'free_delivery']);
            $table->decimal('value', 8, 2)->default(0);
            $table->decimal('min_order_amount', 8, 2)->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('current_uses')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
```

- [ ] **Step 3: Write orders promo columns migration**

Replace the newest `..._add_promo_cols_to_orders_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('promo_code')->nullable()->after('notes');
            $table->decimal('discount_amount', 5, 2)->default(0)->after('promo_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['promo_code', 'discount_amount']);
        });
    }
};
```

- [ ] **Step 4: Write settings migration**

Replace the newest `..._create_settings_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
```

- [ ] **Step 5: Run migrations**

```bash
& "C:\xampp\php\php.exe" artisan migrate
```

Expected: 3 new tables created (promotions, settings) + 2 new columns on orders.

- [ ] **Step 6: Commit**

```bash
git add database/migrations/
git commit -m "feat: add promotions, settings migrations + promo cols on orders"
```

---

## Task 2: Models + Factory + Seeder

**Files:** Promotion.php, Setting.php, PromotionFactory.php, SettingSeeder.php, Order.php update, DatabaseSeeder.php update

- [ ] **Step 1: Create Promotion model**

Create `app/Models/Promotion.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_order_amount',
        'max_uses', 'current_uses', 'is_active', 'expires_at',
    ];

    protected $casts = [
        'value'            => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active'        => 'boolean',
        'expires_at'       => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(fn ($q) => $q->whereNull('max_uses')->orWhereColumn('current_uses', '<', 'max_uses'));
    }

    public function isValid(float $subtotal): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->current_uses >= $this->max_uses) return false;
        if ($this->min_order_amount && $subtotal < $this->min_order_amount) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal, float $deliveryFee): float
    {
        return match($this->type) {
            'percentage'   => round($subtotal * ($this->value / 100), 2),
            'fixed_amount' => min($this->value, $subtotal),
            'free_delivery'=> $deliveryFee,
            default        => 0,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'percentage'    => "{$this->value}% off",
            'fixed_amount'  => "£{$this->value} off",
            'free_delivery' => 'Free delivery',
            default         => $this->type,
        };
    }

    public function incrementUses(): void
    {
        $this->increment('current_uses');
    }
}
```

- [ ] **Step 2: Create Setting model**

Create `app/Models/Setting.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($key, $value);
        }
    }
}
```

- [ ] **Step 3: Update Order model — add promo fields to fillable + casts**

Open `app/Models/Order.php`. Add `'promo_code'` and `'discount_amount'` to `$fillable`:

```php
protected $fillable = [
    'user_id', 'type', 'status', 'subtotal', 'delivery_fee', 'total',
    'customer_name', 'customer_email', 'customer_phone',
    'delivery_address', 'delivery_city', 'delivery_postcode',
    'stripe_session_id', 'stripe_payment_intent_id', 'notes',
    'promo_code', 'discount_amount',
];
```

Add `'discount_amount' => 'decimal:2'` to `$casts`.

- [ ] **Step 4: Create PromotionFactory**

Create `database/factories/PromotionFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'             => strtoupper($this->faker->unique()->lexify('????')),
            'name'             => $this->faker->words(3, true),
            'type'             => $this->faker->randomElement(['percentage', 'fixed_amount', 'free_delivery']),
            'value'            => $this->faker->randomFloat(2, 5, 20),
            'min_order_amount' => null,
            'max_uses'         => null,
            'current_uses'     => 0,
            'is_active'        => true,
            'expires_at'       => null,
        ];
    }

    public function percentage(float $pct = 10): static
    {
        return $this->state(['type' => 'percentage', 'value' => $pct]);
    }

    public function fixed(float $amount = 5): static
    {
        return $this->state(['type' => 'fixed_amount', 'value' => $amount]);
    }

    public function freeDelivery(): static
    {
        return $this->state(['type' => 'free_delivery', 'value' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }
}
```

- [ ] **Step 5: Create SettingSeeder**

Create `database/seeders/SettingSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'store_name'       => 'Aces & Eights Pizza',
            'store_address'    => '156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP',
            'store_phone'      => '+44 020 7485 4033',
            'store_email'      => 'nw5pizza@gmail.com',
            'opening_sun_thu'  => '16:00 – 22:45',
            'opening_fri_sat'  => '16:00 – 23:15',
            'hero_text'        => 'FORGED IN THE FIRE OF TRADITION. SERVED WITH INDUSTRIAL PRECISION. ACES & EIGHTS PIZZA — ESTABLISHED 2012.',
            'story_text'       => 'Born from the hum of machinery and the heat of the forge, Aces & Eights was founded on the premise that the best food is made by hand, with tools that have stood the test of time.',
            'maintenance_mode' => '0',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
```

- [ ] **Step 6: Add SettingSeeder to DatabaseSeeder**

Open `database/seeders/DatabaseSeeder.php`. Add `SettingSeeder::class` to the call array (after DeliveryZoneSeeder):

```php
$this->call([
    AdminUserSeeder::class,
    CategorySeeder::class,
    AllergenSeeder::class,
    ToppingSeeder::class,
    DeliveryZoneSeeder::class,
    MenuItemSeeder::class,
    SettingSeeder::class,
]);
```

Run: `& "C:\xampp\php\php.exe" artisan db:seed --class=SettingSeeder`

Expected: 9 settings rows created in `settings` table.

- [ ] **Step 7: Commit**

```bash
git add app/Models/Promotion.php app/Models/Setting.php app/Models/Order.php database/factories/PromotionFactory.php database/seeders/SettingSeeder.php database/seeders/DatabaseSeeder.php
git commit -m "feat: Promotion and Setting models, factory, seeder"
```

---

## Task 3: Promotions — Feature Tests + Admin CRUD

**Files:**
- Create: `tests/Feature/PromotionTest.php`
- Modify: `app/Http/Controllers/Admin/PromotionController.php`
- Create: `resources/views/admin/promotions/index.blade.php`
- Create: `resources/views/admin/promotions/edit.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Write failing promotion tests**

Create `tests/Feature/PromotionTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_promotions_page_returns_200(): void
    {
        $this->actingAs($this->admin)->get('/admin/promotions')->assertStatus(200);
    }

    public function test_admin_can_create_promotion(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/promotions', [
            'code'      => 'SAVE10',
            'name'      => '10% off everything',
            'type'      => 'percentage',
            'value'     => '10',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/promotions');
        $this->assertDatabaseHas('promotions', ['code' => 'SAVE10', 'type' => 'percentage']);
    }

    public function test_promo_code_is_forced_uppercase(): void
    {
        $this->actingAs($this->admin)->post('/admin/promotions', [
            'code' => 'lowercase', 'name' => 'Test', 'type' => 'fixed_amount', 'value' => '5',
        ]);
        $this->assertDatabaseHas('promotions', ['code' => 'LOWERCASE']);
    }

    public function test_admin_can_update_promotion(): void
    {
        $promo = Promotion::factory()->create(['name' => 'Old Name']);
        $this->actingAs($this->admin)->put("/admin/promotions/{$promo->id}", [
            'code' => $promo->code, 'name' => 'New Name', 'type' => $promo->type, 'value' => $promo->value,
        ]);
        $this->assertDatabaseHas('promotions', ['id' => $promo->id, 'name' => 'New Name']);
    }

    public function test_admin_can_delete_promotion(): void
    {
        $promo = Promotion::factory()->create();
        $this->actingAs($this->admin)->delete("/admin/promotions/{$promo->id}");
        $this->assertDatabaseMissing('promotions', ['id' => $promo->id]);
    }

    public function test_promotion_is_valid_when_conditions_met(): void
    {
        $promo = Promotion::factory()->percentage(10)->create(['min_order_amount' => 20]);
        $this->assertTrue($promo->isValid(25.00));
        $this->assertFalse($promo->isValid(15.00));
    }

    public function test_promotion_calculates_correct_discount(): void
    {
        $pct   = Promotion::factory()->percentage(10)->create();
        $fixed = Promotion::factory()->fixed(5)->create();
        $free  = Promotion::factory()->freeDelivery()->create();

        $this->assertEquals(2.50, $pct->calculateDiscount(25.00, 3.50));
        $this->assertEquals(5.00, $fixed->calculateDiscount(25.00, 3.50));
        $this->assertEquals(3.50, $free->calculateDiscount(25.00, 3.50));
    }

    public function test_expired_promotion_is_not_valid(): void
    {
        $promo = Promotion::factory()->expired()->create();
        $this->assertFalse($promo->isValid(50.00));
    }
}
```

Run: `& "C:\xampp\php\php.exe" artisan test tests/Feature/PromotionTest.php`
Expected: failures (no routes/controller yet).

- [ ] **Step 2: Add promotion routes**

Open `routes/web.php`. Inside the admin middleware group, find the promotions section:
```php
    // Promotions
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
```

Replace with full resource routes:
```php
    // Promotions
    Route::get('/promotions',              [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create',       [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions',             [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promo}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promo}',      [PromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promo}',   [PromotionController::class, 'destroy'])->name('promotions.destroy');
```

- [ ] **Step 3: Write PromotionController**

Replace full contents of `app/Http/Controllers/Admin/PromotionController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::latest()->paginate(20);
        return view('admin.promotions.index', ['title' => 'Promotions', 'promotions' => $promotions]);
    }

    public function create(): View
    {
        return view('admin.promotions.edit', ['title' => 'Add Promotion', 'promo' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Promotion::create($data);
        return redirect()->route('admin.promotions.index')->with('success', "Promotion '{$data['code']}' created.");
    }

    public function edit(string $promo): View
    {
        $promotion = Promotion::findOrFail($promo);
        return view('admin.promotions.edit', ['title' => 'Edit Promotion', 'promo' => $promotion]);
    }

    public function update(Request $request, string $promo): RedirectResponse
    {
        $promotion = Promotion::findOrFail($promo);
        $data = $this->validated($request, $promotion->id);
        $promotion->update($data);
        return redirect()->route('admin.promotions.index')->with('success', "Promotion '{$promotion->code}' updated.");
    }

    public function destroy(string $promo): RedirectResponse
    {
        $promotion = Promotion::findOrFail($promo);
        $code = $promotion->code;
        $promotion->delete();
        return redirect()->route('admin.promotions.index')->with('success', "Promotion '{$code}' deleted.");
    }

    private function validated(Request $request, ?int $excludeId = null): array
    {
        $data = $request->validate([
            'code'             => 'required|string|max:50|unique:promotions,code' . ($excludeId ? ",{$excludeId}" : ''),
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:percentage,fixed_amount,free_delivery',
            'value'            => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses'         => 'nullable|integer|min:1',
            'is_active'        => 'boolean',
            'expires_at'       => 'nullable|date|after:today',
        ]);

        $data['code']      = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
```

- [ ] **Step 4: Create promotions index view**

Create `resources/views/admin/promotions/index.blade.php`:

```blade
@extends('layouts.admin')
@section('content')

@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">{{ session('success') }}</div>
@endif

<div class="flex justify-between items-end mb-8">
  <div>
    <h1 class="font-serif text-4xl font-black text-on-surface uppercase">Promotions</h1>
    <p class="font-sans text-sm text-on-surface-variant mt-1">Manage discount codes and special offers.</p>
  </div>
  <a href="{{ route('admin.promotions.create') }}" class="gold-button px-6 py-3 font-mono text-xs font-bold uppercase flex items-center gap-2">
    <span class="material-symbols-outlined">add</span> ADD PROMOTION
  </a>
</div>
<div class="double-divider mb-8"></div>

<div class="industrial-border overflow-hidden bg-white">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[600px]">
      <thead>
        <tr class="bg-surface-container-high border-b border-[#2B2B2B]">
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Code</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Name</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Discount</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Uses</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase text-center">Active</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase">Expires</th>
          <th class="px-6 py-4 font-mono text-[10px] font-bold uppercase text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#2B2B2B]/10">
        @forelse($promotions as $promo)
        <tr class="{{ !$promo->is_active ? 'opacity-60' : '' }} hover:bg-surface-container-low transition-colors">
          <td class="px-6 py-4 font-mono text-sm font-bold">{{ $promo->code }}</td>
          <td class="px-6 py-4 font-sans text-sm">{{ $promo->name }}</td>
          <td class="px-6 py-4 font-mono text-sm font-bold text-primary">{{ $promo->type_label }}</td>
          <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">
            {{ $promo->current_uses }}{{ $promo->max_uses ? ' / ' . $promo->max_uses : '' }}
          </td>
          <td class="px-6 py-4 text-center">
            <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded-full {{ $promo->is_active ? 'bg-green-100 text-green-800' : 'bg-surface-container-high text-on-surface-variant' }}">
              {{ $promo->is_active ? 'ACTIVE' : 'OFF' }}
            </span>
          </td>
          <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">
            {{ $promo->expires_at?->format('d M Y') ?? '—' }}
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex justify-end gap-2">
              <a href="{{ route('admin.promotions.edit', $promo->id) }}" class="p-2 hover:bg-surface-container rounded" title="Edit">
                <span class="material-symbols-outlined text-on-surface-variant">edit</span>
              </a>
              <form method="POST" action="{{ route('admin.promotions.destroy', $promo->id) }}"
                    onsubmit="return confirm('Delete {{ addslashes($promo->code) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 hover:bg-brand-error/10 rounded" title="Delete">
                  <span class="material-symbols-outlined text-brand-error">delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-6 py-12 text-center font-sans text-sm text-on-surface-variant">No promotions yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-6">{{ $promotions->links() }}</div>

@endsection
```

- [ ] **Step 5: Create promotions edit view**

Create `resources/views/admin/promotions/edit.blade.php`:

```blade
@extends('layouts.admin')
@section('content')

<div class="flex items-center gap-2 text-on-surface-variant mb-4 font-mono text-xs">
  <a href="{{ route('admin.promotions.index') }}" class="hover:text-primary transition-colors">Promotions</a>
  <span class="material-symbols-outlined text-sm">chevron_right</span>
  <span>{{ $promo ? $promo->code : 'New' }}</span>
</div>

<h1 class="font-serif text-3xl font-black text-on-surface uppercase mb-8">{{ $title }}</h1>

<form method="POST"
      action="{{ $promo ? route('admin.promotions.update', $promo->id) : route('admin.promotions.store') }}"
      class="max-w-2xl">
  @csrf
  @if($promo) @method('PUT') @endif

  @if($errors->any())
    <div class="mb-6 p-4 bg-brand-error/10 border border-brand-error font-mono text-xs text-brand-error">
      @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
    </div>
  @endif

  <div class="space-y-8">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Promo Code *</label>
        <input name="code" value="{{ old('code', $promo?->code) }}" required
               class="w-full industrial-border-b font-mono text-sm py-2 uppercase focus:outline-none focus:border-primary"
               placeholder="e.g. SAVE10">
      </div>
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Name *</label>
        <input name="name" value="{{ old('name', $promo?->name) }}" required
               class="w-full industrial-border-b font-sans text-sm py-2 focus:outline-none focus:border-primary"
               placeholder="e.g. 10% off everything">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Discount Type *</label>
        <select name="type" required class="w-full industrial-border-b font-sans text-sm py-2 focus:outline-none focus:border-primary">
          @foreach(['percentage' => 'Percentage (%)', 'fixed_amount' => 'Fixed Amount (£)', 'free_delivery' => 'Free Delivery'] as $val => $label)
            <option value="{{ $val }}" {{ old('type', $promo?->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Value (% or £)</label>
        <input name="value" type="number" step="0.01" min="0"
               value="{{ old('value', $promo?->value) }}"
               class="w-full industrial-border-b font-mono text-sm py-2 focus:outline-none focus:border-primary"
               placeholder="e.g. 10 or 5.00">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Min. Order Amount (£)</label>
        <input name="min_order_amount" type="number" step="0.01" min="0"
               value="{{ old('min_order_amount', $promo?->min_order_amount) }}"
               class="w-full industrial-border-b font-mono text-sm py-2 focus:outline-none focus:border-primary"
               placeholder="Leave blank for no minimum">
      </div>
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Max Uses</label>
        <input name="max_uses" type="number" min="1"
               value="{{ old('max_uses', $promo?->max_uses) }}"
               class="w-full industrial-border-b font-mono text-sm py-2 focus:outline-none focus:border-primary"
               placeholder="Leave blank for unlimited">
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Expires At</label>
        <input name="expires_at" type="date"
               value="{{ old('expires_at', $promo?->expires_at?->format('Y-m-d')) }}"
               class="w-full industrial-border-b font-mono text-sm py-2 focus:outline-none focus:border-primary">
      </div>
      <div class="flex items-center gap-3 mt-6">
        <label class="relative inline-flex items-center cursor-pointer">
          <input name="is_active" type="checkbox" value="1" {{ old('is_active', $promo?->is_active ?? true) ? 'checked' : '' }} class="sr-only peer"/>
          <div class="w-11 h-6 bg-surface-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
        </label>
        <span class="font-mono text-xs uppercase text-on-surface-variant">Active</span>
      </div>
    </div>

    <div class="flex gap-4 pt-4">
      <button type="submit" class="gold-button px-8 py-3 font-mono text-xs uppercase">
        {{ $promo ? 'UPDATE PROMOTION' : 'CREATE PROMOTION' }}
      </button>
      <a href="{{ route('admin.promotions.index') }}" class="btn-ghost">Cancel</a>
    </div>
  </div>
</form>

@endsection
```

- [ ] **Step 6: Run tests — expect pass**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/PromotionTest.php
```

Expected: 8 passed.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/Admin/PromotionController.php resources/views/admin/promotions/ routes/web.php tests/Feature/PromotionTest.php
git commit -m "feat: admin promotions CRUD with full test coverage"
```

---

## Task 4: Promo Code at Checkout

**Files:**
- Modify: `app/Http/Controllers/CheckoutController.php`
- Modify: `resources/views/checkout/index.blade.php`

- [ ] **Step 1: Update CheckoutController to apply promo codes**

Open `app/Http/Controllers/CheckoutController.php`. Add import:
```php
use App\Models\Promotion;
```

In `store()`, update the validation to include `promo_code`, and add discount calculation after delivery fee is set. Replace from `$cartItems = json_decode(...)` through to `$order = Order::create([...])`:

```php
$cartItems   = json_decode($data['cart_items'], true);
$isDelivery  = $data['order_type'] === 'delivery';
$deliveryFee = $isDelivery
    ? (\App\Models\DeliveryZone::active()->orderBy('sort_order')->value('fee') ?? 3.50)
    : 0;
$user = Auth::user();

$orderItems = [];
$subtotal   = 0;

foreach ($cartItems as $ci) {
    $lineTotal   = (float) ($ci['lineTotal'] ?? 0);
    $subtotal   += $lineTotal;
    $orderItems[] = [
        'name'                => $ci['name'],
        'qty'                 => max(1, (int) ($ci['qty'] ?? 1)),
        'unit_price'          => (float) ($ci['basePrice'] ?? 0),
        'size'                => $ci['size'] ?? null,
        'crust'               => $ci['crust'] ?? null,
        'size_extra'          => (float) ($ci['sizeExtra'] ?? 0),
        'crust_extra'         => (float) ($ci['crustExtra'] ?? 0),
        'added_toppings'      => $ci['toppings'] ?? [],
        'removed_ingredients' => $ci['removedIngredients'] ?? [],
        'instructions'        => $ci['instructions'] ?? null,
        'line_total'          => $lineTotal,
    ];
}

// Apply promo code
$discountAmount = 0;
$promoCode      = null;
$promoModel     = null;

if (!empty($data['promo_code'])) {
    $promoModel = Promotion::where('code', strtoupper(trim($data['promo_code'])))->first();
    if ($promoModel && $promoModel->isValid($subtotal)) {
        $discountAmount = $promoModel->calculateDiscount($subtotal, $deliveryFee);
        $promoCode      = $promoModel->code;
    }
}

$total = max(0, $subtotal + $deliveryFee - $discountAmount);

$order = Order::create([
    'user_id'           => $user->id,
    'type'              => $data['order_type'],
    'status'            => 'pending_payment',
    'subtotal'          => $subtotal,
    'delivery_fee'      => $deliveryFee,
    'discount_amount'   => $discountAmount,
    'promo_code'        => $promoCode,
    'total'             => $total,
    'customer_name'     => $user->name,
    'customer_email'    => $user->email,
    'delivery_address'  => $data['street_address'] ?? null,
    'delivery_city'     => $data['city'] ?? null,
    'delivery_postcode' => $data['postal_code'] ?? null,
]);
```

Also add `'promo_code'` to the validation rules:
```php
$data = $request->validate([
    'order_type'     => 'required|in:delivery,collection',
    'cart_items'     => ['required', 'json', function ($attr, $val, $fail) {
        $items = json_decode($val, true);
        if (empty($items)) $fail('Your cart is empty.');
    }],
    'street_address' => 'required_if:order_type,delivery|nullable|string|max:255',
    'city'           => 'required_if:order_type,delivery|nullable|string|max:100',
    'postal_code'    => 'required_if:order_type,delivery|nullable|string|max:20',
    'promo_code'     => 'nullable|string|max:50',
]);
```

After the order items are created, if there was a valid promo, increment its uses:
```php
foreach ($orderItems as $item) {
    $order->items()->create($item);
}

if ($promoModel) {
    $promoModel->incrementUses();
}
```

- [ ] **Step 2: Add promo code field to checkout view**

Open `resources/views/checkout/index.blade.php`. Find the Payment Method section. Add a promo code input field before the payment method section:

```blade
{{-- Promo Code --}}
<section class="mt-8" x-data="{ applied: false, error: '' }">
  <h3 class="font-label-bold text-label-bold uppercase mb-4 text-primary">Promo Code</h3>
  <div class="flex gap-3">
    <input name="promo_code" id="promo-code"
           class="flex-1 bg-transparent border-b-2 border-on-surface py-2 focus:ring-0 focus:border-primary font-mono text-sm uppercase placeholder:text-outline-variant placeholder:normal-case"
           placeholder="Enter code (e.g. SAVE10)"
           type="text"
           maxlength="50"/>
    <button type="button"
            @click="
              const code = document.getElementById('promo-code').value.trim();
              if (code) { applied = true; error = '' }
              else { error = 'Enter a code first' }
            "
            class="px-4 py-2 industrial-border font-mono text-xs font-bold uppercase hover:bg-surface-container transition-colors">
      APPLY
    </button>
  </div>
  <p x-show="applied" x-cloak class="font-mono text-[10px] text-green-700 mt-2">Code applied — discount calculated at checkout.</p>
  <p x-show="error" x-cloak class="font-mono text-[10px] text-brand-error mt-2" x-text="error"></p>
</section>
```

- [ ] **Step 3: Update CheckoutTest to cover promo code**

Open `tests/Feature/CheckoutTest.php`. Add this test:

```php
public function test_valid_promo_code_applies_discount(): void
{
    $promo = \App\Models\Promotion::factory()->fixed(5)->create(['code' => 'FIVE']);
    $cartItems = [[
        'id' => 'margherita', 'name' => 'Margherita', 'category' => 'pizza',
        'basePrice' => 20.00, 'qty' => 1, 'size' => '12" Standard', 'sizeExtra' => 0,
        'crust' => '48hr Sourdough', 'crustExtra' => 0, 'toppings' => [],
        'removedIngredients' => [], 'chips' => [], 'instructions' => '',
        'lineTotal' => 20.00,
    ]];

    $this->actingAs($this->customer)->post('/checkout', [
        'order_type'     => 'collection',
        'cart_items'     => json_encode($cartItems),
        'promo_code'     => 'FIVE',
    ]);

    $this->assertDatabaseHas('orders', [
        'promo_code'      => 'FIVE',
        'discount_amount' => 5.00,
        'total'           => 15.00,
    ]);
}
```

- [ ] **Step 4: Show discount on order confirmation view**

Open `resources/views/orders/confirmation.blade.php`. Find the totals section (subtotal/delivery/total rows). Add a discount row between subtotal and delivery:

```blade
@if($order->discount_amount > 0)
<div class="flex justify-between font-sans text-sm">
  <span class="text-green-700">Promo ({{ $order->promo_code }})</span>
  <span class="text-green-700">− £{{ number_format($order->discount_amount, 2) }}</span>
</div>
@endif
```

- [ ] **Step 5: Run checkout tests**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/CheckoutTest.php
```

Expected: all pass (including the new promo test).

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/CheckoutController.php resources/views/checkout/index.blade.php tests/Feature/CheckoutTest.php
git commit -m "feat: promo code discount at checkout, delivery fee from DB"
```

---

## Task 5: Delivery Zones Admin CRUD

**Files:**
- Modify: `app/Http/Controllers/Admin/DeliveryController.php`
- Modify: `resources/views/admin/delivery/index.blade.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/DeliveryZoneTest.php`

- [ ] **Step 1: Write delivery zone tests**

Create `tests/Feature/DeliveryZoneTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryZoneTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_delivery_page_returns_200(): void
    {
        $this->actingAs($this->admin)->get('/admin/delivery')->assertStatus(200);
    }

    public function test_admin_can_create_delivery_zone(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/delivery', [
            'name'      => 'Zone 1',
            'min_km'    => '0',
            'max_km'    => '2',
            'fee'       => '2.50',
            'is_active' => '1',
        ]);
        $response->assertRedirect('/admin/delivery');
        $this->assertDatabaseHas('delivery_zones', ['name' => 'Zone 1', 'fee' => 2.50]);
    }

    public function test_admin_can_update_delivery_zone(): void
    {
        $zone = DeliveryZone::factory()->create(['fee' => 3.00]);
        $this->actingAs($this->admin)->put("/admin/delivery/{$zone->id}", [
            'name' => $zone->name, 'min_km' => $zone->min_km, 'max_km' => $zone->max_km,
            'fee' => '4.50', 'is_active' => '1',
        ]);
        $this->assertDatabaseHas('delivery_zones', ['id' => $zone->id, 'fee' => 4.50]);
    }

    public function test_admin_can_delete_delivery_zone(): void
    {
        $zone = DeliveryZone::factory()->create();
        $this->actingAs($this->admin)->delete("/admin/delivery/{$zone->id}");
        $this->assertDatabaseMissing('delivery_zones', ['id' => $zone->id]);
    }
}
```

- [ ] **Step 2: Add DeliveryZone factory**

Create `database/factories/DeliveryZoneFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryZoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->words(3, true),
            'min_km'     => 0.00,
            'max_km'     => $this->faker->randomFloat(2, 1, 5),
            'fee'        => $this->faker->randomFloat(2, 2, 6),
            'is_active'  => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
```

- [ ] **Step 3: Add delivery routes**

Open `routes/web.php`. Find:
```php
    // Delivery
    Route::get('/delivery', [DeliveryController::class, 'index'])->name('delivery.index');
```

Replace with:
```php
    // Delivery Zones
    Route::get('/delivery',              [DeliveryController::class, 'index'])->name('delivery.index');
    Route::post('/delivery',             [DeliveryController::class, 'store'])->name('delivery.store');
    Route::put('/delivery/{zone}',       [DeliveryController::class, 'update'])->name('delivery.update');
    Route::delete('/delivery/{zone}',    [DeliveryController::class, 'destroy'])->name('delivery.destroy');
```

- [ ] **Step 4: Rewrite DeliveryController**

Replace full contents of `app/Http/Controllers/Admin/DeliveryController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(): View
    {
        $zones = DeliveryZone::orderBy('sort_order')->get();
        return view('admin.delivery.index', ['title' => 'Delivery Zones', 'zones' => $zones]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        DeliveryZone::create($data);
        return redirect()->route('admin.delivery.index')->with('success', 'Delivery zone added.');
    }

    public function update(Request $request, string $zone): RedirectResponse
    {
        $zoneModel = DeliveryZone::findOrFail($zone);
        $zoneModel->update($this->validated($request));
        return redirect()->route('admin.delivery.index')->with('success', "Zone '{$zoneModel->name}' updated.");
    }

    public function destroy(string $zone): RedirectResponse
    {
        DeliveryZone::findOrFail($zone)->delete();
        return redirect()->route('admin.delivery.index')->with('success', 'Zone deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'min_km'     => 'required|numeric|min:0',
            'max_km'     => 'required|numeric|min:0',
            'fee'        => 'required|numeric|min:0',
            'is_active'  => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
```

- [ ] **Step 5: Update delivery zones view**

Open `resources/views/admin/delivery/index.blade.php`. Add `$zones` data to the view. Find the "Zonal Fees" sidebar section and replace it with a live DB-driven section. Find the right column (`<!-- Right Column: Settings & Fees -->`) and replace the Fee Structure Card content with:

```blade
{{-- Fee Structure Card — DB driven --}}
@if(session('success'))
  <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">{{ session('success') }}</div>
@endif

<div class="bg-surface border-2 border-outline rounded-sm mb-4">
  <div class="bg-surface-container px-4 py-3 border-b-2 border-outline flex justify-between items-center">
    <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
      <span class="material-symbols-outlined">payments</span> Delivery Zones
    </h3>
  </div>
  <div class="p-4 flex flex-col gap-4">
    @foreach($zones as $zone)
    <form method="POST" action="{{ route('admin.delivery.update', $zone->id) }}" class="flex items-center gap-3">
      @csrf @method('PUT')
      <input type="hidden" name="name" value="{{ $zone->name }}">
      <input type="hidden" name="min_km" value="{{ $zone->min_km }}">
      <input type="hidden" name="max_km" value="{{ $zone->max_km }}">
      <input type="hidden" name="is_active" value="{{ $zone->is_active ? '1' : '0' }}">
      <div class="flex-1">
        <label class="font-mono text-[10px] font-bold text-on-surface uppercase">{{ $zone->name }}</label>
      </div>
      <div class="flex items-center gap-1">
        <span class="font-mono text-xs text-on-surface-variant">£</span>
        <input type="number" name="fee" step="0.01" min="0" value="{{ $zone->fee }}"
               class="w-16 bg-background border-0 border-b-2 border-outline focus:ring-0 focus:border-primary font-mono text-sm text-center py-1 px-0"/>
      </div>
      <button type="submit" class="font-mono text-[10px] text-primary hover:underline uppercase">Save</button>
      <form method="POST" action="{{ route('admin.delivery.destroy', $zone->id) }}"
            onsubmit="return confirm('Delete {{ addslashes($zone->name) }}?')" style="display:inline">
        @csrf @method('DELETE')
        <button type="submit" class="font-mono text-[10px] text-brand-error hover:underline uppercase">Del</button>
      </form>
    </form>
    @endforeach

    {{-- Add new zone --}}
    <form method="POST" action="{{ route('admin.delivery.store') }}" class="border-t border-outline pt-4 mt-2 flex flex-col gap-2">
      @csrf
      <p class="font-mono text-[10px] uppercase text-on-surface-variant font-bold">Add Zone</p>
      <input type="text" name="name" placeholder="Zone name" required
             class="w-full border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0 focus:border-primary">
      <div class="grid grid-cols-3 gap-2">
        <input type="number" name="min_km" placeholder="From km" step="0.1" min="0" required
               class="border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0">
        <input type="number" name="max_km" placeholder="To km" step="0.1" min="0" required
               class="border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0">
        <input type="number" name="fee" placeholder="Fee £" step="0.01" min="0" required
               class="border-0 border-b border-outline font-mono text-xs py-1 focus:ring-0">
      </div>
      <input type="hidden" name="is_active" value="1">
      <button type="submit" class="w-full bg-primary text-on-primary py-2 font-mono text-[10px] font-bold uppercase mt-1">ADD ZONE</button>
    </form>
  </div>
</div>
```

Note: the `<form>` for delete is nested inside another form above — that's invalid HTML. Fix by using a separate delete form outside the update form. Rewrite the zone row as:

```blade
@foreach($zones as $zone)
<div class="flex items-center gap-3 border-b border-outline pb-2">
  <div class="flex-1">
    <span class="font-mono text-[10px] font-bold text-on-surface uppercase">{{ $zone->name }}</span>
    <span class="font-mono text-[9px] text-on-surface-variant block">{{ $zone->min_km }}–{{ $zone->max_km }}km</span>
  </div>
  <form method="POST" action="{{ route('admin.delivery.update', $zone->id) }}" class="flex items-center gap-2">
    @csrf @method('PUT')
    <input type="hidden" name="name" value="{{ $zone->name }}">
    <input type="hidden" name="min_km" value="{{ $zone->min_km }}">
    <input type="hidden" name="max_km" value="{{ $zone->max_km }}">
    <input type="hidden" name="is_active" value="{{ $zone->is_active ? '1' : '0' }}">
    <span class="font-mono text-[10px] text-on-surface-variant">£</span>
    <input type="number" name="fee" step="0.01" min="0" value="{{ $zone->fee }}"
           class="w-16 bg-background border-0 border-b border-outline font-mono text-xs text-center py-1"/>
    <button type="submit" class="font-mono text-[10px] text-primary hover:underline">SAVE</button>
  </form>
  <form method="POST" action="{{ route('admin.delivery.destroy', $zone->id) }}"
        onsubmit="return confirm('Delete?')">
    @csrf @method('DELETE')
    <button type="submit" class="font-mono text-[10px] text-brand-error hover:underline">DEL</button>
  </form>
</div>
@endforeach
```

- [ ] **Step 6: Run delivery zone tests**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/DeliveryZoneTest.php
```

Expected: 4 passed.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/Admin/DeliveryController.php app/Http/Controllers/CheckoutController.php resources/views/admin/delivery/index.blade.php database/factories/DeliveryZoneFactory.php routes/web.php tests/Feature/DeliveryZoneTest.php
git commit -m "feat: delivery zones admin CRUD, delivery fee from DB at checkout"
```

---

## Task 6: Settings — Persist + Use in Views

**Files:**
- Modify: `app/Http/Controllers/Admin/SettingsController.php`
- Modify: `resources/views/admin/settings/index.blade.php`
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/SettingsTest.php`

- [ ] **Step 1: Write settings tests**

Create `tests/Feature/SettingsTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_settings_page_returns_200(): void
    {
        $this->actingAs($this->admin)->get('/admin/settings')->assertStatus(200);
    }

    public function test_admin_can_save_settings(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'store_name'      => 'Test Pizza',
            'store_address'   => '1 Test Street, London',
            'store_phone'     => '+44 123 456 7890',
            'store_email'     => 'test@test.com',
            'opening_sun_thu' => '17:00 – 22:00',
            'opening_fri_sat' => '17:00 – 23:00',
        ]);

        $response->assertRedirect('/admin/settings');
        $this->assertDatabaseHas('settings', ['key' => 'store_name', 'value' => 'Test Pizza']);
        $this->assertSame('1 Test Street, London', Setting::get('store_address'));
    }

    public function test_setting_get_returns_default_when_not_set(): void
    {
        $this->assertSame('default_val', Setting::get('nonexistent_key', 'default_val'));
    }

    public function test_setting_set_persists_value(): void
    {
        Setting::set('test_key', 'test_value');
        $this->assertDatabaseHas('settings', ['key' => 'test_key', 'value' => 'test_value']);
        $this->assertSame('test_value', Setting::get('test_key'));
    }
}
```

- [ ] **Step 2: Add settings POST route**

Open `routes/web.php`. Find:
```php
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
```

Replace with:
```php
    // Settings
    Route::get('/settings',  [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
```

- [ ] **Step 3: Rewrite SettingsController**

Replace full contents of `app/Http/Controllers/Admin/SettingsController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'title'    => 'Settings',
            'settings' => [
                'store_name'      => Setting::get('store_name',      'Aces & Eights Pizza'),
                'store_address'   => Setting::get('store_address',   '156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP'),
                'store_phone'     => Setting::get('store_phone',     '+44 020 7485 4033'),
                'store_email'     => Setting::get('store_email',     'nw5pizza@gmail.com'),
                'opening_sun_thu' => Setting::get('opening_sun_thu', '16:00 – 22:45'),
                'opening_fri_sat' => Setting::get('opening_fri_sat', '16:00 – 23:15'),
                'hero_text'       => Setting::get('hero_text',       ''),
                'story_text'      => Setting::get('story_text',      ''),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name'      => 'required|string|max:100',
            'store_address'   => 'required|string|max:255',
            'store_phone'     => 'required|string|max:30',
            'store_email'     => 'required|email|max:100',
            'opening_sun_thu' => 'required|string|max:50',
            'opening_fri_sat' => 'required|string|max:50',
            'hero_text'       => 'nullable|string|max:500',
            'story_text'      => 'nullable|string|max:1000',
        ]);

        Setting::setMany($data);

        return redirect()->route('admin.settings.index')->with('success', 'Settings saved.');
    }
}
```

- [ ] **Step 4: Update admin settings view to use DB values**

Open `resources/views/admin/settings/index.blade.php`. Add flash success banner after `@section('content')`:
```blade
@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">{{ session('success') }}</div>
@endif
```

Find the outer `<div class="grid ...">` containing all the settings sections and wrap it in a form:
```blade
<form method="POST" action="{{ route('admin.settings.update') }}">
  @csrf
  {{-- existing grid content --}}
</form>
```

Update the hardcoded input values to use `$settings`:
- Address input: `value="{{ $settings['store_address'] }}"`
- Phone input: `value="{{ $settings['store_phone'] }}"`
- Email input: `value="{{ $settings['store_email'] }}"`
- Hero text textarea: `{{ $settings['hero_text'] }}`
- Story text textarea: `{{ $settings['story_text'] }}`
- Opening Sun–Thu: `value="{{ $settings['opening_sun_thu'] }}"`
- Opening Fri–Sat: `value="{{ $settings['opening_fri_sat'] }}"`

Also add name attributes to each input:
- Address: `name="store_address"`
- Phone: `name="store_phone"`
- Email: `name="store_email"`
- Hero text: `name="hero_text"`
- Story text: `name="story_text"`
- Opening Sun–Thu: `name="opening_sun_thu"`
- Opening Fri–Sat: `name="opening_fri_sat"`

Change the existing "Update Records" button to a submit button inside the form. Find other "save" buttons and make them submit the same form.

- [ ] **Step 5: Wire footer to use settings**

Open `resources/views/layouts/app.blade.php`. Find the footer address, phone, email, and hours — they're hardcoded. Replace with `Setting::get()` calls:

Find: `156 &amp; 158 Fortess Road` → Replace with `{{ Setting::get('store_address', '156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP') }}`
Find: `+44 020 7485 4033` → Replace with `{{ Setting::get('store_phone', '+44 020 7485 4033') }}`
Find: `nw5pizza@gmail.com` → Replace with `{{ Setting::get('store_email', 'nw5pizza@gmail.com') }}`
Find: `16:00 – 22:45` (Sun–Thu) → Replace with `{{ Setting::get('opening_sun_thu', '16:00 – 22:45') }}`
Find: `16:00 – 23:15` (Fri–Sat) → Replace with `{{ Setting::get('opening_fri_sat', '16:00 – 23:15') }}`

Add at the top of the file: `@use(App\Models\Setting)` — OR just call `\App\Models\Setting::get(...)` with the full namespace.

- [ ] **Step 6: Run settings tests**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/SettingsTest.php
```

Expected: 4 passed.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/Admin/SettingsController.php resources/views/admin/settings/index.blade.php resources/views/layouts/app.blade.php routes/web.php tests/Feature/SettingsTest.php
git commit -m "feat: settings persist to DB, footer reads from settings"
```

---

## Task 7: Full Test Suite + Final Build

- [ ] **Step 1: Run full test suite**

```bash
& "C:\xampp\php\php.exe" artisan test
```

Expected: all tests pass (72 from Plan 6 + 8 promo + 6 checkout + 4 delivery + 4 settings = 94+ total).

Fix any failures before proceeding.

- [ ] **Step 2: Clear caches and build**

```bash
& "C:\xampp\php\php.exe" artisan config:clear
& "C:\xampp\php\php.exe" artisan view:clear
npm run build
```

Expected: `✓ built in Xms`.

- [ ] **Step 3: Final commit**

```bash
git add .
git commit -m "feat: Plan 7 complete — promotions, delivery zones, settings all wired to DB"
```
