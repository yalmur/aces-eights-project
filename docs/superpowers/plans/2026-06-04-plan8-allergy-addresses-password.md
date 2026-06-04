# Aces & Eights Pizza — Plan 8: Allergy Backend, Addresses & Password Change

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Wire the admin allergy management page to the database, add customer saved addresses with checkout pre-fill, and add a password change form to the customer account page.

**Architecture:** Three independent subsystems. Allergy: `AllergyController` passes real `Allergen` records and `Setting` values to the existing view; new routes handle allergen CRUD, menu-item allergen sync (using existing `allergen_menu_item` pivot from Plan 4), and disclaimer/alerts persistence via `Setting::setMany`. Addresses: new `user_addresses` migration + `UserAddress` model; account page gets add/delete/set-default forms; checkout gets Alpine.js pre-fill from the user's default address passed via the controller. Password change: new POST `/account/password` route + validation + `Auth::user()->update(['password' => ...])`.

**Tech Stack:** Laravel 11, Eloquent ORM, Alpine.js 3, PHP 8.2 at `C:\xampp\php\php.exe`

---

## Schema addition

```
user_addresses    id, user_id FK cascade, label varchar, street_address,
                  city, postcode, is_default bool default false, timestamps
```

`allergens` and `allergen_menu_item` tables already exist from Plan 4.

---

## File Map

| Action | File |
|--------|------|
| Create | `database/migrations/..._create_user_addresses_table.php` |
| Create | `app/Models/UserAddress.php` |
| Modify | `app/Models/User.php` — add `addresses()` hasMany |
| Modify | `app/Http/Controllers/Admin/AllergyController.php` — full CRUD |
| Modify | `resources/views/admin/allergy/index.blade.php` — wire to DB |
| Modify | `routes/web.php` — allergy routes + address routes + password route |
| Modify | `app/Http/Controllers/Account/AccountController.php` — addresses + default address for checkout |
| Create | `app/Http/Controllers/Account/AddressController.php` |
| Create | `app/Http/Controllers/Account/PasswordController.php` |
| Modify | `resources/views/account/index.blade.php` — addresses + password change tabs |
| Modify | `resources/views/checkout/index.blade.php` — pre-fill from default address |
| Create | `tests/Feature/AllergyAdminTest.php` |
| Create | `tests/Feature/Account/AddressTest.php` |
| Create | `tests/Feature/Account/PasswordChangeTest.php` |

---

## Task 1: Allergy Admin — Wire to DB + CRUD

**Files:**
- Modify: `app/Http/Controllers/Admin/AllergyController.php`
- Modify: `resources/views/admin/allergy/index.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Write failing allergy admin tests**

Create `tests/Feature/AllergyAdminTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Allergen;
use App\Models\MenuItem;
use App\Models\Category;
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
        $cat  = Category::factory()->create(['slug' => 'pizza']);
        $item = MenuItem::factory()->create(['category_id' => $cat->id]);
        $gluten = Allergen::factory()->create(['name' => 'Gluten']);
        $dairy  = Allergen::factory()->create(['name' => 'Dairy']);

        $this->actingAs($this->admin)->post('/admin/allergy/map', [
            'menu_item_id' => $item->id,
            'allergens'    => [$gluten->id, $dairy->id],
        ]);

        $this->assertDatabaseHas('allergen_menu_item', ['menu_item_id' => $item->id, 'allergen_id' => $gluten->id]);
        $this->assertDatabaseHas('allergen_menu_item', ['menu_item_id' => $item->id, 'allergen_id' => $dairy->id]);
    }

    public function test_admin_can_save_global_settings(): void
    {
        $this->actingAs($this->admin)->post('/admin/allergy/settings', [
            'allergy_alerts_enabled' => '1',
            'checkout_disclaimer'    => 'Custom disclaimer text.',
        ]);

        $this->assertDatabaseHas('settings', ['key' => 'checkout_disclaimer', 'value' => 'Custom disclaimer text.']);
    }
}
```

Run: `& "C:\xampp\php\php.exe" artisan test tests/Feature/AllergyAdminTest.php` — expect failures.

- [ ] **Step 2: Add allergen/allergy routes**

Open `routes/web.php`. Find the admin allergy route (currently just GET):
```php
    // Allergy Management
    Route::get('/allergy', [AllergyController::class, 'index'])->name('allergy.index');
