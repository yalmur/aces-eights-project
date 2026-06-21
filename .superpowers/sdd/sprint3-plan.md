# Sprint 3 Plan — Settings, DeliveryFee, MenuShow

## Context
Laravel 12 pizza webapp. 254 tests passing (e8687d4).
App: C:\AcesAndEightsPizza\webapp. Tests: `php artisan test`.

## Global Constraints
- Laravel 12 / PHP 8.2
- RefreshDatabase on all DB-touching tests
- No mocks unless controller injects mockable dependency
- Test real behaviour — no framework internals
- TDD: failing test first, minimal production code, green, refactor
- Commit after each task

---

## Task 1 — Admin SettingsController tests

**File:** `tests/Feature/Admin/SettingsControllerTest.php`
**Controller:** `app/Http/Controllers/Admin/SettingsController.php`

Routes:
- `GET  /admin/settings` → `admin.settings.index`
- `POST /admin/settings` → `admin.settings.update` (with `@method('POST')`)

Controller behaviour:
- `index()`: reads 11 settings via `Setting::get(key, default)`, passes to view
- `update()`: validates all 11 fields, calls `Setting::setMany($data)`, redirects with flash success

Read `app/Models/Setting.php` before writing — understand `get()`, `setMany()`.

**Tests (TDD order):**
1. `settings_page_requires_admin` — unauthenticated → redirect to login
2. `settings_page_returns_200_for_admin` — admin user → 200
3. `settings_page_blocked_for_customer` — customer role → 403
4. `settings_update_persists_values` — POST valid data → DB has updated setting, redirect to settings index
5. `settings_update_flashes_success_message` — POST valid data → session has 'success'
6. `settings_update_validates_required_fields` — POST with store_name missing → redirect back with errors
7. `settings_update_validates_email_field` — POST with invalid store_email → errors

Valid POST payload (use these exact field names):
```php
[
    'store_name'              => 'Test Pizza',
    'store_address'           => '1 Test St',
    'store_phone'             => '01234 567890',
    'store_email'             => 'test@example.com',
    'opening_sun_thu'         => '16:00 – 22:45',
    'opening_fri_sat'         => '16:00 – 23:15',
    'hero_text'               => 'Great pizza',
    'story_text'              => 'Our story',
    'size_large_extra'        => '4.00',
    'crust_gluten_free_extra' => '2.00',
    'crust_cauliflower_extra' => '2.50',
]
```

Admin user factory: `User::factory()->create(['role' => 'admin'])`.
Customer user factory: `User::factory()->create(['role' => 'customer'])`.

**Commit:** `test: add AdminSettingsController tests`

---

## Task 2 — CheckoutController deliveryFee tests

**File:** `tests/Feature/DeliveryFeeTest.php`
**Route:** `GET /delivery-fee?postcode=XXX` → JSON

Controller method (already read):
```php
public function deliveryFee(Request $request): JsonResponse
{
    $postcode = trim($request->query('postcode', ''));
    if (!$postcode) {
        return response()->json(['covered' => false, 'fee' => 0, 'zone' => null, 'message' => 'Enter your postcode']);
    }
    $zone = DeliveryZone::findByPostcode($postcode);
    if (!$zone) {
        return response()->json(['covered' => false, ...]);
    }
    return response()->json(['covered' => true, 'fee' => (float) $zone->fee, 'zone' => $zone->name, ...]);
}
```

Read `app/Models/DeliveryZone.php` — understand `findByPostcode()` (matches postcode against zone's `postcodes` CSV field).

**Tests (TDD order):**
1. `returns_not_covered_when_no_postcode_given` — GET /delivery-fee (no query param) → JSON `{covered: false, message: 'Enter your postcode'}`
2. `returns_not_covered_for_unknown_postcode` — zone with 'NW5,N7', query postcode='SW1' → JSON `{covered: false}`
3. `returns_covered_with_fee_for_known_postcode` — create zone (postcodes='NW5,N7', fee=2.50, name='Zone A'), query postcode='NW5' → JSON `{covered: true, fee: 2.5, zone: 'Zone A'}`
4. `postcode_lookup_is_case_insensitive` — create zone with 'NW5', query 'nw5' → `{covered: true}` (if findByPostcode normalises) OR test confirms case sensitivity — check actual model behaviour first
5. `returns_zero_fee_in_not_covered_response` — unknown postcode → JSON has `fee: 0`

**Commit:** `test: add delivery fee endpoint tests`

---

## Task 3 — MenuController@show tests

**File:** `tests/Feature/MenuShowTest.php`
**Route:** `GET /menu/{slug}`
**Controller:** already read above.

Behaviour:
- Loads MenuItem by slug WHERE is_available=true → firstOrFail() (404 if missing or unavailable)
- Loads related items (same category, different id, available, random, limit 4)
- Returns view with `title`, `item`, `related`

**Tests (TDD order):**
1. `menu_show_returns_200_for_available_item` — create category + available menu item → GET /menu/{slug} → 200
2. `menu_show_returns_404_for_unknown_slug` — GET /menu/nonexistent → 404
3. `menu_show_returns_404_for_unavailable_item` — item with is_available=false → 404
4. `menu_show_sets_correct_page_title` — response assertSee($item->name)
5. `menu_show_loads_related_items_from_same_category` — create 3 items in same category, 1 in different → view has at least 1 related item (not the current item)

Use `MenuItem::factory()->create(['slug' => 'test-pizza', 'is_available' => true])`.
Check `database/factories/MenuItemFactory.php` and `database/factories/CategoryFactory.php` before writing.

**Commit:** `test: add MenuController show page tests`

---

## Success criteria
All 3 tasks: `php artisan test` passes with ≥ 271 tests (currently 254 + 7 + 5 + 5 = ~266 min). No regression.
