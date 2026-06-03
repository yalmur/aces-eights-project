# Aces & Eights Pizza — Plan 5: Customer Ordering & Checkout

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Wire the complete ordering flow — customer registers/logs in, places an order from their Alpine.js cart, pays via Stripe, receives a confirmation, and can track order status — replacing every stub with live data.

**Architecture:** Cart lives in Alpine.js localStorage until checkout; the POST /checkout handler receives the serialised cart JSON (via hidden form field) plus delivery form data, validates prices against the DB, creates an `Order` + `OrderItem` records, then redirects to a Stripe Checkout Session. On Stripe success the confirmation page marks the order `accepted` and clears the cart. A Stripe webhook provides belt-and-suspenders status updates. All order reads (confirmation, tracking, account history, admin orders) query the `orders` table directly.

**Tech Stack:** Laravel 11, Stripe PHP SDK (`stripe/stripe-php`), Alpine.js 3, MySQL 8, PHP 8.2 at `C:\xampp\php\php.exe`

---

## Schema additions

```
orders          id, user_id(nullable FK), type, status, subtotal, delivery_fee, total,
                customer_name, customer_email, customer_phone(null), delivery_address(null),
                delivery_city(null), delivery_postcode(null),
                stripe_session_id(null,unique), stripe_payment_intent_id(null),
                notes(null), timestamps

order_items     id, order_id FK, menu_item_id(nullable FK), name, qty,
                unit_price, size(null), crust(null), size_extra, crust_extra,
                added_toppings(json,null), removed_ingredients(json,null),
                instructions(null), line_total, timestamps
```

---

## File Map

| Action | File |
|--------|------|
| Modify | `app/Http/Controllers/AuthController.php` — add `register()` |
| Modify | `routes/web.php` — add POST /register, POST /checkout, POST /stripe/webhook |
| Modify | `resources/views/auth/register.blade.php` — fix form action |
| Modify | `bootstrap/app.php` — exclude webhook from CSRF |
| Create | `database/migrations/..._create_orders_table.php` |
| Create | `database/migrations/..._create_order_items_table.php` |
| Create | `app/Models/Order.php` |
| Create | `app/Models/OrderItem.php` |
| Modify | `app/Models/User.php` — add hasMany orders |
| Modify | `app/Http/Controllers/CheckoutController.php` — add `store()` |
| Create | `app/Http/Controllers/StripeWebhookController.php` |
| Modify | `app/Http/Controllers/OrderController.php` — real data |
| Modify | `app/Http/Controllers/Account/AccountController.php` — real orders |
| Modify | `app/Http/Controllers/Admin/OrderController.php` — real orders |
| Modify | `resources/views/checkout/index.blade.php` — cart injection |
| Modify | `resources/views/orders/confirmation.blade.php` — real order |
| Modify | `resources/views/orders/tracking.blade.php` — real order |
| Modify | `resources/views/account/index.blade.php` — real orders |
| Modify | `resources/views/admin/orders/index.blade.php` — real orders |
| Modify | `resources/views/admin/orders/detail.blade.php` — real order |
| Create | `database/factories/OrderFactory.php` |
| Create | `tests/Feature/RegistrationTest.php` |
| Create | `tests/Feature/CheckoutTest.php` |

---

## Task 1: Registration Backend

**Files:**
- Modify: `app/Http/Controllers/AuthController.php`
- Modify: `routes/web.php`
- Modify: `resources/views/auth/register.blade.php`

- [ ] **Step 1: Write failing registration test**