```

Replace with:
```php
    // Allergy Management
    Route::get('/allergy',                         [AllergyController::class, 'index'])->name('allergy.index');
    Route::post('/allergy/settings',               [AllergyController::class, 'saveSettings'])->name('allergy.settings');
    Route::post('/allergy/map',                    [AllergyController::class, 'saveMap'])->name('allergy.map');
    Route::patch('/allergens/{allergen}/toggle',   [AllergyController::class, 'toggle'])->name('allergens.toggle');
    Route::delete('/allergens/{allergen}',         [AllergyController::class, 'destroyAllergen'])->name('allergens.destroy');
```

- [ ] **Step 3: Rewrite AllergyController**

Replace the full contents of `app/Http/Controllers/Admin/AllergyController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AllergyController extends Controller
{
    public function index(): View
    {
        return view('admin.allergy.index', [
            'title'             => 'Allergy Management',
            'allergens'         => Allergen::orderBy('sort_order')->get(),
            'menuItems'         => MenuItem::with('category')->orderBy('name')->get(),
            'alertsEnabled'     => Setting::get('allergy_alerts_enabled', '1') === '1',
            'disclaimer'        => Setting::get('checkout_disclaimer',
                'ACES & EIGHTS PIZZA CO. TAKES FOOD SAFETY SERIOUSLY. Please be advised that our kitchen handles wheat, dairy, and eggs. While we take meticulous steps to prevent cross-contact, we cannot guarantee a 100% allergen-free environment for those with severe sensitivities. By proceeding with your order, you acknowledge these risks. Contact our floor manager for specific ingredient concerns.'
            ),
        ]);
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'allergy_alerts_enabled' => 'boolean',
            'checkout_disclaimer'    => 'nullable|string|max:2000',
        ]);

        Setting::setMany([
            'allergy_alerts_enabled' => $request->boolean('allergy_alerts_enabled') ? '1' : '0',
            'checkout_disclaimer'    => $data['checkout_disclaimer'] ?? '',
        ]);

        return redirect()->route('admin.allergy.index')->with('success', 'Allergy settings saved.');
    }

    public function saveMap(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'allergens'    => 'nullable|array',
            'allergens.*'  => 'exists:allergens,id',
        ]);

        $item = MenuItem::findOrFail($data['menu_item_id']);
        $item->allergens()->sync($data['allergens'] ?? []);

        return redirect()->route('admin.allergy.index')->with('success', "Allergen mapping saved for '{$item->name}'.");
    }

    public function toggle(string $allergen): RedirectResponse
    {
        $a = Allergen::findOrFail($allergen);
        $a->update(['is_visible' => !$a->is_visible]);
        return redirect()->route('admin.allergy.index')
            ->with('success', "'{$a->name}' " . ($a->is_visible ? 'visible' : 'hidden') . '.');
    }

    public function destroyAllergen(string $allergen): RedirectResponse
    {
        $a = Allergen::findOrFail($allergen);
        $name = $a->name;
        $a->delete();
        return redirect()->route('admin.allergy.index')->with('success', "'{$name}' removed.");
    }
}
```

- [ ] **Step 4: Update allergy admin view**

Open `resources/views/admin/allergy/index.blade.php`. Make targeted edits:

**Edit A** — Add flash banner right after `@section('content')`:
```blade
@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs">{{ session('success') }}</div>
@endif
```

**Edit B** — Replace the `x-data="{ alerts: true }"` Section 1 with a form-driven version. Find the Global Safety Alerts `<section` and replace it:

```blade
{{-- Section 1: Global Allergy Alerts --}}
<form method="POST" action="{{ route('admin.allergy.settings') }}" class="mb-10">
  @csrf
  <section class="bg-surface-container-low border-2 border-on-surface p-6">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">campaign</span>
        <h3 class="font-mono text-xs font-bold uppercase">Global Safety Alerts</h3>
      </div>
      <label class="relative inline-flex items-center cursor-pointer">
        <input name="allergy_alerts_enabled" type="checkbox" value="1" {{ $alertsEnabled ? 'checked' : '' }} class="sr-only peer"/>
        <div class="w-11 h-6 bg-surface-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
      </label>
    </div>
    <p class="font-sans text-sm text-on-surface-variant leading-relaxed mb-6">
      When enabled, a high-visibility warning banner will appear across the top of the consumer site regarding ingredient cross-contamination and the current allergy protocol.
    </p>

    {{-- Checkout Disclaimer --}}
    <div class="mt-4">
      <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-2">Checkout Disclaimer</label>
      <textarea name="checkout_disclaimer" rows="5" class="w-full border-2 border-on-surface p-3 font-sans text-sm focus:ring-0 focus:border-primary resize-none">{{ $disclaimer }}</textarea>
    </div>

    <div class="flex justify-end mt-4">
      <button type="submit" class="bg-primary text-on-primary px-6 py-3 font-mono text-xs font-bold uppercase">PUBLISH CHANGES</button>
    </div>
  </section>
