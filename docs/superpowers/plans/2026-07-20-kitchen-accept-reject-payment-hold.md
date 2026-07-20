# Kitchen Accept/Reject Payment-Hold Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the Kitchen dashboard's one-way "Fire" button with **Accept** (captures the customer's held payment, moves the order to cooking) and **Reject** (voids/refunds the payment, cancels the order, emails the customer an apology).

**Architecture:** Checkout switches Stripe Checkout Sessions to `capture_method: manual` (authorize-only). The existing `accepted` DB status already means "authorized, in the kitchen's queue" — no schema/enum change to that. A new `orders.payment_captured_at` column tracks whether the hold has actually been captured. All payment side-effects (capture on accept, void/refund on reject) live in one place — `Admin\OrderController::updateStatus()` — since every UI (Kitchen dashboard, order detail page, in-store view) already routes through it.

**Tech Stack:** Laravel 12, PHP 8.2, Stripe PHP SDK v20 (`stripe/stripe-php`), MySQL, Blade + Tailwind + Alpine.js, PHPUnit feature/unit tests.

## Global Constraints

- Follow the project's existing test convention: feature tests hit Stripe's real test-mode API directly (no HTTP/SDK mocking) — see `tests/Feature/CheckoutTest.php`. The one exception is documented in Task 3 below, where Stripe's platform makes real-API testing impossible.
- Route name for all order status transitions: `admin.orders.status` (`PATCH /admin/orders/{order}/status`), controller `App\Http\Controllers\Admin\OrderController::updateStatus()`.
- Admin auth: `['auth', 'admin']` middleware; test admin user via `User::factory()->create(['role' => 'admin'])`.
- Stripe config keys: `config('services.stripe.secret')`, `config('services.stripe.webhook_secret')` (`config/services.php:38-41`).
- The exact customer-facing rejection copy: "Sorry, we're unable to accept your order today. Please try again another time."
- The `accepted` status customer-facing label changes from "Accepted" to **"Order Received"** (per `docs/superpowers/specs/2026-07-20-kitchen-accept-reject-payment-hold-design.md`).

---

## File Structure

**New files:**
- `database/migrations/2026_07_20_000000_add_payment_captured_at_to_orders_table.php` — adds the capture-tracking column.
- `app/Mail/OrderRejected.php` + `resources/views/emails/order-rejected.blade.php` — the "sorry" email.
- `tests/Feature/Admin/OrderAcceptRejectTest.php` — capture/void/refund behavior of `updateStatus()`.

**Modified files:**
- `app/Models/Order.php` — `$fillable`, `$casts`, `getStatusLabelAttribute()`.
- `app/Http/Controllers/CheckoutController.php` — manual capture param.
- `app/Http/Controllers/OrderController.php` — fix the confirmation-page reconciliation condition.
- `app/Http/Controllers/Admin/OrderController.php` — capture/void/refund logic in `updateStatus()`.
- `resources/views/admin/kitchen/_card.blade.php` — Fire → Accept/Reject buttons.
- `resources/views/admin/orders/detail.blade.php` — dropdown → Accept/Reject buttons while `accepted`.
- `resources/views/admin/orders/in-store.blade.php` — relabel existing buttons.
- `resources/views/orders/tracking.blade.php` — stepper label.
- `tests/Feature/OrderControllerTest.php`, `tests/Feature/CheckoutTest.php`, `tests/Feature/Admin/KitchenIndexTest.php`, `tests/Feature/Admin/OrderShowTest.php`, `tests/Feature/Admin/InStoreOrderTest.php`, `tests/Unit/OrderModelTest.php` — new assertions for the above.

---

### Task 1: `payment_captured_at` column

**Files:**
- Create: `database/migrations/2026_07_20_000000_add_payment_captured_at_to_orders_table.php`
- Modify: `app/Models/Order.php:14-27`
- Test: `tests/Unit/OrderModelTest.php`

**Interfaces:**
- Produces: `Order::$fillable` includes `'payment_captured_at'`; `Order::$casts['payment_captured_at'] = 'datetime'`.

- [ ] **Step 1: Write the migration**

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
            $table->timestamp('payment_captured_at')->nullable()->after('stripe_payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_captured_at');
        });
    }
};
```

- [ ] **Step 2: Run the migration**

Run: `php artisan migrate`
Expected: `2026_07_20_000000_add_payment_captured_at_to_orders_table ... DONE`

- [ ] **Step 3: Update the Order model**

In `app/Models/Order.php`, change the `$fillable` array (currently lines 14-20) to add `'payment_captured_at'` after `'stripe_payment_intent_id'`:

```php
    protected $fillable = [
        'user_id', 'type', 'status', 'subtotal', 'delivery_fee', 'total',
        'customer_name', 'customer_email', 'customer_phone',
        'delivery_address', 'delivery_city', 'delivery_postcode',
        'stripe_session_id', 'stripe_payment_intent_id', 'payment_captured_at', 'notes',
        'promo_code', 'discount_amount',
    ];