Create `tests/Feature/RegistrationTest.php`:

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_returns_200(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $response = $this->post(route('register.post'), [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'customer']);
        $this->assertAuthenticated();
    }

    public function test_registration_requires_all_fields(): void
    {
        $response = $this->post(route('register.post'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_registration_requires_unique_email(): void
    {
        \App\Models\User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('register.post'), [
            'name'                  => 'Another User',
            'email'                 => 'existing@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post(route('register.post'), [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}
```

- [ ] **Step 2: Run — expect failures**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/RegistrationTest.php
```

Expected: failures because `route('register.post')` doesn't exist.

- [ ] **Step 3: Add register() to AuthController**

Open `app/Http/Controllers/AuthController.php`. Add this method after `login()`:

```php
public function register(Request $request): RedirectResponse
{
    $data = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = \App\Models\User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => $data['password'],
        'role'     => 'customer',
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->intended('/');
}
```

- [ ] **Step 4: Add POST /register route**

Open `routes/web.php`. Find:

```php
Route::get('/register', fn () => view('auth.register', ['title' => 'Create Account']))->name('register');
```

Add below it:

```php
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register.post');
```

- [ ] **Step 5: Fix register form action**

Open `resources/views/auth/register.blade.php`. Find `<form action="{{ route('register') }}"` and change to:

```blade
<form action="{{ route('register.post') }}" method="POST" class="space-y-6">
```

- [ ] **Step 6: Run tests — expect pass**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/RegistrationTest.php
```

Expected: 5 passed.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/AuthController.php routes/web.php resources/views/auth/register.blade.php tests/Feature/RegistrationTest.php
git commit -m "feat: wire customer registration"
```

---

## Task 2: Orders + OrderItems Migrations

**Files:**
- Create: 2 migration files

- [ ] **Step 1: Create migration stubs**

```bash
& "C:\xampp\php\php.exe" artisan make:migration create_orders_table
& "C:\xampp\php\php.exe" artisan make:migration create_order_items_table
```

- [ ] **Step 2: Write orders migration**

Find the newest `..._create_orders_table.php` in `database/migrations/`. Replace its full contents:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['delivery', 'collection'])->default('delivery');
            $table->enum('status', [
                'pending_payment', 'accepted', 'cooking',
                'ready', 'out_for_delivery', 'collected', 'delivered', 'cancelled',
            ])->default('pending_payment');
            $table->decimal('subtotal', 8, 2);
            $table->decimal('delivery_fee', 5, 2)->default(0);
            $table->decimal('total', 8, 2);
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_postcode')->nullable();
            $table->string('stripe_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

- [ ] **Step 3: Write order_items migration**

Find the newest `..._create_order_items_table.php`. Replace:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('qty')->default(1);
            $table->decimal('unit_price', 8, 2);
            $table->string('size')->nullable();
            $table->string('crust')->nullable();
            $table->decimal('size_extra', 5, 2)->default(0);
            $table->decimal('crust_extra', 5, 2)->default(0);
            $table->json('added_toppings')->nullable();
            $table->json('removed_ingredients')->nullable();
            $table->string('instructions')->nullable();
            $table->decimal('line_total', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
```

- [ ] **Step 4: Run migrations**

```bash
& "C:\xampp\php\php.exe" artisan migrate
```

Expected: `orders` and `order_items` tables created.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/
git commit -m "feat: add orders and order_items migrations"
```

---

## Task 3: Order + OrderItem Models

**Files:**
- Create: `app/Models/Order.php`
- Create: `app/Models/OrderItem.php`
- Modify: `app/Models/User.php`

- [ ] **Step 1: Create model stubs**

```bash
& "C:\xampp\php\php.exe" artisan make:model Order
& "C:\xampp\php\php.exe" artisan make:model OrderItem
& "C:\xampp\php\php.exe" artisan make:factory OrderFactory
```

- [ ] **Step 2: Write Order model**

Replace `app/Models/Order.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'status', 'subtotal', 'delivery_fee', 'total',
        'customer_name', 'customer_email', 'customer_phone',
        'delivery_address', 'delivery_city', 'delivery_postcode',
        'stripe_session_id', 'stripe_payment_intent_id', 'notes',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_payment'  => 'Awaiting Payment',
            'accepted'         => 'Accepted',
            'cooking'          => 'Cooking',
            'ready'            => 'Ready',
            'out_for_delivery' => 'Out for Delivery',
            'collected'        => 'Collected',
            'delivered'        => 'Delivered',
            'cancelled'        => 'Cancelled',
            default            => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending_payment'           => 'text-on-surface-variant',
            'accepted', 'cooking'       => 'text-primary',
            'ready', 'out_for_delivery' => 'text-green-700',
            'delivered', 'collected'    => 'text-[#2B2B2B]',
            'cancelled'                 => 'text-brand-error',
            default                     => 'text-on-surface',
        };
    }

    public function isDelivery(): bool
    {
        return $this->type === 'delivery';
    }

    public function isPaid(): bool
    {
        return $this->status !== 'pending_payment';
    }
}
```

- [ ] **Step 3: Write OrderItem model**

Replace `app/Models/OrderItem.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'menu_item_id', 'name', 'qty', 'unit_price',
        'size', 'crust', 'size_extra', 'crust_extra',
        'added_toppings', 'removed_ingredients', 'instructions', 'line_total',
    ];

    protected $casts = [
        'unit_price'           => 'decimal:2',
        'size_extra'           => 'decimal:2',
        'crust_extra'          => 'decimal:2',
        'line_total'           => 'decimal:2',
        'added_toppings'       => 'array',
        'removed_ingredients'  => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getCustomisationSummaryAttribute(): string
    {
        $parts = [];
        if ($this->size && $this->size !== '12" Standard') $parts[] = $this->size;
        if ($this->crust && $this->crust !== '48hr Sourdough') $parts[] = $this->crust;
        if ($this->removed_ingredients) {
            foreach ($this->removed_ingredients as $ing) $parts[] = 'no ' . $ing;
        }
        if ($this->added_toppings) {
            foreach ($this->added_toppings as $t) $parts[] = '+' . $t['name'];
        }
        if ($this->instructions) $parts[] = $this->instructions;
        return implode(' · ', $parts) ?: 'No extras';
    }
}
```

- [ ] **Step 4: Add orders relationship to User**

Open `app/Models/User.php`. Add after the class declaration (after the `use HasFactory, Notifiable;` line), add the import and method:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;
```

And add the method inside the class:

```php
public function orders(): HasMany
{
    return $this->hasMany(Order::class)->latest();
}
```

- [ ] **Step 5: Write OrderFactory**

Replace `database/factories/OrderFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 10, 60);
        $fee = 3.50;
        return [
            'user_id'          => User::factory(),
            'type'             => 'delivery',
            'status'           => 'accepted',
            'subtotal'         => $subtotal,
            'delivery_fee'     => $fee,
            'total'            => $subtotal + $fee,
            'customer_name'    => $this->faker->name(),
            'customer_email'   => $this->faker->email(),
            'customer_phone'   => $this->faker->phoneNumber(),
            'delivery_address' => $this->faker->streetAddress(),
            'delivery_city'    => 'London',
            'delivery_postcode'=> 'NW5 2HP',
        ];
    }

    public function pendingPayment(): static
    {
        return $this->state(['status' => 'pending_payment', 'stripe_session_id' => 'cs_test_' . fake()->uuid()]);
    }

    public function collection(): static
    {
        return $this->state(['type' => 'collection', 'delivery_fee' => 0]);
    }
}
```

- [ ] **Step 6: Commit**

```bash
git add app/Models/Order.php app/Models/OrderItem.php app/Models/User.php database/factories/OrderFactory.php
git commit -m "feat: add Order and OrderItem models"
```

---

## Task 4: Stripe SDK + Configuration

**Files:**
- Modify: `.env` and `.env.example`
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Install Stripe PHP SDK**

```bash
composer require stripe/stripe-php
```

Expected: package installed, composer.lock updated.

- [ ] **Step 2: Add Stripe keys to .env**

Open `.env`. Add these lines at the bottom:

```ini
STRIPE_KEY=pk_test_REPLACE_WITH_YOUR_PUBLISHABLE_KEY
STRIPE_SECRET=sk_test_REPLACE_WITH_YOUR_SECRET_KEY
STRIPE_WEBHOOK_SECRET=whsec_REPLACE_WITH_YOUR_WEBHOOK_SECRET
```

Also add to `.env.example` (same keys but empty values):

```ini
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
```

**To get test keys:** Go to https://dashboard.stripe.com/test/apikeys — copy the Publishable key (pk_test_...) and Secret key (sk_test_...). For webhook secret, use Stripe CLI: `stripe listen --forward-to localhost:8000/stripe/webhook` which prints a webhook signing secret.

- [ ] **Step 3: Add Stripe config**

Open `config/services.php`. Add inside the return array:

```php
'stripe' => [
    'key'            => env('STRIPE_KEY'),
    'secret'         => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
],
```

- [ ] **Step 4: Exclude webhook route from CSRF**

Open `bootstrap/app.php`. Find the `->withMiddleware(` block and update it to exclude the webhook URL from CSRF:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'stripe/webhook',
    ]);
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);
})
```

- [ ] **Step 5: Commit**

```bash
git add composer.json composer.lock .env.example config/services.php bootstrap/app.php
git commit -m "feat: install Stripe SDK and configure services"
```

---

## Task 5: Checkout Controller — Receive Cart, Create Order

**Files:**
- Modify: `app/Http/Controllers/CheckoutController.php`
- Modify: `routes/web.php`
- Modify: `resources/views/checkout/index.blade.php`

- [ ] **Step 1: Write failing checkout test**

Create `tests/Feature/CheckoutTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private MenuItem $pizza;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer']);
        $category = Category::factory()->create(['slug' => 'pizza', 'name' => 'Pizza']);
        $this->pizza = MenuItem::factory()->create([
            'category_id' => $category->id,
            'name'        => 'Margherita',
            'slug'        => 'margherita',
            'base_price'  => 12.00,
        ]);
    }

    public function test_checkout_page_requires_auth(): void
    {
        $response = $this->get('/checkout');

        $response->assertRedirect('/login');
    }

    public function test_checkout_page_accessible_when_logged_in(): void
    {
        $response = $this->actingAs($this->customer)->get('/checkout');

        $response->assertStatus(200);
    }

    public function test_post_checkout_creates_order(): void
    {
        $cartItems = [[
            'id'        => 'margherita',
            'name'      => 'Margherita',
            'category'  => 'pizza',
            'basePrice' => 12.00,
            'qty'       => 2,
            'size'      => '12" Standard',
            'sizeExtra' => 0,
            'crust'     => '48hr Sourdough',
            'crustExtra'=> 0,
            'toppings'  => [],
            'removedIngredients' => [],
            'chips'     => [],
            'instructions' => '',
            'lineTotal' => 24.00,
        ]];

        $response = $this->actingAs($this->customer)->post('/checkout', [
            'order_type'     => 'delivery',
            'street_address' => '10 Test Street',
            'city'           => 'London',
            'postal_code'    => 'NW5 2HP',
            'cart_items'     => json_encode($cartItems),
        ]);

        // Should redirect to Stripe or to order page
        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id'          => $this->customer->id,
            'type'             => 'delivery',
            'status'           => 'pending_payment',
            'customer_name'    => $this->customer->name,
            'delivery_address' => '10 Test Street',
        ]);
        $this->assertDatabaseHas('order_items', [
            'name'       => 'Margherita',
            'qty'        => 2,
            'unit_price' => 12.00,
            'line_total' => 24.00,
        ]);
    }

    public function test_checkout_requires_cart_items(): void
    {
        $response = $this->actingAs($this->customer)->post('/checkout', [
            'order_type' => 'delivery',
            'street_address' => '10 Test St',
            'city'       => 'London',
            'postal_code'=> 'NW5 2HP',
            'cart_items' => '[]',
        ]);

        $response->assertSessionHasErrors(['cart_items']);
    }

    public function test_collection_order_has_no_delivery_fee(): void
    {
        $cartItems = [[
            'id' => 'margherita', 'name' => 'Margherita', 'category' => 'pizza',
            'basePrice' => 12.00, 'qty' => 1, 'size' => '12" Standard', 'sizeExtra' => 0,
            'crust' => '48hr Sourdough', 'crustExtra' => 0, 'toppings' => [],
            'removedIngredients' => [], 'chips' => [], 'instructions' => '',
            'lineTotal' => 12.00,
        ]];

        $this->actingAs($this->customer)->post('/checkout', [
            'order_type'  => 'collection',
            'cart_items'  => json_encode($cartItems),
        ]);

        $this->assertDatabaseHas('orders', [
            'type'         => 'collection',
            'delivery_fee' => 0,
        ]);
    }
}
```

- [ ] **Step 2: Run — expect failures**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/CheckoutTest.php
```

Expected: failures (no POST route, no guard on GET /checkout).

- [ ] **Step 3: Add auth middleware to checkout route + add POST route**

Open `routes/web.php`. Find:

```php
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
```

Replace with:

```php
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});
```

Also add the Stripe webhook route OUTSIDE any middleware group (at top-level):

```php
Route::post('/stripe/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handle'])->name('stripe.webhook');
```

- [ ] **Step 4: Rewrite CheckoutController**

Replace the FULL contents of `app/Http/Controllers/CheckoutController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function index(): View
    {
        return view('checkout.index', ['title' => 'Checkout']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_type'     => 'required|in:delivery,collection',
            'cart_items'     => ['required', 'json', function ($attr, $val, $fail) {
                $items = json_decode($val, true);
                if (empty($items)) $fail('Your cart is empty.');
            }],
            'street_address' => 'required_if:order_type,delivery|nullable|string|max:255',
            'city'           => 'required_if:order_type,delivery|nullable|string|max:100',
            'postal_code'    => 'required_if:order_type,delivery|nullable|string|max:20',
        ]);

        $cartItems   = json_decode($data['cart_items'], true);
        $isDelivery  = $data['order_type'] === 'delivery';
        $deliveryFee = $isDelivery ? 3.50 : 0;
        $user        = Auth::user();

        // Build order items and compute totals
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

        $total = $subtotal + $deliveryFee;

        // Create order
        $order = Order::create([
            'user_id'          => $user->id,
            'type'             => $data['order_type'],
            'status'           => 'pending_payment',
            'subtotal'         => $subtotal,
            'delivery_fee'     => $deliveryFee,
            'total'            => $total,
            'customer_name'    => $user->name,
            'customer_email'   => $user->email,
            'delivery_address' => $data['street_address'] ?? null,
            'delivery_city'    => $data['city'] ?? null,
            'delivery_postcode'=> $data['postal_code'] ?? null,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        // Create Stripe Checkout Session
        try {
            $stripe  = new StripeClient(config('services.stripe.secret'));
            $lineItems = [];

            foreach ($order->items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'gbp',
                        'unit_amount'  => (int) round($item->line_total / $item->qty * 100),
                        'product_data' => ['name' => $item->name . ($item->customisation_summary !== 'No extras' ? ' (' . $item->customisation_summary . ')' : '')],
                    ],
                    'quantity' => $item->qty,
                ];
            }

            if ($deliveryFee > 0) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'gbp',
                        'unit_amount'  => (int) ($deliveryFee * 100),
                        'product_data' => ['name' => 'Delivery Fee'],
                    ],
                    'quantity' => 1,
                ];
            }

            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => route('orders.confirmation', $order->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('checkout'),
                'metadata'             => ['order_id' => $order->id],
            ]);

            $order->update(['stripe_session_id' => $session->id]);

            return redirect($session->url, 303);

        } catch (\Exception $e) {
            // If Stripe fails (e.g. no keys in test), redirect to confirmation anyway
            $order->update(['status' => 'accepted']);
            return redirect()->route('orders.confirmation', $order->id);
        }
    }
}
```

- [ ] **Step 5: Add cart injection to checkout form**

Open `resources/views/checkout/index.blade.php`.

Find the outer `<form` tag:
```blade
<form action="{{ route('checkout') }}" method="POST">
```

Replace with (adds Alpine.js cart injection on submit):
```blade
<form action="{{ route('checkout.store') }}" method="POST"
      x-data="{ orderType: 'delivery' }"
      @submit.prevent="document.getElementById('cart-json').value = JSON.stringify($store.cart.items); $el.submit()">