</form>
```

**Edit C** — Replace the hardcoded `@foreach([...] as $allergen)` in Section 2 (Allergen Library) with:

```blade
<div class="grid grid-cols-1 gap-3">
  @foreach($allergens as $allergen)
  <div class="bg-surface border border-on-surface flex items-center p-3 justify-between">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 bg-primary-container flex items-center justify-center text-on-primary">
        <span class="material-symbols-outlined">{{ $allergen->icon }}</span>
      </div>
      <span class="font-mono text-xs font-bold uppercase">{{ $allergen->name }}</span>
    </div>
    <div class="flex items-center gap-3">
      <form method="POST" action="{{ route('admin.allergens.toggle', $allergen->id) }}">
        @csrf @method('PATCH')
        <button type="submit" class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors cursor-pointer" style="{{ $allergen->is_visible ? 'font-variation-settings:\'FILL\' 1' : '' }}" title="{{ $allergen->is_visible ? 'Visible — click to hide' : 'Hidden — click to show' }}">
          {{ $allergen->is_visible ? 'visibility' : 'visibility_off' }}
        </button>
      </form>
      <form method="POST" action="{{ route('admin.allergens.destroy', $allergen->id) }}" onsubmit="return confirm('Delete {{ addslashes($allergen->name) }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="material-symbols-outlined text-brand-error hover:opacity-70 cursor-pointer">delete</button>
      </form>
    </div>
  </div>
  @endforeach
