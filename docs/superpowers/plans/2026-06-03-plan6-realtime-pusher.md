# Aces & Eights Pizza — Plan 6: Real-Time Order Status with Pusher

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add real-time order status updates so kitchen staff see new orders immediately and customers watch their order progress live — both powered by Pusher WebSockets via Laravel Broadcasting.

**Architecture:** Server fires `OrderStatusUpdated` event (implements `ShouldBroadcast`) every time admin changes an order status. Event broadcasts on two private Pusher channels: `private-order.{id}` (customer's own order) and `private-admin.orders` (all admin staff). Frontend uses Laravel Echo + pusher-js to subscribe and reactively update Alpine.js state. Works gracefully without real Pusher keys — `BROADCAST_CONNECTION=log` by default so the event still dispatches and tests pass; switch to `pusher` when credentials are available.

**Tech Stack:** `pusher/pusher-php-server` (PHP), `laravel-echo` + `pusher-js` (npm), Laravel 11 Broadcasting, Alpine.js 3

---

## File Map

| Action | File |
|--------|------|
| Create | `config/broadcasting.php` |
| Create | `routes/channels.php` |
| Modify | `bootstrap/app.php` — register channels route |
| Modify | `.env` + `.env.example` — Pusher + VITE vars |
| Create | `app/Events/OrderStatusUpdated.php` |
| Modify | `app/Http/Controllers/Admin/OrderController.php` — fire event |
| Modify | `resources/js/app.js` — Echo init |
| Modify | `resources/views/orders/tracking.blade.php` — live status |
| Modify | `resources/views/admin/orders/index.blade.php` — live queue |
| Modify | `resources/views/admin/kitchen/index.blade.php` — live kanban |
| Create | `tests/Feature/BroadcastingTest.php` |

---

## Task 1: Install Packages

**Files:**
- Modify: `composer.json` (via composer)
- Modify: `package.json` (via npm)

- [ ] **Step 1: Install Pusher PHP server SDK**

```bash
cd C:\AcesAndEightsPizza\webapp
composer require pusher/pusher-php-server
```

Expected: `pusher/pusher-php-server` added to `composer.json`, no errors.

- [ ] **Step 2: Install Laravel Echo + pusher-js**

```bash
npm install --save-dev laravel-echo pusher-js
```

Expected: both packages appear in `node_modules/`, `package.json` updated.

- [ ] **Step 3: Commit**

```bash
git add composer.json composer.lock package.json package-lock.json
git commit -m "feat: install pusher PHP SDK and laravel-echo + pusher-js"
```

---

## Task 2: Broadcasting Configuration

**Files:**
- Create: `config/broadcasting.php`
- Create: `routes/channels.php`
- Modify: `bootstrap/app.php`
- Modify: `.env` + `.env.example`

- [ ] **Step 1: Create `config/broadcasting.php`**

```php
<?php

return [

    'default' => env('BROADCAST_CONNECTION', 'log'),

    'connections' => [

        'pusher' => [
            'driver'  => 'pusher',
            'key'     => env('PUSHER_APP_KEY'),
            'secret'  => env('PUSHER_APP_SECRET'),
            'app_id'  => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'eu'),
                'useTLS'  => true,
            ],
            'client_options' => [],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
```

- [ ] **Step 2: Create `routes/channels.php`**

```php
<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

/*
 * Customers can listen to their own order channel.
 */
Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);
    return $order && $order->user_id === $user->id;
});

/*
 * Admin staff can listen to the shared orders channel.
 */
Broadcast::channel('admin.orders', function ($user) {
    return $user->role === 'admin';
});
```

- [ ] **Step 3: Register channels route in bootstrap/app.php**

Open `bootstrap/app.php`. Find the `->withRouting(` block and add `channels:`:

```php
->withRouting(
    web:      __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    channels: __DIR__.'/../routes/channels.php',
    health:   '/up',
)
```

- [ ] **Step 4: Add Pusher vars to .env**

Open `.env`. The file currently has `BROADCAST_CONNECTION=log`. Add below it:

```ini
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=eu

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

**To get real credentials:** Create a free account at https://pusher.com → create an App → copy App ID, Key, Secret, Cluster into the values above. Until then the app uses `log` driver which is safe.

Also add to `.env.example`:

```ini
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=eu

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

- [ ] **Step 5: Verify config loads**

```bash
& "C:\xampp\php\php.exe" artisan config:clear
& "C:\xampp\php\php.exe" artisan channel:list
```

Expected: shows `order.{orderId}` and `admin.orders` channels (or no error if channel:list not available — just verify no PHP errors).

- [ ] **Step 6: Commit**

```bash
git add config/broadcasting.php routes/channels.php bootstrap/app.php .env.example
git commit -m "feat: broadcasting config, Pusher channels"
```

---

## Task 3: OrderStatusUpdated Event

**Files:**
- Create: `app/Events/OrderStatusUpdated.php`

- [ ] **Step 1: Create Events directory and event class**

Create `app/Events/OrderStatusUpdated.php`:

```php
<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('admin.orders')];

        if ($this->order->user_id) {
            $channels[] = new PrivateChannel('order.' . $this->order->id);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'     => $this->order->id,
            'status'       => $this->order->status,
            'status_label' => $this->order->status_label,
            'status_color' => $this->order->status_color,
            'type'         => $this->order->type,
        ];
    }

    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated';
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Events/OrderStatusUpdated.php
git commit -m "feat: OrderStatusUpdated broadcast event"
```

---

## Task 4: Fire Event + Broadcasting Tests

**Files:**
- Modify: `app/Http/Controllers/Admin/OrderController.php`
- Create: `tests/Feature/BroadcastingTest.php`

- [ ] **Step 1: Write failing broadcast test**

Create `tests/Feature/BroadcastingTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BroadcastingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_status_update_dispatches_order_status_updated_event(): void
    {
        Event::fake([OrderStatusUpdated::class]);

        $order = Order::factory()->create(['status' => 'accepted']);

        $this->actingAs($this->admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => 'cooking',
        ]);

        Event::assertDispatched(OrderStatusUpdated::class, function ($event) use ($order) {
            return $event->order->id === $order->id
                && $event->order->status === 'cooking';
        });
    }

    public function test_event_broadcasts_on_admin_channel(): void
    {
        $order = Order::factory()->create();
        $event = new OrderStatusUpdated($order);

        $channels = $event->broadcastOn();
        $channelNames = array_map(fn ($c) => $c->name, $channels);

        $this->assertContains('private-admin.orders', $channelNames);
    }

    public function test_event_broadcasts_on_customer_channel_when_user_exists(): void
    {
        $customer = User::factory()->create();
        $order    = Order::factory()->create(['user_id' => $customer->id]);
        $event    = new OrderStatusUpdated($order);

        $channels    = $event->broadcastOn();
        $channelNames = array_map(fn ($c) => $c->name, $channels);

        $this->assertContains('private-order.' . $order->id, $channelNames);
    }

    public function test_event_does_not_broadcast_customer_channel_for_guest_order(): void
    {
        $order = Order::factory()->create(['user_id' => null]);
        $event = new OrderStatusUpdated($order);

        $channels    = $event->broadcastOn();
        $channelNames = array_map(fn ($c) => $c->name, $channels);

        $this->assertNotContains('private-order.' . $order->id, $channelNames);
        $this->assertContains('private-admin.orders', $channelNames);
    }

    public function test_event_broadcast_payload_contains_required_fields(): void
    {
        $order   = Order::factory()->create(['status' => 'cooking']);
        $event   = new OrderStatusUpdated($order);
        $payload = $event->broadcastWith();

        $this->assertArrayHasKey('order_id', $payload);
        $this->assertArrayHasKey('status', $payload);
        $this->assertArrayHasKey('status_label', $payload);
        $this->assertArrayHasKey('status_color', $payload);
        $this->assertSame('cooking', $payload['status']);
        $this->assertSame('Cooking', $payload['status_label']);
    }

    public function test_event_broadcast_name_is_correct(): void
    {
        $order = Order::factory()->create();
        $event = new OrderStatusUpdated($order);

        $this->assertSame('OrderStatusUpdated', $event->broadcastAs());
    }
}
```

- [ ] **Step 2: Run — expect failure on first test (no event fired yet)**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/BroadcastingTest.php
```

Expected: `test_status_update_dispatches_order_status_updated_event` fails because controller doesn't fire event yet. Other tests (channel/payload) should pass.

- [ ] **Step 3: Fire event from Admin OrderController**

Open `app/Http/Controllers/Admin/OrderController.php`. Add import at top:

```php
use App\Events\OrderStatusUpdated;
```

In the `updateStatus()` method, add event dispatch after the update:

```php
public function updateStatus(Request $request, string $order): RedirectResponse
{
    $orderModel = Order::findOrFail($order);
    $data = $request->validate([
        'status' => 'required|in:accepted,cooking,ready,out_for_delivery,collected,delivered,cancelled',
    ]);

    $orderModel->update(['status' => $data['status']]);

    OrderStatusUpdated::dispatch($orderModel);

    return back()->with('success', "Order #{$orderModel->id} updated to {$orderModel->status_label}.");
}
```

- [ ] **Step 4: Run all broadcasting tests — expect pass**

```bash
& "C:\xampp\php\php.exe" artisan test tests/Feature/BroadcastingTest.php
```

Expected: 5 passed.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Admin/OrderController.php app/Events/OrderStatusUpdated.php tests/Feature/BroadcastingTest.php
git commit -m "feat: fire OrderStatusUpdated event on status change, add broadcast tests"
```

---

## Task 5: Frontend Echo Setup

**Files:**
- Modify: `resources/js/app.js`

- [ ] **Step 1: Add Echo initialisation to app.js**

Open `resources/js/app.js`. At the top, add the Echo imports. After the existing imports and before `Alpine.store(...)`, add:

```js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// Only initialise Echo if Pusher credentials are provided
if (import.meta.env.VITE_PUSHER_APP_KEY && import.meta.env.VITE_PUSHER_APP_KEY !== 'your_pusher_app_key') {
    window.Pusher = Pusher
    window.Echo = new Echo({
        broadcaster:  'pusher',
        key:          import.meta.env.VITE_PUSHER_APP_KEY,
        cluster:      import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'eu',
        forceTLS:     true,
        authEndpoint: '/broadcasting/auth',
    })
}
```

Read `resources/js/app.js`. The file currently starts with:
```js
import Alpine from 'alpinejs'

window.Alpine = Alpine
```

Make TWO targeted edits only:

**Edit A** — add Echo imports right after `import Alpine from 'alpinejs'`:
```js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
```

**Edit B** — add Echo init block right after `window.Alpine = Alpine` and before the first `Alpine.store(...)` call:
```js
// Initialise Pusher Echo when credentials are available
if (import.meta.env.VITE_PUSHER_APP_KEY && import.meta.env.VITE_PUSHER_APP_KEY !== 'your_pusher_app_key') {
    window.Pusher = Pusher
    window.Echo = new Echo({
        broadcaster:  'pusher',
        key:          import.meta.env.VITE_PUSHER_APP_KEY,
        cluster:      import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'eu',
        forceTLS:     true,
        authEndpoint: '/broadcasting/auth',
    })
}
```

Do NOT modify anything else in `app.js` — Alpine stores remain exactly as they are.

- [ ] **Step 2: Build and verify no JS errors**

```bash
npm run build
```

Expected: `✓ built in Xms` — no errors. (Echo will be `undefined` when VITE_PUSHER_APP_KEY is placeholder — that's intentional and guarded.)

- [ ] **Step 3: Commit**

```bash
git add resources/js/app.js
git commit -m "feat: initialise Laravel Echo + Pusher in frontend"
```

---

## Task 6: Customer Tracking — Live Status

**Files:**
- Modify: `resources/views/orders/tracking.blade.php`

- [ ] **Step 1: Update tracking view to subscribe to real-time updates**

Open `resources/views/orders/tracking.blade.php`. Find the `<div class="max-w-container...">` outer div and replace it with an Alpine.js component that subscribes to live updates:

Replace the outer wrapping div from:
```blade
<div class="max-w-container mx-auto px-4 lg:px-16 py-12">
```

To:
```blade
<div class="max-w-container mx-auto px-4 lg:px-16 py-12"
     x-data="{
       currentStatus: '{{ $order->status }}',
       statusLabel:   '{{ $order->status_label }}',
       statusColor:   '{{ $order->status_color }}',
       init() {
         if (window.Echo) {
           window.Echo.private('order.{{ $order->id }}')
             .listen('.OrderStatusUpdated', (data) => {
               this.currentStatus = data.status
               this.statusLabel   = data.status_label
               this.statusColor   = data.status_color
             })
         }
       }
     }">
```

Then update the **current status card** inside the view. Find the static status display:
```blade
<p class="font-serif text-2xl font-black {{ $order->status_color }}">{{ $order->status_label }}</p>
```

Replace with the reactive Alpine.js version:
```blade
<p class="font-serif text-2xl font-black" :class="statusColor" x-text="statusLabel"></p>
```

Also update the status stepper to use `currentStatus` instead of `$order->status` for the done/active checks. The PHP `@php` block calculates `$currentIndex` from `$order->status` on page load — that's fine for initial render. Only the status card text updates live. Add a live connection indicator below the status card:

```blade
{{-- Live connection indicator --}}
@if($order->user_id === auth()->id())
<p class="font-mono text-[10px] text-on-surface-variant mt-3 text-center" x-show="window.Echo !== undefined">
  <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-1"></span>
  Live updates active
</p>
@endif
```

- [ ] **Step 2: Build**

```bash
npm run build
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/orders/tracking.blade.php
git commit -m "feat: customer order tracking listens for live status updates"
```

---

## Task 7: Admin Kitchen + Orders — Live Queue

**Files:**
- Modify: `resources/views/admin/orders/index.blade.php`
- Modify: `resources/views/admin/kitchen/index.blade.php`

- [ ] **Step 1: Add live update notification to admin orders index**

Open `resources/views/admin/orders/index.blade.php`. Find `@section('content')` and add a toast notification component immediately after it:

```blade
{{-- Live order updates toast --}}
<div x-data="{
       show: false,
       message: '',
       notify(msg) { this.message = msg; this.show = true; setTimeout(() => this.show = false, 4000) },
       init() {
         if (window.Echo) {
           window.Echo.private('admin.orders')
             .listen('.OrderStatusUpdated', (data) => {
               this.notify('Order #' + data.order_id + ' → ' + data.status_label)
             })
         }
       }
     }"
     x-show="show"
     x-cloak
     x-transition
     class="fixed bottom-6 right-6 z-50 bg-on-surface text-surface px-6 py-3 font-mono text-xs font-bold uppercase shadow-xl">
  <span x-text="message"></span>
</div>
```

Also update the **LIVE STATUS: ACTIVE** badge area. Find the stats bar KPI divs and add a live indicator next to the page heading:

```blade
{{-- After the main h1/heading, add: --}}
<span x-data x-show="window.Echo !== undefined" x-cloak
      class="font-mono text-[10px] bg-green-100 text-green-800 px-2 py-1 border border-green-300 uppercase tracking-widest">
  ● LIVE
</span>
```

- [ ] **Step 2: Add live update listener to kitchen command page**

Open `resources/views/admin/kitchen/index.blade.php`. Find the `<main` content area. Add the Echo subscription at the bottom of the page content (before `@endsection`):

```blade
{{-- Live kitchen updates --}}
<div x-data="{
       newOrders: 0,
       init() {
         if (window.Echo) {
           window.Echo.private('admin.orders')
             .listen('.OrderStatusUpdated', (data) => {
               // Flash the relevant column header
               const badge = document.getElementById('status-badge-' + data.status)
               if (badge) {
                 badge.classList.add('ring-2', 'ring-yellow-400')
                 setTimeout(() => badge.classList.remove('ring-2', 'ring-yellow-400'), 2000)
               }
             })
         }
       }
     }" x-init="init()">
</div>
```

Add `id` attributes to the column count badges in the kanban board so the live updater can target them. Find the span elements with the column counts (e.g., `<span class="bg-outline text-on-primary px-3 py-1 text-label-bold rounded-full">3</span>`) and add IDs:

- Order Queue badge: `id="status-badge-pending_payment"`
- In the Kitchen badge: `id="status-badge-cooking"`
- Ready for Dispatch badge: `id="status-badge-ready"`
- Out for Delivery badge: `id="status-badge-out_for_delivery"`

- [ ] **Step 3: Build**

```bash
npm run build
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/orders/index.blade.php resources/views/admin/kitchen/index.blade.php
git commit -m "feat: admin orders and kitchen listen for live Pusher updates"
```

---

## Task 8: Full Test Suite + Final Build

**Files:** none new

- [ ] **Step 1: Run full test suite**

```bash
& "C:\xampp\php\php.exe" artisan test
```

Expected: all tests pass (67 from Plan 5 + 5 new broadcasting = 72+ total).

If any test fails, fix before proceeding.

- [ ] **Step 2: Clear compiled views and config**

```bash
& "C:\xampp\php\php.exe" artisan config:clear
& "C:\xampp\php\php.exe" artisan view:clear
```

- [ ] **Step 3: Production build**

```bash
npm run build
```

Expected: `✓ built in Xms` — no errors.

- [ ] **Step 4: Verify broadcasting route registered**

```bash
& "C:\xampp\php\php.exe" artisan route:list --path=broadcasting
```

Expected: `POST broadcasting/auth` appears.

- [ ] **Step 5: Final commit**

```bash
git add .
git commit -m "feat: Plan 6 complete — real-time Pusher broadcasting, live order tracking, admin kitchen updates"
```

---

## Activating Real-Time (when Pusher credentials available)

1. Get credentials from https://pusher.com → New App → copy App ID, Key, Secret, Cluster
2. Open `C:\AcesAndEightsPizza\webapp\.env` and set:
   ```ini
   BROADCAST_CONNECTION=pusher
   PUSHER_APP_ID=your_actual_id
   PUSHER_APP_KEY=your_actual_key
   PUSHER_APP_SECRET=your_actual_secret
   PUSHER_APP_CLUSTER=eu
   VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
   VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
   ```
3. Run `npm run build` to rebuild with real VITE vars
4. Restart artisan serve
5. Open two browser windows — admin updates status, customer tracking page updates instantly