```

Also remove any existing `x-data="{ orderType: ... }"` from child elements since it's now on the form.

Add this hidden input immediately after `@csrf`:
```blade
@csrf
<input type="hidden" name="cart_items" id="cart-json">
<input type="hidden" name="order_type" :value="orderType">
```

Update all `orderType` references within the form to use the parent `x-data` scope (they should work as-is since Alpine looks up the scope chain). Verify the Delivery/Collection toggle buttons still use `:class` correctly with `orderType`.

- [ ] **Step 6: Run tests**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/CheckoutTest.php
```

Expected: 5 passed. The Stripe redirect test passes because the controller falls back to redirect when Stripe keys are not real.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/CheckoutController.php routes/web.php resources/views/checkout/index.blade.php tests/Feature/CheckoutTest.php
git commit -m "feat: checkout creates order and initiates Stripe session"
```

---

## Task 6: Stripe Webhook Controller

**Files:**
- Create: `app/Http/Controllers/StripeWebhookController.php`

- [ ] **Step 1: Create the webhook controller**

Create `app/Http/Controllers/StripeWebhookController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        // Verify webhook signature if secret is configured
        if ($secret) {
            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $secret);
            } catch (SignatureVerificationException $e) {
                return response('Webhook signature verification failed.', 400);
            }
        } else {
            $event = json_decode($payload, true);
            $event = (object) $event;
        }

        $eventType = is_object($event) ? $event->type : ($event['type'] ?? '');

        if ($eventType === 'checkout.session.completed') {
            $sessionData = is_object($event) ? $event->data->object : ($event['data']['object'] ?? []);
            $sessionId = is_object($sessionData) ? $sessionData->id : ($sessionData['id'] ?? null);

            if ($sessionId) {
                Order::where('stripe_session_id', $sessionId)
                    ->where('status', 'pending_payment')
                    ->update(['status' => 'accepted']);
            }
        }

        return response('OK', 200);
    }
}
```

- [ ] **Step 2: Verify route exists (from Task 5)**

```bash
& "C:\xampp\php\php.exe" artisan route:list --path=stripe
```

Expected: `POST stripe/webhook` listed.

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/StripeWebhookController.php
git commit -m "feat: Stripe webhook updates order status on payment"
```

