# SDD Plan — Session 2 (2026-06-21 PM)

## Project Context
Aces & Eights Pizza — Laravel 12/PHP 8.2 webapp at C:/AcesAndEightsPizza/webapp
Tests run: `cd C:/AcesAndEightsPizza/webapp && php artisan test`
Branch base: e4e0d47 (364 tests / 604 assertions)
SDD ledger: C:/AcesAndEightsPizza/webapp/.superpowers/sdd/progress.md

## Global Constraints
- TDD: write tests first, make them pass, no over-engineering
- Each task = one atomic commit after tests pass
- Follow existing test patterns in the codebase (PHPUnit, RefreshDatabase where DB needed)
- No DB for pure unit tests (use `make()` not `create()` or mock)
- YAGNI: only test what's specified, no extra assertions
- Tests must be in correct namespace: `Tests\Unit` or `Tests\Feature`
- No comments in test files unless non-obvious
- Commit message pattern: `test: <description>`

## Tasks

### Task 9: DeliveryZone postcode normalization edge-case tests
**File:** `tests/Feature/DeliveryZoneModelTest.php` (EXTEND — already has 17 tests)
**Goal:** Add normalization edge-case tests not yet covered.

**Tests to add (add at bottom of existing class):**

For `extractDistrict` normalization:
- `test_extract_district_handles_multiple_internal_spaces` — "NW5  2HP" → "NW5"
- `test_extract_district_handles_tab_whitespace` — "NW5\t2HP" → "NW5"
- `test_extract_district_is_idempotent_for_clean_input` — "NW5 2HP" already clean → "NW5"

For `getPostcodeListAttribute` edge cases:
- `test_postcode_list_filters_empty_segments_from_double_commas` — "NW5,,N7" → ["NW5", "N7"]
- `test_postcode_list_handles_single_district` — "NW5" → ["NW5"]

Total new tests: 5 (brings file from 17 → 22)
Commit: `test: add DeliveryZone normalization edge-case tests`

---

### Task 10: Topping model scopeAvailable unit tests
**File:** `tests/Unit/ToppingModelTest.php` (NEW)
**Goal:** Unit-test `Topping::scopeAvailable()` scope.

**Tests:**
- `test_scope_available_returns_only_available_toppings` — 2 available, 1 unavailable; scope returns 2
- `test_scope_available_orders_by_sort_order` — 3 available with sort_order 3,1,2; result order is 1,2,3
- `test_scope_available_returns_empty_when_all_unavailable` — all unavailable; scope returns empty
- `test_scope_available_excludes_unavailable_topping` — mixed; unavailable one absent from result

Total: 4 tests
Commit: `test: add Topping model scopeAvailable unit tests`

---

### Task 11: Category model availableItems relationship unit tests
**File:** `tests/Unit/CategoryModelTest.php` (NEW)
**Goal:** Unit-test `Category::availableItems()` and contrast with `menuItems()`.

**Tests:**
- `test_available_items_returns_only_available_menu_items` — category has 3 items (2 available, 1 not); availableItems returns 2
- `test_available_items_orders_by_sort_order` — items with sort_order 3,1,2; result order is 1,2,3
- `test_available_items_returns_empty_when_all_unavailable` — all unavailable; returns empty
- `test_menu_items_returns_all_items_regardless_of_availability` — 2 available + 1 unavailable; menuItems returns 3

Total: 4 tests
Commit: `test: add Category model availableItems relationship unit tests`

---

### Task 12: Admin MenuItemController slug auto-generation feature tests
**File:** `tests/Feature/Admin/MenuItemTest.php` (EXTEND — already has tests)
**Goal:** Verify slug is auto-generated from `name` on store and update.

Read existing MenuItemTest.php first to understand existing pattern and avoid duplication.

**Tests to add (append to existing class, ensure no duplication with existing slug-related test):**
- `test_store_auto_generates_slug_from_name` — POST store with name "Garlic Bread Deluxe"; assertDatabaseHas slug "garlic-bread-deluxe"
- `test_update_regenerates_slug_when_name_changes` — PUT update with new name "Spicy Garlic Bread"; assertDatabaseHas new slug "spicy-garlic-bread"
- `test_slug_uses_laravel_str_slug_format` — name "Triple-Cheese & Mushroom!" → slug "triple-cheese-mushroom" (special chars stripped, hyphens normalized)

Total new tests: 3
Commit: `test: add Admin MenuItemController slug auto-generation tests`
