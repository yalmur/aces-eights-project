# Task 2 Report — PromoController Feature Tests

## Status: DONE

## Commit
`2baa7b2` — "test: add PromoController API tests (valid/invalid/expired codes)"

## Test Count
Before: 244 | After: 252 (+8, no regressions)

## File Created
`tests/Feature/PromoControllerTest.php`

## Tests Written (all 8 passing)
1. `test_valid_fixed_promo_returns_discount` — fixed £5 promo, subtotal=20 → `{valid:true, discount:5.00}`
2. `test_valid_percentage_promo_returns_discount` — 10% promo, subtotal=50 → `{valid:true, discount:5.00}`
3. `test_invalid_code_returns_valid_false` — unknown code → `{valid:false}`
4. `test_expired_promo_returns_valid_false` — factory `expired()` state → `{valid:false}`
5. `test_subtotal_below_minimum_returns_valid_false` — min_order_amount=25, subtotal=10 → `{valid:false}`
6. `test_validation_fails_without_code` — no `code` field → 422 with `code` validation error
7. `test_validation_fails_without_subtotal` — no `subtotal` field → 422 with `subtotal` validation error
8. `test_code_lookup_is_case_insensitive` — lowercase `save5` → same as `SAVE5` → `{valid:true}`

## Notes
- No production code changes were needed — `PromoController::check()` already handled all scenarios correctly.
- `PromotionFactory` had all required states (`fixed()`, `percentage()`, `expired()`).
- The controller already normalises codes via `strtoupper(trim($request->code))`, making test 8 pass trivially.
- Existing `PromotionTest.php` covers overlapping scenarios (endpoint smoke tests) but at model/integration level. `PromoControllerTest.php` tests the HTTP API contract specifically with clean assertions.