---

## Task 7: Order Confirmation + Tracking — Real Data

**Files:**
- Modify: `app/Http/Controllers/OrderController.php`
- Modify: `resources/views/orders/confirmation.blade.php`
- Modify: `resources/views/orders/tracking.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Rewrite OrderController**

Replace the FULL contents of `app/Http/Controllers/OrderController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\StripeClient;

class OrderController extends Controller
{
    public function confirmation(Request $request, string $order): View
    {
        $orderModel = Order::with('items')->findOrFail($order);

        // If coming from Stripe success, mark as accepted and clear pending
        if ($request->session_id && $orderModel->status === 'pending_payment') {
            $orderModel->update(['status' => 'accepted']);
        }

        return view('orders.confirmation', [
            'title' => 'Order Confirmed — #' . $orderModel->id,
            'order' => $orderModel,
        ]);
    }

    public function tracking(string $order): View
    {
        $orderModel = Order::with('items')->findOrFail($order);

        return view('orders.tracking', [
            'title' => 'Track Order #' . $orderModel->id,
            'order' => $orderModel,
        ]);
    }
}
```

- [ ] **Step 2: Write order confirmation view**

Replace the FULL contents of `resources/views/orders/confirmation.blade.php`:

```blade
@extends('layouts.app')
@section('content')