```

And the `$casts` array (currently lines 22-27) to add the new cast:

```php
    protected $casts = [
        'subtotal'             => 'decimal:2',
        'delivery_fee'         => 'decimal:2',
        'total'                => 'decimal:2',
        'discount_amount'      => 'decimal:2',
        'payment_captured_at'  => 'datetime',
    ];
```

- [ ] **Step 4: Write the failing test**

Add to `tests/Unit/OrderModelTest.php` (new section, matches existing plain-`new Order(...)` style):

```php
    // -------------------------------------------------------------------------
    // payment_captured_at
    // -------------------------------------------------------------------------

    public function test_payment_captured_at_is_cast_to_datetime(): void
    {
        $order = new Order(['payment_captured_at' => '2026-07-20 12:00:00']);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $order->payment_captured_at);
    }

    public function test_payment_captured_at_defaults_to_null(): void
    {
        $order = new Order([]);
        $this->assertNull($order->payment_captured_at);
    }
```

- [ ] **Step 5: Run test to verify it fails, then passes**

Run: `php artisan test --filter=test_payment_captured_at`
Expected before Step 3: FAIL (`payment_captured_at` not cast / mass-assignment silently dropped since not in `$fillable`)
Expected after Step 3: PASS

- [ ] **Step 6: Commit**

```bash
git add database/migrations/2026_07_20_000000_add_payment_captured_at_to_orders_table.php app/Models/Order.php tests/Unit/OrderModelTest.php
git commit -m "feat: add payment_captured_at column to track Stripe capture state"
```

---

### Task 2: Checkout uses manual capture

**Files:**
- Modify: `app/Http/Controllers/CheckoutController.php:271-278`
- Test: `tests/Feature/CheckoutTest.php`

**Interfaces:**
- Consumes: none new.
- Produces: every Checkout Session created by `CheckoutController::store()` now creates its underlying PaymentIntent with `capture_method: 'manual'`.

- [ ] **Step 1: Write the failing test**

Add to `tests/Feature/CheckoutTest.php` (uses the same real-Stripe-test-API convention as `test_post_checkout_creates_order` above it):

```php
    public function test_checkout_session_uses_manual_capture(): void
    {
        \App\Models\DeliveryZone::factory()->create(['postcodes' => 'NW5,N7,N19', 'is_active' => true]);

        $cartItems = [[
            'id' => 'margherita', 'name' => 'Margherita', 'category' => 'pizza',
            'basePrice' => 12.00, 'qty' => 1, 'size' => '12" Standard', 'sizeExtra' => 0,
            'crust' => '48hr Sourdough', 'crustExtra' => 0, 'toppings' => [],
            'removedIngredients' => [], 'chips' => [], 'instructions' => '', 'lineTotal' => 12.00,
        ]];

        $this->actingAs($this->customer)->post('/checkout', [
            'order_type'     => 'delivery',
            'street_address' => '10 Test Street',
            'city'           => 'London',
            'postal_code'    => 'NW5 2HP',
            'cart_items'     => json_encode($cartItems),
        ]);

        $order  = \App\Models\Order::where('customer_name', $this->customer->name)->latest()->first();
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($order->stripe_session_id);
        $intent  = $stripe->paymentIntents->retrieve($session->payment_intent);

        $this->assertSame('manual', $intent->capture_method);
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_checkout_session_uses_manual_capture`
Expected: FAIL — `$intent->capture_method` is `'automatic'` (Stripe's default).

- [ ] **Step 3: Implement**

In `app/Http/Controllers/CheckoutController.php`, change the `$sessionParams` array (currently lines 271-278):

```php
            $sessionParams = [
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'payment_intent_data'  => ['capture_method' => 'manual'],
                'success_url'          => route('orders.confirmation', $order->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('checkout'),
                'metadata'             => ['order_id' => $order->id],
            ];
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=test_checkout_session_uses_manual_capture`
Expected: PASS

- [ ] **Step 5: Run the full checkout and webhook suites to check nothing else broke**

Run: `php artisan test tests/Feature/CheckoutTest.php tests/Feature/StripeWebhookTest.php`
Expected: all PASS — `StripeWebhookTest` doesn't inspect capture mode at all (it only reads `payment_intent` as an opaque string off the fabricated event payload), so it should be unaffected by the switch to manual capture. This run locks that in.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/CheckoutController.php tests/Feature/CheckoutTest.php
git commit -m "feat: checkout authorizes payment (manual capture) instead of charging immediately"
```

---

### Task 3: Fix the confirmation-page reconciliation condition

**Context:** `OrderController::reconcileWithStripe()` (added in an earlier session, before this feature existed) currently gates on `$session->payment_status === 'paid'`. Under manual capture, Stripe's Checkout Session `payment_status` stays `'unpaid'` until the PaymentIntent is actually *captured* — it will never read `'paid'` right after checkout anymore. The correct signal that checkout succeeded (card authorized) is `$session->status === 'complete'`.

**Why this task deviates from the "real Stripe API" test convention:** Stripe Checkout Sessions can only be marked `complete` by an actual card confirmation (hosted checkout page or Stripe.js) — there is no server-side API call that fabricates a completed session in test mode. So the condition itself is extracted into a small pure method and unit-tested against a Stripe SDK object built with `constructFrom()` (a real SDK utility for hydrating resource objects from arrays with no network call) — no mocking framework, no network, just Stripe's own supported way to build a fixture object.

**Files:**
- Modify: `app/Http/Controllers/OrderController.php` (the `reconcileWithStripe` method added earlier today)
- Test: `tests/Feature/OrderControllerTest.php`

**Interfaces:**
- Produces: `OrderController::shouldReconcile(\Stripe\Checkout\Session $session): bool` (new private method, pure — no I/O).

- [ ] **Step 1: Write the failing test**

Add to `tests/Feature/OrderControllerTest.php`:

```php
    // ─── Stripe reconciliation condition ─────────────────────────────────────

    public function test_should_reconcile_true_when_session_complete(): void
    {
        $session = \Stripe\Checkout\Session::constructFrom([
            'status'         => 'complete',
            'payment_status' => 'unpaid', // manual capture: stays unpaid even though authorized
            'payment_intent' => 'pi_test_123',
        ]);

        $controller = new \App\Http\Controllers\OrderController();
        $method = new \ReflectionMethod($controller, 'shouldReconcile');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($controller, $session));
    }

    public function test_should_reconcile_false_when_session_still_open(): void
    {
        $session = \Stripe\Checkout\Session::constructFrom([
            'status'         => 'open',
            'payment_status' => 'unpaid',
            'payment_intent' => null,
        ]);

        $controller = new \App\Http\Controllers\OrderController();
        $method = new \ReflectionMethod($controller, 'shouldReconcile');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($controller, $session));
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_should_reconcile`
Expected: FAIL — `shouldReconcile` doesn't exist yet.

- [ ] **Step 3: Implement**

In `app/Http/Controllers/OrderController.php`, replace the `reconcileWithStripe` method (added earlier today) with:

```php
    private function reconcileWithStripe(Order $orderModel): void
    {
        try {
            $stripe  = new StripeClient(config('services.stripe.secret'));
            $session = $stripe->checkout->sessions->retrieve($orderModel->stripe_session_id);

            if ($this->shouldReconcile($session)) {
                $orderModel->update([
                    'status'                   => 'accepted',
                    'stripe_payment_intent_id' => $session->payment_intent,
                ]);
                OrderStatusUpdated::dispatch($orderModel);
                Mail::to($orderModel->customer_email)->queue(new OrderConfirmation($orderModel));
            }
        } catch (\Throwable $e) {
            Log::warning('Stripe confirmation-page reconciliation failed', [
                'order_id' => $orderModel->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function shouldReconcile(\Stripe\Checkout\Session $session): bool
    {
        return $session->status === 'complete';
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=test_should_reconcile`
Expected: PASS

- [ ] **Step 5: Run the full OrderController test file**

Run: `php artisan test tests/Feature/OrderControllerTest.php`
Expected: all PASS

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/OrderController.php tests/Feature/OrderControllerTest.php
git commit -m "fix: confirmation-page reconciliation checks session.status, not payment_status, under manual capture"
```

---

### Task 4: `OrderRejected` mailable

**Files:**
- Create: `app/Mail/OrderRejected.php`
- Create: `resources/views/emails/order-rejected.blade.php`
- Test: new test class, `tests/Feature/OrderRejectedMailTest.php`

**Interfaces:**
- Produces: `new \App\Mail\OrderRejected(Order $order)`, subject `"We're unable to accept your order — Aces & Eights Pizza #{$order->id}"`, view `emails.order-rejected`.
- Consumed by: Task 6 (`Admin\OrderController::updateStatus()`).

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Mail\OrderRejected;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderRejectedMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_rejected_mail_has_correct_subject_and_recipient(): void
    {
        $order = Order::factory()->create(['customer_email' => 'sad-customer@example.com']);

        $mail = new OrderRejected($order);

        $this->assertSame(
            "We're unable to accept your order — Aces & Eights Pizza #{$order->id}",
            $mail->envelope()->subject
        );
    }

    public function test_order_rejected_mail_renders_apology_copy(): void
    {
        $order = Order::factory()->create(['customer_email' => 'sad-customer@example.com']);

        Mail::to($order->customer_email)->send(new OrderRejected($order));

        Mail::assertSent(OrderRejected::class, function ($mail) use ($order) {
            $rendered = $mail->render();
            return str_contains($rendered, "unable to accept your order")
                && str_contains($rendered, "#{$order->id}");
        });
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=OrderRejectedMailTest`
Expected: FAIL — `App\Mail\OrderRejected` doesn't exist.

- [ ] **Step 3: Implement the mailable**

```php
<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRejected extends Mailable
{
    use SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "We're unable to accept your order — Aces & Eights Pizza #{$this->order->id}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-rejected');
    }
}
```

- [ ] **Step 4: Implement the view**

```blade
@component('mail::message')
# Sorry, {{ $order->customer_name }}

We're unable to accept your order today. Please try again another time.

**Order #{{ $order->id }}**

No payment has been taken for this order — any card authorization has been released.

If you have any questions, please contact us directly.

Thanks,<br>
Aces & Eights Pizza
@endcomponent
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=OrderRejectedMailTest`
Expected: PASS

- [ ] **Step 6: Commit**

```bash
git add app/Mail/OrderRejected.php resources/views/emails/order-rejected.blade.php tests/Feature/OrderRejectedMailTest.php
git commit -m "feat: add OrderRejected mailable for kitchen-rejected orders"
```

---

### Task 5: Capture payment on Accept (`accepted → cooking`)

**Context:** Stripe test mode lets you create *and confirm* a manual-capture PaymentIntent purely via the server API using the test payment method `pm_card_visa` (no browser/hosted page needed) — this reaches `requires_capture` status synchronously, giving tests a real object to capture against, consistent with the project's real-API test convention.

**Files:**
- Modify: `app/Http/Controllers/Admin/OrderController.php:136-152` (`updateStatus`)
- Test: Create `tests/Feature/Admin/OrderAcceptRejectTest.php`

**Interfaces:**
- Consumes: `Order::$fillable` includes `payment_captured_at` (Task 1).
- Produces: `updateStatus()` captures the Stripe PaymentIntent before advancing `accepted → cooking`; on capture failure, returns back with an error and does not change status.

- [ ] **Step 1: Write the failing tests**

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderAcceptRejectTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private \Stripe\StripeClient $stripe;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    private function createHeldPaymentIntent(int $amountPence = 1200): \Stripe\PaymentIntent
    {
        return $this->stripe->paymentIntents->create([
            'amount'                    => $amountPence,
            'currency'                  => 'gbp',
            'payment_method'            => 'pm_card_visa',
            'capture_method'            => 'manual',
            'confirm'                   => true,
            'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' => 'never'],
        ]);
    }

    public function test_accept_captures_payment_and_moves_to_cooking(): void
    {
        $intent = $this->createHeldPaymentIntent();
        $order  = Order::factory()->create([
            'status'                   => 'accepted',
            'stripe_payment_intent_id' => $intent->id,
        ]);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking'])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('cooking', $order->status);
        $this->assertNotNull($order->payment_captured_at);

        $capturedIntent = $this->stripe->paymentIntents->retrieve($intent->id);
        $this->assertSame('succeeded', $capturedIntent->status);
    }

    public function test_accept_fails_gracefully_when_capture_errors(): void
    {
        $order = Order::factory()->create([
            'status'                   => 'accepted',
            'stripe_payment_intent_id' => 'pi_does_not_exist_12345',
        ]);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking'])
            ->assertSessionHasErrors(['status']);

        $order->refresh();
        $this->assertSame('accepted', $order->status);
        $this->assertNull($order->payment_captured_at);
    }

    public function test_accept_skips_stripe_for_in_store_order_with_no_payment_intent(): void
    {
        $order = Order::factory()->create([
            'status'                   => 'accepted',
            'stripe_payment_intent_id' => null,
        ]);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking'])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('cooking', $order->status);
        $this->assertNull($order->payment_captured_at);
    }

    public function test_accept_is_idempotent_against_double_click(): void
    {
        $intent = $this->createHeldPaymentIntent();
        $order  = Order::factory()->create([
            'status'                   => 'accepted',
            'stripe_payment_intent_id' => $intent->id,
        ]);

        // First click: captures and moves to cooking.
        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking'])
            ->assertRedirect();

        // Second click with the same payload (double-click): order is now
        // 'cooking', not 'accepted', so isCaptureTransition() is false and the
        // guard in capturePayment() (payment_captured_at already set) means
        // Stripe is never called a second time. Re-capturing an already-
        // captured PaymentIntent would throw — if this silently re-attempted
        // it, this second request would 500 instead of redirecting cleanly.
        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking'])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('cooking', $order->status);

        $intentAfter = $this->stripe->paymentIntents->retrieve($intent->id);
        $this->assertSame('succeeded', $intentAfter->status);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test tests/Feature/Admin/OrderAcceptRejectTest.php`
Expected: FAIL — no capture logic exists yet, so `payment_captured_at` stays null and the invalid-intent case doesn't produce a validation error (it currently just saves the status blindly).

- [ ] **Step 3: Implement**

Replace `Admin\OrderController::updateStatus()` (currently lines 136-152) with:

```php
    public function updateStatus(Request $request, string $order): RedirectResponse
    {
        $orderModel = Order::findOrFail($order);
        $data = $request->validate([
            'status' => 'required|in:accepted,cooking,ready,out_for_delivery,collected,delivered,cancelled',
        ]);
        $newStatus = $data['status'];
        $oldStatus = $orderModel->status;

        if ($this->isCaptureTransition($oldStatus, $newStatus)) {
            $captured = $this->capturePayment($orderModel);
            if (!$captured) {
                return back()->withErrors(['status' => 'Payment could not be captured. The order was not moved — you can retry Accept or Reject it.']);
            }
        }

        $orderModel->update(['status' => $newStatus]);

        if ($newStatus === 'cancelled') {
            $this->voidOrRefundPayment($orderModel);
        }

        OrderStatusUpdated::dispatch($orderModel);

        if (!empty($orderModel->customer_email)) {
            if ($newStatus === 'cancelled' && $oldStatus === 'accepted') {
                Mail::to($orderModel->customer_email)->queue(new OrderRejected($orderModel));
            } else {
                Mail::to($orderModel->customer_email)->queue(new OrderStatusUpdate($orderModel));
            }
        }

        return back()->with('success', "Order #{$orderModel->id} updated to {$orderModel->status_label}.");
    }

    private function isCaptureTransition(string $oldStatus, string $newStatus): bool
    {
        return $oldStatus === 'accepted' && $newStatus === 'cooking';
    }

    private function capturePayment(Order $orderModel): bool
    {
        if (!$orderModel->stripe_payment_intent_id || $orderModel->payment_captured_at) {
            return true; // nothing to capture (in-store order, or already captured)
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $stripe->paymentIntents->capture($orderModel->stripe_payment_intent_id);
            $orderModel->update(['payment_captured_at' => now()]);
            return true;
        } catch (\Throwable $e) {
            Log::warning('Stripe payment capture failed', [
                'order_id' => $orderModel->id,
                'error'    => $e->getMessage(),
            ]);
            return false;
        }
    }
```

Add the new imports at the top of `app/Http/Controllers/Admin/OrderController.php` (alongside the existing `use` statements — `Controller`, `OrderStatusUpdated`, `OrderStatusUpdate`, `Order`, `Request`, `Mail`, `View`, `RedirectResponse` are already there):

```php
use App\Mail\OrderRejected;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
```

(`voidOrRefundPayment` is implemented in Task 6 — until then, leave a private stub so Task 5's tests pass on their own:)

```php
    private function voidOrRefundPayment(Order $orderModel): void
    {
        // implemented in Task 6
    }
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test tests/Feature/Admin/OrderAcceptRejectTest.php`
Expected: all PASS

- [ ] **Step 5: Run the full existing Admin order status suite to check nothing broke**

Run: `php artisan test tests/Feature/Admin/OrderStatusTest.php`
Expected: all PASS (existing generic `accepted → cooking` test at line 26 now also exercises the new capture path against a fake `stripe_payment_intent_id` — check: `Order::factory()` doesn't set one by default, so `capturePayment()` returns `true` immediately via the "nothing to capture" guard; existing tests are unaffected)

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/OrderController.php tests/Feature/Admin/OrderAcceptRejectTest.php
git commit -m "feat: capture held payment when kitchen accepts an order"
```

---

### Task 6: Void or refund payment on Reject

**Files:**
- Modify: `app/Http/Controllers/Admin/OrderController.php` (`voidOrRefundPayment` stub from Task 5)
- Test: `tests/Feature/Admin/OrderAcceptRejectTest.php` (extend)

**Interfaces:**
- Consumes: `App\Mail\OrderRejected` (Task 4).
- Produces: `voidOrRefundPayment(Order $orderModel): void` — cancels an uncaptured PaymentIntent, or refunds an already-captured one.

- [ ] **Step 1: Write the failing tests**

Add to `tests/Feature/Admin/OrderAcceptRejectTest.php`:

```php
    public function test_reject_voids_uncaptured_payment_and_emails_customer(): void
    {
        Mail::fake();
        $intent = $this->createHeldPaymentIntent();
        $order  = Order::factory()->create([
            'status'                   => 'accepted',
            'stripe_payment_intent_id' => $intent->id,
            'customer_email'           => 'rejected-customer@example.com',
        ]);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cancelled'])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('cancelled', $order->status);

        $cancelledIntent = $this->stripe->paymentIntents->retrieve($intent->id);
        $this->assertSame('canceled', $cancelledIntent->status);

        Mail::assertQueued(\App\Mail\OrderRejected::class, fn ($m) => $m->hasTo('rejected-customer@example.com'));
        Mail::assertNotQueued(\App\Mail\OrderStatusUpdate::class);
    }

    public function test_cancelling_already_captured_order_issues_refund_not_void(): void
    {
        Mail::fake();
        $intent = $this->createHeldPaymentIntent();
        $order  = Order::factory()->create([
            'status'                   => 'cooking',
            'stripe_payment_intent_id' => $intent->id,
            'payment_captured_at'      => now(),
            'customer_email'           => 'already-cooking@example.com',
        ]);
        $this->stripe->paymentIntents->capture($intent->id);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cancelled'])
            ->assertRedirect();

        $refunds = $this->stripe->refunds->all(['payment_intent' => $intent->id]);
        $this->assertCount(1, $refunds->data);

        Mail::assertQueued(\App\Mail\OrderStatusUpdate::class);
        Mail::assertNotQueued(\App\Mail\OrderRejected::class);
    }

    public function test_rejected_order_excluded_from_revenue_totals(): void
    {
        Mail::fake();
        $intent = $this->createHeldPaymentIntent(5000);
        $order  = Order::factory()->create([
            'status'                   => 'accepted',
            'stripe_payment_intent_id' => $intent->id,
            'total'                    => 50.00,
        ]);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cancelled']);

        // admin.orders.index computes "today's revenue" via
        // whereNotIn('status', ['pending_payment', 'cancelled']) — a rejected
        // order must not inflate it, same as any other cancelled order.
        $revenue = \App\Models\Order::whereDate('created_at', today())
            ->whereNotIn('status', ['pending_payment', 'cancelled'])
            ->sum('total');

        $this->assertEquals(0, $revenue);
    }

    public function test_reject_skips_stripe_for_in_store_order(): void
    {
        Mail::fake();
        $order = Order::factory()->create([
            'status'                   => 'accepted',
            'stripe_payment_intent_id' => null,
            'customer_email'           => 'walkin@example.com',
        ]);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cancelled'])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('cancelled', $order->status);
        Mail::assertQueued(\App\Mail\OrderRejected::class);
    }
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test tests/Feature/Admin/OrderAcceptRejectTest.php`
Expected: FAIL — `voidOrRefundPayment` is currently an empty stub, so Stripe intents stay in `requires_capture`/`succeeded` untouched.

- [ ] **Step 3: Implement**

Replace the `voidOrRefundPayment` stub in `app/Http/Controllers/Admin/OrderController.php` (added in Task 5) with:

```php
    private function voidOrRefundPayment(Order $orderModel): void
    {
        if (!$orderModel->stripe_payment_intent_id) {
            return; // in-store order, nothing to void or refund
        }

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));

            if ($orderModel->payment_captured_at) {
                $stripe->refunds->create(['payment_intent' => $orderModel->stripe_payment_intent_id]);
            } else {
                $stripe->paymentIntents->cancel($orderModel->stripe_payment_intent_id);
            }
        } catch (\Throwable $e) {
            Log::warning('Stripe void/refund on cancel failed', [
                'order_id' => $orderModel->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test tests/Feature/Admin/OrderAcceptRejectTest.php`
Expected: all PASS

- [ ] **Step 5: Run the full Admin order test suite**

Run: `php artisan test tests/Feature/Admin`
Expected: all PASS

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/OrderController.php tests/Feature/Admin/OrderAcceptRejectTest.php
git commit -m "feat: void or refund payment when an order is cancelled"
```

---

### Task 7: Kitchen dashboard — Fire becomes Accept/Reject

**Files:**
- Modify: `resources/views/admin/kitchen/_card.blade.php:99-107`
- Test: `tests/Feature/Admin/KitchenIndexTest.php`

- [ ] **Step 1: Write the failing test**

Add to `tests/Feature/Admin/KitchenIndexTest.php`:

```php
    public function test_accepted_order_card_shows_accept_and_reject_buttons(): void
    {
        Order::factory()->create(['status' => 'accepted']);

        $response = $this->actingAs($this->admin)->get('/admin/kitchen');

        $response->assertSee('Accept');
        $response->assertSee('Reject');
        $response->assertDontSee('Fire');
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_accepted_order_card_shows_accept_and_reject_buttons`
Expected: FAIL — page still says "Fire".

- [ ] **Step 3: Implement**

In `resources/views/admin/kitchen/_card.blade.php`, replace the `@if($column === 'preparing' && $order->status === 'accepted')` block (currently lines 100-107):

```blade
      @if($column === 'preparing' && $order->status === 'accepted')
        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
          @csrf @method('PATCH')
          <input type="hidden" name="status" value="cooking">
          <button class="px-3 py-1.5 bg-primary hover:bg-primary-hover text-white text-[11px] font-bold uppercase rounded transition-colors flex items-center gap-1">
            Accept <span class="material-symbols-outlined text-[12px]">check</span>
          </button>
        </form>
        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST"
              onsubmit="return confirm('Reject order #{{ $order->id }}? The customer\'s payment hold will be released and they\'ll be notified.')">
          @csrf @method('PATCH')
          <input type="hidden" name="status" value="cancelled">
          <button class="px-3 py-1.5 bg-zinc-800 hover:bg-red-900 text-zinc-300 hover:text-white text-[11px] font-bold uppercase rounded transition-colors flex items-center gap-1">
            Reject <span class="material-symbols-outlined text-[12px]">close</span>
          </button>
        </form>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=test_accepted_order_card_shows_accept_and_reject_buttons`
Expected: PASS

- [ ] **Step 5: Run the full Kitchen test suite**

Run: `php artisan test tests/Feature/Admin/KitchenIndexTest.php tests/Feature/Admin/KitchenPollTest.php`
Expected: all PASS

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/kitchen/_card.blade.php tests/Feature/Admin/KitchenIndexTest.php
git commit -m "feat: replace kitchen Fire button with Accept/Reject"
```

---

### Task 8: Order detail page — Accept/Reject while `accepted`

**Files:**
- Modify: `resources/views/admin/orders/detail.blade.php:49-59`
- Test: `tests/Feature/Admin/OrderShowTest.php`

- [ ] **Step 1: Write the failing tests**

Add to `tests/Feature/Admin/OrderShowTest.php`:

```php
    public function test_detail_page_shows_accept_reject_for_accepted_order(): void
    {
        $order = Order::factory()->create(['status' => 'accepted']);

        $response = $this->actingAs($this->admin)->get("/admin/orders/{$order->id}");

        $response->assertSee('Accept');
        $response->assertSee('Reject');
    }

    public function test_detail_page_shows_dropdown_for_cooking_order(): void
    {
        $order = Order::factory()->create(['status' => 'cooking']);

        $response = $this->actingAs($this->admin)->get("/admin/orders/{$order->id}");

        $response->assertSee('Update Status');
    }
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=test_detail_page_shows`
Expected: FAIL — the dropdown renders unconditionally today, so `test_detail_page_shows_accept_reject_for_accepted_order` fails.

- [ ] **Step 3: Implement**

In `resources/views/admin/orders/detail.blade.php`, replace the status update form block (currently lines 49-59):

```blade
      {{-- Status update --}}
      @if($order->status === 'accepted')
        <div class="mt-4 flex gap-3">
          <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="cooking">
            <button type="submit" class="gold-button px-4 py-2 font-mono text-xs uppercase whitespace-nowrap">Accept</button>
          </form>
          <form action="{{ route('admin.orders.status', $order->id) }}" method="POST"
                onsubmit="return confirm('Reject order #{{ $order->id }}? The customer\'s payment hold will be released and they\'ll be notified.')">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="cancelled">
            <button type="submit" class="border-2 border-on-surface px-4 py-2 font-mono text-xs uppercase whitespace-nowrap">Reject</button>
          </form>
        </div>
      @else
        <form method="POST" action="{{ route('admin.orders.status', $order->id) }}" class="mt-4 flex gap-3 items-center">
          @csrf
          @method('PATCH')
          <select name="status" class="industrial-border-b font-mono text-xs py-2 flex-1">
            @foreach(['accepted','cooking','ready','out_for_delivery','collected','delivered','cancelled'] as $s)
              <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
          </select>
          <button type="submit" class="gold-button px-4 py-2 font-mono text-xs uppercase whitespace-nowrap">Update Status</button>
        </form>
      @endif
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --filter=test_detail_page_shows`
Expected: all PASS

- [ ] **Step 5: Run the full order detail suite**

Run: `php artisan test tests/Feature/Admin/OrderShowTest.php`
Expected: all PASS

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/orders/detail.blade.php tests/Feature/Admin/OrderShowTest.php
git commit -m "feat: order detail page shows Accept/Reject for accepted orders"
```

---

### Task 9: In-store view relabeling

**Files:**
- Modify: `resources/views/admin/orders/in-store.blade.php:231-238`
- Test: `tests/Feature/Admin/InStoreOrderTest.php`

- [ ] **Step 1: Write the failing test**

Add to `tests/Feature/Admin/InStoreOrderTest.php` (check its existing `setUp()`/admin factory pattern first and match it — same pattern as other Admin tests: `User::factory()->create(['role' => 'admin'])`):

```php
    public function test_accepted_in_store_order_shows_accept_label(): void
    {
        $order = Order::factory()->create([
            'status' => 'accepted',
            'type'   => 'collection',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/orders/in-store');

        $response->assertSee('Accept');
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_accepted_in_store_order_shows_accept_label`
Expected: FAIL — button currently says "→ Cooking".

- [ ] **Step 3: Implement**

In `resources/views/admin/orders/in-store.blade.php`, change the `$nextStatus` button block (currently lines 239-248):

```blade
          @if($nextStatus)
            <form method="POST" action="{{ route('admin.orders.status', $order->id) }}">
              @csrf @method('PATCH')
              <input type="hidden" name="status" value="{{ $nextStatus }}">
              <button type="submit"
                      class="bg-on-surface text-surface px-4 py-2 font-label-bold text-[10px] uppercase hover:bg-primary transition-colors">
                {{ $order->status === 'accepted' ? 'Accept' : '→ ' . ucwords(str_replace('_', ' ', $nextStatus)) }}
              </button>
            </form>
          @endif
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=test_accepted_in_store_order_shows_accept_label`
Expected: PASS

- [ ] **Step 5: Run the full in-store suite**

Run: `php artisan test tests/Feature/Admin/InStoreOrderTest.php`
Expected: all PASS

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/orders/in-store.blade.php tests/Feature/Admin/InStoreOrderTest.php
git commit -m "feat: relabel in-store accepted-order button to Accept"
```

---

### Task 10: Customer-facing wording — "Order Received"

**Files:**
- Modify: `app/Models/Order.php:39-52` (`getStatusLabelAttribute`)
- Modify: `resources/views/orders/tracking.blade.php:29-30`
- Test: `tests/Unit/OrderModelTest.php`, `tests/Feature/OrderControllerTest.php`

- [ ] **Step 1: Write the failing tests**

Add to `tests/Unit/OrderModelTest.php`:

```php
    public function test_status_label_for_accepted_is_order_received(): void
    {
        $order = new Order(['status' => 'accepted']);
        $this->assertSame('Order Received', $order->status_label);
    }

    public function test_status_label_for_cooking_is_unchanged(): void
    {
        $order = new Order(['status' => 'cooking']);
        $this->assertSame('Cooking', $order->status_label);
    }
```

Add to `tests/Feature/OrderControllerTest.php`:

```php
    public function test_tracking_page_shows_order_received_for_accepted_status(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'accepted']);

        $this->actingAs($user)
            ->get(route('orders.tracking', $order))
            ->assertSee('Order Received');
    }
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=test_status_label_for_accepted`
Run: `php artisan test --filter=test_tracking_page_shows_order_received`
Expected: FAIL — both still say "Accepted".

- [ ] **Step 3: Implement**

In `app/Models/Order.php`, change the `'accepted'` line in `getStatusLabelAttribute()` (currently line 43):

```php
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_payment'  => 'Awaiting Payment',
            'accepted'         => 'Order Received',
            'cooking'          => 'Cooking',
            'ready'            => 'Ready',
            'out_for_delivery' => 'Out for Delivery',
            'collected'        => 'Collected',
            'delivered'        => 'Delivered',
            'cancelled'        => 'Cancelled',
            default            => ucfirst($this->status),
        };
    }
```

In `resources/views/orders/tracking.blade.php`, change the stepper arrays (currently lines 28-30):

```blade
  @php
    $statuses = $order->isDelivery()
      ? ['accepted' => 'Order Received', 'cooking' => 'Cooking', 'out_for_delivery' => 'Out for Delivery', 'delivered' => 'Delivered']
      : ['accepted' => 'Order Received', 'cooking' => 'Cooking', 'ready' => 'Ready', 'collected' => 'Collected'];
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --filter=test_status_label_for_accepted`
Run: `php artisan test --filter=test_tracking_page_shows_order_received`
Expected: both PASS

- [ ] **Step 5: Run the full model and order-controller suites**

Run: `php artisan test tests/Unit/OrderModelTest.php tests/Feature/OrderControllerTest.php tests/Feature/OrderModelTest.php`
Expected: all PASS

- [ ] **Step 6: Commit**

```bash
git add app/Models/Order.php resources/views/orders/tracking.blade.php tests/Unit/OrderModelTest.php tests/Feature/OrderControllerTest.php
git commit -m "feat: relabel accepted-status customer wording to Order Received"
```

---

### Task 11: Full regression pass

**Files:** none (verification only)

- [ ] **Step 1: Run the entire test suite**

Run: `php artisan test`
Expected: all PASS, zero failures.

- [ ] **Step 2: Manually verify the graphify knowledge graph is still queryable** (per project CLAUDE.md, not required to change but confirm nothing broke)

Run: `& "C:\Python313\python.exe" -m graphify update C:\AcesAndEightsPizza\webapp`
Expected: completes without error (graph auto-updates via the post-commit hook already, this just double-checks).

- [ ] **Step 3: Final commit if any stray changes remain**

```bash
git status
```

Expected: clean working tree (everything already committed per-task above).
