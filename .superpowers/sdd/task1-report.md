# Task 1 Report — OrderController Feature Tests

**Status:** DONE

**Commit hash:** 39c6aae

**Test count before:** 237 passed  
**Test count after:** 244 passed (7 new tests, 0 regressions)

## Tests written

| # | Method | Result |
|---|--------|--------|
| 1 | `test_confirmation_shows_order_details` | PASS |
| 2 | `test_confirmation_returns_404_for_unknown_order` | PASS |
| 3 | `test_confirmation_returns_403_for_wrong_user` | PASS |
| 4 | `test_confirmation_allows_guest_order_without_auth` | PASS |
| 5 | `test_tracking_shows_order_details` | PASS |
| 6 | `test_tracking_returns_403_for_wrong_user` | PASS |
| 7 | `test_tracking_allows_guest_order_without_auth` | PASS |

## Concerns

None. All 7 tests passed on first run without requiring any changes to production code — the controller behaviour was already correct. The existing `CustomerPagesTest` had 3 overlapping smoke tests (200 for confirmation, 200 for tracking, 403 for wrong user on tracking) but these new tests are more targeted, use `assertViewHas`, and cover the guest-order paths that were untested.