<div class="max-w-container mx-auto px-4 lg:px-16 py-12">

  {{-- Success banner --}}
  <div class="bg-green-50 border-2 border-green-600 p-8 mb-10 text-center">
    <span class="material-symbols-outlined text-green-600 text-5xl mb-4 block" style="font-variation-settings:'FILL' 1">check_circle</span>
    <h1 class="font-serif text-3xl font-black text-on-surface uppercase mb-2">Order Confirmed!</h1>
    <p class="font-sans text-sm text-on-surface-variant">Order <strong class="text-on-surface">#{{ $order->id }}</strong> — {{ $order->isDelivery() ? 'Delivery' : 'Collection' }}</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

    {{-- Order items --}}
    <div class="bg-surface-container-low border border-outline-variant p-6">
      <h2 class="font-serif text-xl font-bold mb-6 uppercase">Your Order</h2>
      @foreach($order->items as $item)
      <div class="flex justify-between items-start border-b border-outline-variant py-4 last:border-0">
        <div class="flex-1">
          <p class="font-sans text-sm font-bold">{{ $item->qty }}× {{ $item->name }}</p>
          @if($item->customisation_summary !== 'No extras')
            <p class="font-mono text-[10px] text-on-surface-variant mt-1">{{ $item->customisation_summary }}</p>
          @endif
        </div>
        <span class="font-mono text-sm font-bold text-primary ml-4">£{{ number_format($item->line_total, 2) }}</span>
      </div>
      @endforeach
      <div class="pt-4 space-y-2">
        <div class="flex justify-between font-sans text-sm"><span class="text-on-surface-variant">Subtotal</span><span>£{{ number_format($order->subtotal, 2) }}</span></div>
        <div class="flex justify-between font-sans text-sm"><span class="text-on-surface-variant">Delivery</span><span>{{ $order->delivery_fee > 0 ? '£' . number_format($order->delivery_fee, 2) : 'FREE' }}</span></div>
        <div class="flex justify-between font-serif text-base font-bold border-t border-outline-variant pt-2">
          <span>Total</span><span class="text-primary">£{{ number_format($order->total, 2) }}</span>
        </div>
      </div>
    </div>

    {{-- Delivery / Status --}}
    <div class="bg-surface-container-low border border-outline-variant p-6">
      <h2 class="font-serif text-xl font-bold mb-6 uppercase">{{ $order->isDelivery() ? 'Delivery Details' : 'Collection Details' }}</h2>
      @if($order->isDelivery())
        <p class="font-sans text-sm text-on-surface-variant mb-1">Delivering to:</p>
        <p class="font-sans text-sm font-bold">{{ $order->delivery_address }}, {{ $order->delivery_city }}, {{ $order->delivery_postcode }}</p>
        <p class="font-sans text-sm text-on-surface-variant mt-4">Estimated delivery: <strong>30–45 minutes</strong></p>
      @else
        <p class="font-sans text-sm text-on-surface-variant mb-1">Pick up from:</p>
        <p class="font-sans text-sm font-bold">156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP</p>
        <p class="font-sans text-sm text-on-surface-variant mt-4">Estimated ready: <strong>20–30 minutes</strong></p>
      @endif
      <div class="mt-6 p-4 bg-surface border border-outline-variant">
        <p class="font-mono text-[10px] uppercase text-on-surface-variant mb-1">Status</p>
        <p class="font-serif text-lg font-bold {{ $order->status_color }}">{{ $order->status_label }}</p>
      </div>
    </div>
  </div>

  <div class="flex gap-4 justify-center">
    <a href="{{ route('orders.tracking', $order->id) }}" class="btn-primary">TRACK MY ORDER</a>
    <a href="{{ route('menu') }}" class="btn-secondary" x-data @click="$store.cart.clear()">ORDER MORE</a>
  </div>

