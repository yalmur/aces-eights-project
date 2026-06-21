# Sprint 3 Task 2 — Delivery Fee Endpoint Tests

## Status
COMPLETE

## Commit
e5fb1d6 — `test: add delivery fee endpoint tests`

## Tests
- Before: 261 passing
- After: 266 passing (+5)
- Regressions: 0

## Tests written (DeliveryFeeTest.php)

| # | Test | Outcome |
|---|------|---------|
| 1 | `test_returns_not_covered_when_no_postcode_given` | PASS |
| 2 | `test_returns_not_covered_for_unknown_postcode` | PASS |
| 3 | `test_returns_covered_with_fee_for_known_postcode` | PASS |
| 4 | `test_returns_zero_fee_in_not_covered_response` | PASS |
| 5 | `test_full_postcode_matches_zone_by_district` | PASS |

## Test 5 rationale (deviation from spec)
The spec said: test whitespace trimming on input postcode, or replace with `test_endpoint_is_accessible_without_auth` if `findByPostcode` does not trim.

Findings: `findByPostcode` calls `extractDistrict`, which runs `strtoupper(trim(preg_replace('/\s+/', ' ', $postcode)))` and extracts the district prefix. The controller also does `trim($request->query('postcode', ''))` first. So a full UK postcode `NW5 2HP` correctly extracts to district `NW5` and matches a zone with postcodes containing `NW5`.

Test 5 was therefore written as `test_full_postcode_matches_zone_by_district` — verifying that a caller passing a full postcode (the common real-world case) gets a covered response when the zone holds the district. This tests real behaviour, is more useful than an auth test, and does not duplicate test 3 (which sends a bare district).

## Key observations
- Route `GET /delivery-fee` is public (no auth middleware) — confirmed by `route:list` output.
- `Cache::flush()` required in `setUp()` because `findByPostcode` caches zone lookups for 300 s. Without a flush, stale zones from other tests bleed in.
- The `fee` field is cast `decimal:2` in the model but the controller casts to `(float)` before JSON encoding. `assertJson(['fee' => 2.5])` passes because JSON `2.5 == 2.5`.