</div>
```

**Edit D** — Replace the Section 3 (Menu Item Mapping) static content with a DB-driven form. Replace the `<section class="mb-10">` containing `<h3>Menu Item Allergen Mapping</h3>` with:

```blade
<section class="mb-10">
  <h3 class="font-mono text-xs font-bold uppercase mb-4">Menu Item Allergen Mapping</h3>
  <form method="POST" action="{{ route('admin.allergy.map') }}" x-data="{ selectedItem: null }">
    @csrf
    <div class="mb-4">
      <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-2">Select Menu Item</label>
      <select name="menu_item_id" required @change="selectedItem = $event.target.value"
              class="w-full bg-surface border-0 border-b-2 border-on-surface py-3 font-sans text-sm focus:ring-0 focus:border-primary">
        <option value="">— Choose item —</option>
        @foreach($menuItems as $item)
          <option value="{{ $item->id }}">{{ $item->category->name }} — {{ $item->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-4">
      <p class="font-mono text-[10px] uppercase mb-3 text-on-surface-variant">Allergen Tags:</p>
      <div class="flex flex-wrap gap-2">
        @foreach($allergens as $allergen)
        <label class="flex items-center gap-2 cursor-pointer px-3 py-1.5 border border-outline-variant hover:border-primary transition-colors">
          <input type="checkbox" name="allergens[]" value="{{ $allergen->id }}"
                 class="w-4 h-4 text-primary border-outline rounded focus:ring-primary">
          <span class="font-mono text-[10px] uppercase">{{ $allergen->name }}</span>
        </label>
        @endforeach
      </div>
    </div>
    <button type="submit" class="w-full border-2 border-on-surface py-3 text-on-surface font-mono text-xs font-bold uppercase hover:bg-surface-container-highest transition-all">
      SAVE ITEM MAPPING
    </button>
  </form>
</section>
```

- [ ] **Step 5: Run tests — expect pass**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/AllergyAdminTest.php
```

Expected: 5 passed.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/AllergyController.php resources/views/admin/allergy/index.blade.php routes/web.php tests/Feature/AllergyAdminTest.php
git commit -m "feat: allergy admin wired to DB — allergen CRUD, menu mapping, disclaimer settings"
```

---

## Task 2: User Addresses — Migration + Model

**Files:**
- Create: `database/migrations/..._create_user_addresses_table.php`
- Create: `app/Models/UserAddress.php`
- Modify: `app/Models/User.php`

- [ ] **Step 1: Create migration**

```bash
& "C:\xampp\php\php.exe" artisan make:migration create_user_addresses_table
```

Find the newest `..._create_user_addresses_table.php` and replace its contents:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Home');
            $table->string('street_address');
            $table->string('city');
            $table->string('postcode', 20);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
```

Run: `& "C:\xampp\php\php.exe" artisan migrate`

- [ ] **Step 2: Create UserAddress model**

Create `app/Models/UserAddress.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'label', 'street_address', 'city', 'postcode', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->street_address}, {$this->city}, {$this->postcode}";
    }
}
```

- [ ] **Step 3: Add addresses relationship to User**

Open `app/Models/User.php`. Add import and method:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;
```

Add method inside class body (after `orders()` method):

```php
public function addresses(): HasMany
{
    return $this->hasMany(UserAddress::class)->orderByDesc('is_default');
}

public function defaultAddress(): ?UserAddress
{
    return $this->addresses()->default()->first();
}
```

- [ ] **Step 4: Commit**

```bash
git add database/migrations/ app/Models/UserAddress.php app/Models/User.php
git commit -m "feat: user addresses migration and model"
```

---

## Task 3: Address CRUD + Password Change — Controllers + Routes

**Files:**
- Create: `app/Http/Controllers/Account/AddressController.php`
- Create: `app/Http/Controllers/Account/PasswordController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Write failing address and password tests**

Create `tests/Feature/Account/AddressTest.php`:

```php
<?php

namespace Tests\Feature\Account;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer']);
    }

    public function test_customer_can_add_address(): void
    {
        $response = $this->actingAs($this->customer)->post('/account/addresses', [
            'label'          => 'Home',
            'street_address' => '10 Test St',
            'city'           => 'London',
            'postcode'       => 'NW5 2HP',
        ]);
        $response->assertRedirect('/account');
        $this->assertDatabaseHas('user_addresses', [
            'user_id'        => $this->customer->id,
            'street_address' => '10 Test St',
        ]);
    }

    public function test_customer_can_delete_address(): void
    {
        $addr = UserAddress::factory()->create(['user_id' => $this->customer->id]);
        $this->actingAs($this->customer)->delete("/account/addresses/{$addr->id}");
        $this->assertDatabaseMissing('user_addresses', ['id' => $addr->id]);
    }

    public function test_customer_cannot_delete_other_users_address(): void
    {
        $other = User::factory()->create();
        $addr  = UserAddress::factory()->create(['user_id' => $other->id]);
        $response = $this->actingAs($this->customer)->delete("/account/addresses/{$addr->id}");
        $response->assertStatus(403);
    }

    public function test_customer_can_set_default_address(): void
    {
        $addr1 = UserAddress::factory()->create(['user_id' => $this->customer->id, 'is_default' => true]);
        $addr2 = UserAddress::factory()->create(['user_id' => $this->customer->id, 'is_default' => false]);
        $this->actingAs($this->customer)->patch("/account/addresses/{$addr2->id}/default");
        $this->assertDatabaseHas('user_addresses', ['id' => $addr2->id, 'is_default' => true]);
        $this->assertDatabaseHas('user_addresses', ['id' => $addr1->id, 'is_default' => false]);
    }
}
```

Create `tests/Feature/Account/PasswordChangeTest.php`:

```php
<?php

namespace Tests\Feature\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create([
            'role'     => 'customer',
            'password' => Hash::make('oldpassword'),
        ]);
    }

    public function test_customer_can_change_password(): void
    {
        $response = $this->actingAs($this->customer)->post('/account/password', [
            'current_password'      => 'oldpassword',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertRedirect('/account');
        $this->assertTrue(Hash::check('newpassword123', $this->customer->fresh()->password));
    }

    public function test_wrong_current_password_rejected(): void
    {
        $response = $this->actingAs($this->customer)->post('/account/password', [
            'current_password'      => 'wrongpassword',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertSessionHasErrors(['current_password']);
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $response = $this->actingAs($this->customer)->post('/account/password', [
            'current_password'      => 'oldpassword',
            'password'              => 'newpassword123',
            'password_confirmation' => 'mismatch',
        ]);
        $response->assertSessionHasErrors(['password']);
    }
}
```

Also create `database/factories/UserAddressFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'label'          => $this->faker->randomElement(['Home', 'Work', 'Other']),
            'street_address' => $this->faker->streetAddress(),
            'city'           => 'London',
            'postcode'       => 'NW' . $this->faker->numberBetween(1, 9) . ' ' . $this->faker->numberBetween(1, 9) . 'HP',
            'is_default'     => false,
        ];
    }
}
```

Run: `& "C:\xampp\php\php.exe" artisan test tests/Feature/Account/` — expect failures.

- [ ] **Step 2: Create AddressController**

Create `app/Http/Controllers/Account/AddressController.php`:

```php
<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label'          => 'required|string|max:50',
            'street_address' => 'required|string|max:255',
            'city'           => 'required|string|max:100',
            'postcode'       => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $isFirst = $user->addresses()->count() === 0;

        $user->addresses()->create(array_merge($data, ['is_default' => $isFirst]));

        return redirect()->route('account')->with('success', 'Address saved.');
    }

    public function destroy(string $address): RedirectResponse
    {
        $addr = UserAddress::findOrFail($address);

        if ($addr->user_id !== Auth::id()) {
            abort(403);
        }

        $addr->delete();

        return redirect()->route('account')->with('success', 'Address removed.');
    }

    public function setDefault(string $address): RedirectResponse
    {
        $addr = UserAddress::findOrFail($address);

        if ($addr->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $addr->update(['is_default' => true]);

        return redirect()->route('account')->with('success', 'Default address updated.');
    }
}
```

- [ ] **Step 3: Create PasswordController**

Create `app/Http/Controllers/Account/PasswordController.php`:

```php
<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->update(['password' => $data['password']]);

        return redirect()->route('account')->with('success', 'Password updated successfully.');
    }
}
```

- [ ] **Step 4: Add routes**

Open `routes/web.php`. Find the account middleware group:
```php
Route::middleware('auth')->prefix('account')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('account');
});
```

Replace with:
```php
Route::middleware('auth')->prefix('account')->group(function () {
    Route::get('/',                                   [AccountController::class, 'index'])->name('account');
    Route::post('/addresses',                         [App\Http\Controllers\Account\AddressController::class, 'store'])->name('account.addresses.store');
    Route::delete('/addresses/{address}',             [App\Http\Controllers\Account\AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::patch('/addresses/{address}/default',      [App\Http\Controllers\Account\AddressController::class, 'setDefault'])->name('account.addresses.default');
    Route::post('/password',                          [App\Http\Controllers\Account\PasswordController::class, 'update'])->name('account.password');
});
```

- [ ] **Step 5: Run tests — expect pass**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/Account/AddressTest.php tests/Feature/Account/PasswordChangeTest.php
```

Expected: 7 passed (4 address + 3 password).

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Account/ database/factories/UserAddressFactory.php routes/web.php tests/Feature/Account/
git commit -m "feat: address CRUD and password change controllers with 7 tests"
```

---

## Task 4: Account Page — Addresses + Password Change UI

**Files:**
- Modify: `app/Http/Controllers/Account/AccountController.php`
- Modify: `resources/views/account/index.blade.php`

- [ ] **Step 1: Update AccountController to pass addresses and default address**

Replace the full contents of `app/Http/Controllers/Account/AccountController.php`:

```php
<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $user    = Auth::user();
        $orders  = $user->orders()->with('items')->paginate(10);
        $addresses = $user->addresses()->get();

        return view('account.index', [
            'title'     => 'My Account',
            'user'      => $user,
            'orders'    => $orders,
            'addresses' => $addresses,
        ]);
    }
}
```

- [ ] **Step 2: Update account view — add addresses section and password change**

Open `resources/views/account/index.blade.php`. Make two additions.

**Addition A** — Add flash banner right after `@section('content')`:
```blade
@if(session('success'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 font-mono text-xs max-w-container mx-auto px-4 lg:px-16">
    {{ session('success') }}
  </div>
@endif
```

**Addition B** — Add two new sections AFTER the order history `</div>` (after the closing div of `lg:col-span-2` orders section but before the closing grid div). Add them as additional grid rows:

```blade
    {{-- Saved Addresses --}}
    <div class="lg:col-span-1">
      <div class="bg-surface-container-low border border-outline-variant p-6">
        <h2 class="font-serif text-base font-bold text-on-surface uppercase mb-4">Saved Addresses</h2>

        @if($addresses->isEmpty())
          <p class="font-sans text-xs text-on-surface-variant mb-4">No saved addresses.</p>
        @else
          <div class="space-y-3 mb-4">
            @foreach($addresses as $addr)
            <div class="border border-outline-variant p-3 {{ $addr->is_default ? 'border-primary' : '' }}">
              <div class="flex justify-between items-start">
                <div>
                  <p class="font-mono text-[10px] font-bold uppercase {{ $addr->is_default ? 'text-primary' : 'text-on-surface-variant' }}">
                    {{ $addr->label }}{{ $addr->is_default ? ' · DEFAULT' : '' }}
                  </p>
                  <p class="font-sans text-xs text-on-surface mt-1">{{ $addr->full_address }}</p>
                </div>
                <div class="flex gap-2 ml-3">
                  @if(!$addr->is_default)
                  <form method="POST" action="{{ route('account.addresses.default', $addr->id) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="font-mono text-[9px] text-primary hover:underline uppercase whitespace-nowrap">Set Default</button>
                  </form>
                  @endif
                  <form method="POST" action="{{ route('account.addresses.destroy', $addr->id) }}" onsubmit="return confirm('Remove this address?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="font-mono text-[9px] text-brand-error hover:underline uppercase">Remove</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        @endif

        {{-- Add address form --}}
        <form method="POST" action="{{ route('account.addresses.store') }}" class="space-y-3" x-data="{ open: false }">
          @csrf
          <button type="button" @click="open = !open" class="font-mono text-[10px] text-primary hover:underline uppercase flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">add</span> Add Address
          </button>
          <div x-show="open" x-cloak class="space-y-3 pt-2">
            <input name="label" placeholder="Label (e.g. Home)" value="{{ old('label', 'Home') }}"
                   class="w-full border-b border-outline-variant font-sans text-xs py-1.5 focus:outline-none focus:border-primary bg-transparent">
            <input name="street_address" placeholder="Street address" value="{{ old('street_address') }}"
                   class="w-full border-b border-outline-variant font-sans text-xs py-1.5 focus:outline-none focus:border-primary bg-transparent">
            <div class="grid grid-cols-2 gap-2">
              <input name="city" placeholder="City" value="{{ old('city', 'London') }}"
                     class="border-b border-outline-variant font-sans text-xs py-1.5 focus:outline-none focus:border-primary bg-transparent">
              <input name="postcode" placeholder="Postcode" value="{{ old('postcode') }}"
                     class="border-b border-outline-variant font-mono text-xs py-1.5 focus:outline-none focus:border-primary bg-transparent uppercase">
            </div>
            <button type="submit" class="btn-primary w-full text-center text-xs py-2">SAVE ADDRESS</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Password Change --}}
    <div class="lg:col-span-2">
      <div class="bg-surface-container-low border border-outline-variant p-6">
        <h2 class="font-serif text-base font-bold text-on-surface uppercase mb-6">Change Password</h2>
        <form method="POST" action="{{ route('account.password') }}" class="max-w-sm space-y-4">
          @csrf
          <div>
            <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Current Password</label>
            <input name="current_password" type="password" required
                   class="w-full border-b-2 border-outline bg-transparent py-2 font-sans text-sm focus:outline-none focus:border-primary">
            @error('current_password')
              <p class="font-mono text-[10px] text-brand-error mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">New Password</label>
            <input name="password" type="password" required minlength="8"
                   class="w-full border-b-2 border-outline bg-transparent py-2 font-sans text-sm focus:outline-none focus:border-primary">
            @error('password')
              <p class="font-mono text-[10px] text-brand-error mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="font-mono text-[10px] uppercase text-on-surface-variant block mb-1">Confirm New Password</label>
            <input name="password_confirmation" type="password" required minlength="8"
                   class="w-full border-b-2 border-outline bg-transparent py-2 font-sans text-sm focus:outline-none focus:border-primary">
          </div>
          <button type="submit" class="btn-primary">UPDATE PASSWORD</button>
        </form>
      </div>
    </div>
```

- [ ] **Step 3: Build and verify**

```bash
npm run build
```

Log in as a customer, go to `/account`. Verify: addresses section shows "No saved addresses" + add form. Password change form renders. Adding an address refreshes with it in the list.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Account/AccountController.php resources/views/account/index.blade.php
git commit -m "feat: account page shows addresses and password change form"
```

---

## Task 5: Checkout — Pre-fill from Default Address

**Files:**
- Modify: `app/Http/Controllers/CheckoutController.php`
- Modify: `resources/views/checkout/index.blade.php`

- [ ] **Step 1: Pass default address from CheckoutController**

Open `app/Http/Controllers/CheckoutController.php`. Update `index()`:

```php
public function index(): View
{
    $defaultAddress = Auth::user()->defaultAddress();

    return view('checkout.index', [
        'title'          => 'Checkout',
        'defaultAddress' => $defaultAddress,
    ]);
}
```

- [ ] **Step 2: Pre-fill checkout form from default address**

Open `resources/views/checkout/index.blade.php`. Find the delivery address inputs. Add `x-init` to the delivery section to pre-fill when Alpine initialises, and add PHP fallback values:

On the Street Address input, add:
```blade
value="{{ old('street_address', $defaultAddress?->street_address) }}"
```

On the City input, add:
```blade
value="{{ old('city', $defaultAddress?->city) }}"
```

On the Postal Code input, add:
```blade
value="{{ old('postal_code', $defaultAddress?->postcode) }}"
```

Also add a hint near the address section if a default address exists:
```blade
@if($defaultAddress)
  <p class="font-mono text-[10px] text-primary mt-1">
    Pre-filled from your saved address: {{ $defaultAddress->full_address }}
  </p>
@endif
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/CheckoutController.php resources/views/checkout/index.blade.php
git commit -m "feat: checkout pre-fills from customer default address"
```

---

## Task 6: Full Test Suite + Final Build

- [ ] **Step 1: Run full test suite**

```bash
& "C:\xampp\php\php.exe" artisan test
```

Expected: all tests pass (89 from Plan 7 + 5 allergy + 7 address+password = 101+ total).

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
git commit -m "feat: Plan 8 complete — allergy management wired, saved addresses, password change"
```