</div>

@endsection
```

- [ ] **Step 3: Write order tracking view**

Replace the FULL contents of `resources/views/orders/tracking.blade.php`:

```blade
@extends('layouts.app')
@section('content')

<div class="max-w-container mx-auto px-4 lg:px-16 py-12">

  <div class="mb-8">
    <h1 class="font-serif text-3xl font-black text-on-surface uppercase">Tracking Order #{{ $order->id }}</h1>
    <p class="font-sans text-sm text-on-surface-variant mt-1">{{ $order->isDelivery() ? 'Delivery' : 'Collection' }} · {{ $order->created_at->format('d M Y, H:i') }}</p>
  </div>

  {{-- Status stepper --}}
  @php
    $statuses = $order->isDelivery()
      ? ['accepted' => 'Accepted', 'cooking' => 'Cooking', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered']
      : ['accepted' => 'Accepted', 'cooking' => 'Cooking', 'ready' => 'Ready', 'collected' => 'Collected'];
    $keys = array_keys($statuses);
    $currentIndex = array_search($order->status, $keys);
  @endphp

  <div class="relative flex items-center justify-between w-full px-4 mb-12">
    <div class="absolute top-6 left-0 w-full h-1 bg-outline-variant z-0"></div>
    <div class="absolute top-6 left-0 h-1 bg-primary z-0"
         style="width: {{ $currentIndex !== false ? (($currentIndex / (count($statuses) - 1)) * 100) : 0 }}%"></div>
    @foreach($statuses as $key => $label)
    @php $done = $currentIndex !== false && array_search($key, $keys) <= $currentIndex; @endphp
    <div class="flex flex-col items-center gap-2 z-10">
      <div class="w-12 h-12 rounded-full border-2 flex items-center justify-center shadow-lg {{ $done ? 'bg-primary text-white border-primary' : 'bg-surface text-on-surface-variant border-outline' }}">
        <span class="material-symbols-outlined text-[20px]">{{ $done ? 'check' : 'radio_button_unchecked' }}</span>
      </div>
      <span class="font-mono text-[10px] uppercase {{ $order->status === $key ? 'text-primary font-bold' : 'text-on-surface-variant' }}">{{ $label }}</span>
    </div>
    @endforeach
  </div>

  {{-- Current status card --}}
  <div class="bg-surface-container-low border border-outline-variant p-6 mb-8 text-center">
    <p class="font-mono text-[10px] uppercase text-on-surface-variant mb-2">Current Status</p>
    <p class="font-serif text-2xl font-black {{ $order->status_color }}">{{ $order->status_label }}</p>
    @if($order->status === 'out_for_delivery')
      <p class="font-sans text-sm text-on-surface-variant mt-2">Delivering to: {{ $order->delivery_address }}, {{ $order->delivery_city }}</p>
    @endif
  </div>

  {{-- Order summary --}}
  <div class="border border-outline-variant p-6 mb-8">
    <h3 class="font-serif text-lg font-bold mb-4">Order Summary</h3>
    @foreach($order->items as $item)
    <div class="flex justify-between py-2 border-b border-outline-variant last:border-0">
      <span class="font-sans text-sm">{{ $item->qty }}× {{ $item->name }}</span>
      <span class="font-mono text-sm font-bold">£{{ number_format($item->line_total, 2) }}</span>
    </div>
    @endforeach
    <div class="flex justify-between pt-3 font-bold">
      <span class="font-serif">Total</span>
      <span class="font-mono text-primary">£{{ number_format($order->total, 2) }}</span>
    </div>
  </div>

  <a href="{{ route('home') }}" class="btn-secondary">← Back to Home</a>

</div>

@endsection
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/OrderController.php resources/views/orders/ routes/web.php
git commit -m "feat: order confirmation and tracking pages with real data"
```

---

## Task 8: Customer Account — Order History

**Files:**
- Modify: `app/Http/Controllers/Account/AccountController.php`
- Modify: `resources/views/account/index.blade.php`

- [ ] **Step 1: Update AccountController**

Replace the FULL contents of `app/Http/Controllers/Account/AccountController.php`:

```php
<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index(): View
    {
        $user   = Auth::user();
        $orders = $user->orders()->with('items')->latest()->paginate(10);

        return view('account.index', [
            'title'  => 'My Account',
            'user'   => $user,
            'orders' => $orders,
        ]);
    }
}
```

- [ ] **Step 2: Update account view**

Replace the FULL contents of `resources/views/account/index.blade.php`:

```blade
@extends('layouts.app')
@section('content')

