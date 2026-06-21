# Sprint 2 Plan — Test Coverage + Security Hardening

## Context

Laravel 12 pizza webapp. PHP artisan test: 237 passing / 387 assertions (9a4f5e2).
App at C:\AcesAndEightsPizza\webapp. Run tests: `php artisan test`.

## Global Constraints

- Laravel 12 / PHP 8.2
- RefreshDatabase on all DB-touching tests
- No mocks unless controller injects mockable dependency
- Test only real behaviour — no testing framework internals
- Minimal production code changes; tests are the deliverable
- Each task: write failing test first, watch it fail, implement, verify green
- Commit after each task with `git commit`

## Task 1 — OrderController feature tests

**Files to create:** `tests/Feature/OrderControllerTest.php`

**Source under test:** `app/Http/Controllers/OrderController.php`

The controller has two methods:
```php
public function confirmation(Request $request, string $order): View
public function tracking(Request $request, string $order): View
```

Both:
- Load `Order::with('items')->findOrFail($order)` → 404 on missing
- If `$orderModel->user_id` is set AND doesn't match `$request->user()?->id` → abort(403)
- Return view with `title` and `order`

Routes (verify via `php artisan route:list | grep order`):
- `GET /orders/{order}/confirmation` → `orders.confirmation`
- `GET /orders/{order}/tracking` → `orders.tracking`

**Tests to write (TDD — fail first):**
1. `confirmation_shows_order_details` — authenticated user sees their order, status 200
2. `confirmation_returns_404_for_unknown_order` — random UUID → 404
3. `confirmation_returns_403_for_wrong_user` — order belongs to user A, user B gets 403
4. `confirmation_allows_guest_order_without_auth` — order has `user_id=null`, unauthenticated request → 200
5. `tracking_shows_order_details` — same as #1 but tracking route
6. `tracking_returns_403_for_wrong_user` — same ownership check
7. `tracking_allows_guest_order_without_auth` — `user_id=null` → 200

Use `Order::factory()->create()` and `Order::factory()->has(OrderItem::factory(), 'items')->create()`.
Check factories exist: `database/factories/OrderFactory.php`, `database/factories/OrderItemFactory.php`.

**Commit message:** `test: add OrderController feature tests (confirmation, tracking, auth)`

---

## Task 2 — PromoController API tests

**Files to create:** `tests/Feature/PromoControllerTest.php`

**Source under test:** `app/Http/Controllers/PromoController.php`

The `check` method:
- POST `/promo/check` (verify route name via `php artisan route:list | grep promo`)
- Validates: `code` (required, string, max 50), `subtotal` (required, numeric, min 0)
- Looks up `Promotion::where('code', strtoupper(trim($request->code)))`
- If not found or `!$promo->isValid($subtotal)` → JSON `{valid: false, message: ...}`
- Otherwise → JSON `{valid: true, code, label, discount, message}`

**Tests to write (TDD — fail first):**
1. `valid_fixed_promo_returns_discount` — active promo `SAVE5` with fixed £5 off, subtotal 20 → `valid:true`, `discount:5.00`
2. `valid_percentage_promo_returns_discount` — 10% promo, subtotal 50 → `valid:true`, `discount:5.00`
3. `invalid_code_returns_valid_false` — unknown code → `valid:false`
4. `expired_promo_returns_valid_false` — use Promotion factory's expired state → `valid:false`
5. `code_too_small_for_minimum_returns_valid_false` — promo has min_order, subtotal below it → `valid:false`
6. `validation_fails_without_code` — POST with no `code` → 422
7. `validation_fails_without_subtotal` — POST with no `subtotal` → 422
8. `code_is_case_insensitive` — send `save5` → same result as `SAVE5`

Inspect `app/Models/Promotion.php` and `database/factories/PromotionFactory.php` before writing to understand factory states and `isValid()` signature.

**Commit message:** `test: add PromoController API tests (valid/invalid/expired codes)`

---

## Task 3 — Security hardening + CartController test

### Part A: .env.example defaults

**File to edit:** `.env.example`

Change:
- `APP_DEBUG=true` → `APP_DEBUG=false`
- `SESSION_ENCRYPT=false` → `SESSION_ENCRYPT=true`

These are production-unsafe defaults committed to the repo.

### Part B: CartController test

**Files to create:** `tests/Feature/CartControllerTest.php`

**Source under test:** `app/Http/Controllers/CartController.php` — single `index()` returning `view('cart.index', ['title' => 'Your Order'])`.

Route: `GET /cart` (verify via route:list).

**Tests to write:**
1. `cart_page_is_accessible_to_guests` — unauthenticated GET /cart → 200
2. `cart_page_has_correct_title` — response contains "Your Order"

**Commit message:** `security: harden .env.example defaults (APP_DEBUG, SESSION_ENCRYPT); test: CartController`

---

## Success criteria

All 3 tasks: `php artisan test` passes with ≥ 250 tests total (currently 237). No pre-existing test may fail.