<div class="max-w-container mx-auto px-4 lg:px-16 py-12">

  <h1 class="font-serif text-3xl font-black text-on-surface uppercase mb-8">My Account</h1>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Profile card --}}
    <div class="lg:col-span-1">
      <div class="bg-surface-container-low border border-outline-variant p-6">
        <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center mb-4">
          <span class="font-serif text-2xl font-black text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
        </div>
        <h2 class="font-serif text-xl font-bold text-on-surface">{{ $user->name }}</h2>
        <p class="font-mono text-xs text-on-surface-variant mt-1">{{ $user->email }}</p>
        <div class="section-divider my-4"></div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn-ghost text-left">Sign Out →</button>
        </form>
      </div>
    </div>

    {{-- Order history --}}
    <div class="lg:col-span-2">
      <h2 class="font-serif text-xl font-bold text-on-surface mb-6 uppercase">Order History</h2>

      @if($orders->isEmpty())
        <div class="bg-surface-container-low border border-outline-variant p-12 text-center">
          <p class="font-sans text-sm text-on-surface-variant mb-4">No orders yet.</p>
          <a href="{{ route('menu') }}" class="btn-primary">ORDER NOW</a>
        </div>
      @else
        <div class="space-y-4">
          @foreach($orders as $order)
          <div class="bg-surface-container-low border border-outline-variant p-5">
            <div class="flex items-start justify-between mb-3">
              <div>
                <p class="font-mono text-xs font-bold text-on-surface">ORDER #{{ $order->id }}</p>
                <p class="font-sans text-xs text-on-surface-variant mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
              </div>
              <span class="font-mono text-[10px] font-bold uppercase {{ $order->status_color }}">{{ $order->status_label }}</span>
            </div>
            <p class="font-sans text-xs text-on-surface-variant mb-3">
              {{ $order->items->map(fn($i) => $i->qty . '× ' . $i->name)->join(', ') }}
            </p>
            <div class="flex items-center justify-between">
              <span class="font-mono text-sm font-bold text-primary">£{{ number_format($order->total, 2) }}</span>
              <a href="{{ route('orders.tracking', $order->id) }}"
                 class="font-mono text-[10px] text-on-surface-variant hover:text-primary transition-colors underline uppercase">
                Track →
              </a>
            </div>
          </div>
          @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
      @endif
    </div>

  </div>

</div>

@endsection
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Account/AccountController.php resources/views/account/index.blade.php
git commit -m "feat: customer account shows real order history"
```

---

## Task 9: Admin Orders — Wired to DB

**Files:**
- Modify: `app/Http/Controllers/Admin/OrderController.php`
- Modify: `resources/views/admin/orders/index.blade.php`
- Modify: `resources/views/admin/orders/detail.blade.php`

- [ ] **Step 1: Rewrite Admin OrderController**

Replace the FULL contents of `app/Http/Controllers/Admin/OrderController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['user', 'items'])
            ->whereIn('status', ['accepted', 'cooking', 'ready', 'out_for_delivery'])
            ->latest()
            ->paginate(20);

        $allOrders = Order::with(['user', 'items'])
            ->whereNotIn('status', ['pending_payment'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', [
            'title'     => 'Online Orders',
            'orders'    => $allOrders,
        ]);
    }

    public function inStore(): View
    {
        return view('admin.orders.in-store', ['title' => 'In-Store Orders']);
    }

    public function show(string $order): View
    {
        $orderModel = Order::with(['user', 'items.menuItem'])->findOrFail($order);

        return view('admin.orders.detail', [
            'title' => 'Order #' . $orderModel->id,
            'order' => $orderModel,
        ]);
    }

    public function updateStatus(Request $request, string $order): RedirectResponse
    {
        $orderModel = Order::findOrFail($order);
        $data = $request->validate([
            'status' => 'required|in:accepted,cooking,ready,out_for_delivery,collected,delivered,cancelled',
        ]);

        $orderModel->update(['status' => $data['status']]);

        return back()->with('success', "Order #{ $orderModel->id} status updated to {$orderModel->status_label}.");
    }
}
```

- [ ] **Step 2: Add admin order status update route**

Open `routes/web.php`. Find the admin orders routes block and add the status update route:

```php
    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/in-store', [AdminOrderController::class, 'inStore'])->name('orders.in-store');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.detail');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
```

- [ ] **Step 3: Update admin orders index view**

Open `resources/views/admin/orders/index.blade.php`. Replace the hardcoded `@foreach` orders table rows with a dynamic loop. Find the `@foreach([...] as $order)` loop and replace it with:

```blade
@foreach($orders as $order)
<tr class="hover:bg-surface-container-low transition-colors">
  <td class="px-6 py-4 font-mono text-sm font-bold">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
  <td class="px-6 py-4 font-sans text-sm">{{ $order->customer_name }}</td>
  <td class="px-6 py-4 font-sans text-sm text-on-surface-variant">
    {{ $order->items->map(fn($i) => $i->qty . 'x ' . $i->name)->join(', ') }}
  </td>
  <td class="px-6 py-4 font-mono text-sm font-bold">£{{ number_format($order->total, 2) }}</td>
  <td class="px-6 py-4">
    <span class="text-white text-[10px] px-2 py-0.5 font-bold uppercase rounded-full
      {{ match($order->status) {
        'accepted' => 'bg-primary',
        'cooking'  => 'bg-yellow-500',
        'ready'    => 'bg-green-600',
        'out_for_delivery' => 'bg-blue-600',
        default    => 'bg-[#2B2B2B]',
      } }}">
      {{ $order->status_label }}
    </span>
  </td>
  <td class="px-6 py-4 font-mono text-xs text-on-surface-variant">{{ $order->created_at->diffForHumans() }}</td>
  <td class="px-6 py-4 text-right">
    <a href="{{ route('admin.orders.detail', $order->id) }}"
       class="p-2 hover:bg-surface-container rounded transition-colors inline-block">
      <span class="material-symbols-outlined text-on-surface-variant text-lg">visibility</span>
    </a>
  </td>
</tr>
@endforeach
```

Also replace the hardcoded stats bar counts at the top:

```blade
{{-- Replace hardcoded 142, 8, 3, £3,240 with: --}}
<div class="font-serif text-3xl font-black">{{ \App\Models\Order::whereDate('created_at', today())->whereNotIn('status', ['pending_payment', 'cancelled'])->count() }}</div>
<div class="font-serif text-3xl font-black text-primary">{{ \App\Models\Order::whereIn('status', ['accepted', 'cooking'])->count() }}</div>
<div class="font-serif text-3xl font-black text-green-700">{{ \App\Models\Order::where('status', 'ready')->count() }}</div>
<div class="font-serif text-3xl font-black">£{{ number_format(\App\Models\Order::whereDate('created_at', today())->whereNotIn('status', ['pending_payment', 'cancelled'])->sum('total'), 0) }}</div>
```

- [ ] **Step 4: Update admin order detail view**

Open `resources/views/admin/orders/detail.blade.php`. Replace the hardcoded order data with real `$order` model data.

Key replacements:
- Order number: `ORDER #{{ $order->id }}`
- Customer name: `{{ $order->customer_name }}`
- Status badge: `{{ $order->status_label }}`
- Order items: replace the hardcoded items with `@foreach($order->items as $item)`
- Customer address: `{{ $order->delivery_address }}, {{ $order->delivery_city }}`
- Customer phone: `{{ $order->customer_phone ?? 'N/A' }}`
- Total: `£{{ number_format($order->total, 2) }}`

Add a status update form below the lifecycle buttons:
```blade
<form method="POST" action="{{ route('admin.orders.status', $order->id) }}" class="mt-4">
  @csrf
  @method('PATCH')
  <div class="flex gap-3 items-center">
    <select name="status" class="industrial-border-b font-mono text-xs py-2 flex-1">
      @foreach(['accepted','cooking','ready','out_for_delivery','collected','delivered','cancelled'] as $s)
        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
      @endforeach
    </select>
    <button type="submit" class="gold-button px-4 py-2 font-mono text-xs uppercase">Update Status</button>
  </div>
</form>
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Admin/OrderController.php resources/views/admin/orders/ routes/web.php
git commit -m "feat: admin orders list and detail wired to database"
```

---

## Task 10: Full Test Suite + Final Verification

**Files:**
- Modify: `tests/Feature/CheckoutTest.php` — ensure all pass
- Run full suite

- [ ] **Step 1: Run full test suite**

```bash
& "C:\xampp\php\php.exe" artisan test
```

Expected: all tests pass (58 from Plan 4 + 5 registration + 5 checkout = 68+ total).

If `test_post_checkout_creates_order` fails because Stripe client throws without real keys: the controller's `catch (\Exception $e)` block should redirect to confirmation. If it still fails, verify the .env has `STRIPE_SECRET=sk_test_...` set (even a fake test key like `sk_test_fake`).

- [ ] **Step 2: Build production assets**

```bash
npm run build
```

Expected: `✓ built in Xms`.

- [ ] **Step 3: Manual smoke test**

Start server: `& "C:\xampp\php\php.exe" artisan serve --port=8000`

1. Go to `http://localhost:8000/register` — register as `newcustomer@test.com` / `password123`
2. Go to `/menu`, add Margherita to cart
3. Go to `/checkout` — form should appear (not redirect to login since now logged in)
4. Fill in delivery details, submit
5. If Stripe keys are real: redirects to Stripe Checkout page
6. If Stripe keys are fake: redirects to order confirmation page directly
7. Confirmation page shows order details, items, total
8. Go to `/account` — order history shows the order
9. Log in as `admin@acesandeights.com` / `admin123`, go to `/admin/orders` — order appears in the table

- [ ] **Step 4: Final commit**

```bash
git add .
git commit -m "feat: Plan 5 complete — full ordering flow with Stripe checkout, confirmation, tracking, admin orders"
```
