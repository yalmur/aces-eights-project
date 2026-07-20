---
target: menu
total_score: 28
p0_count: 0
p1_count: 2
timestamp: 2026-07-20T11-49-33Z
slug: resources-views-menu-index-blade-php
---
Method: dual-agent (A: critique-assessment-a · B: critique-assessment-b)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 3 | Active tab/search/allergen badge are clear; empty search shows no "0 results" status |
| 2 | Match System / Real World | 3 | Plain menu language, real photography; truncated allergen codes ("GLUTE","CELER") aren't real words |
| 3 | User Control and Freedom | 3 | Esc closes drawer, per-filter clears exist; no single "reset all filters" |
| 4 | Consistency and Standards | 3 | Registers held consistently, but `shadow-sm` breaks the project's own no-blur rule and off-palette greens appear |
| 5 | Error Prevention | 3 | Allergen-exclusion filter is a genuine preventive feature; failed search isn't guarded |
| 6 | Recognition Rather Than Recall | 3 | Filters labeled and visible; category heading disappears when filtered; allergen codes truncated to 5 chars force guessing |
| 7 | Flexibility and Efficiency | 3 | Search + diet toggles + 3 view modes suit regulars; no favorites/"reorder my usual" |
| 8 | Aesthetic and Minimalist Design | 3 | Clean masthead; filter bar packs 7 controls into one row, 3 view modes arguably surplus |
| 9 | Error Recovery | 2 | The one error state (empty search) has no message and, worse, still renders the Deals section beneath it |
| 10 | Help and Documentation | 2 | No legend for truncated allergen codes, no contextual help on filters |
| **Total** | | **28/40** | **Good — solid, on-brand foundation with specific, fixable gaps** |

## Anti-Patterns Verdict

**Not AI slop.** This passes the product-register slop test: earned familiarity, not generic strangeness. The serif masthead between two gradient hairline rules with a `4px double` border, the two-register button split (oxblood stamped square for "add," gold glossy gradient for aspirational CTAs), and the mono/uppercase Ledger Label discipline on prices/tags are DESIGN.md's committed system genuinely showing up on the page — a template wouldn't make these specific choices. Category-reflex check: first-order "pizza → red+gold" is the obvious pizzeria reflex, but the specific oxblood (not tomato-red) plus letterpress/stamp execution pushes past it, and it clearly isn't the generic Uber-Eats-marketplace look PRODUCT.md names as the anti-reference.

**Where it slips**: the tells that leak through are all at the generic-Tailwind-default layer, not the committed system — `shadow-sm` (soft blur) on cards directly contradicts DESIGN.md's own Stamped Ledger Rule ("no soft/blurred box-shadow anywhere except one named exception"), and off-palette `green-700`/`green-100` diet badges introduce a third accent color absent from the committed oxblood/gold/parchment system.

**Deterministic scan** (CLI, `detect.mjs --json` against the target file — exit code 2, findings present):

| Rule | Count | Verdict |
|------|-------|---------|
| `broken-image` | 1 (blade line 208) | **True positive** — items without an uploaded photo fall back to a generic placehold.co box in both dev and production, not dev-only as first assumed |

Note: a directory-level rerun of the same detector silently dropped this finding (exit 0) — the single-file scan against the assigned target is the authoritative result here; worth knowing the directory-mode scan mode is less reliable.

**Browser overlay** (live injection, same page, runtime DOM scan — 108 findings across 28 elements):

| Overlay rule | Count | Synthesis |
|---|---|---|
| `image-hover-transform` | 79 | One repeated design decision (`group-hover:scale-105` per menu photo), not 79 distinct problems — but it's the runtime fingerprint of Assessment A's own P2 finding: no `prefers-reduced-motion` support anywhere in the stylesheet despite this transform running everywhere |
| `all-caps-body` | 13 | Mostly **false positive** against this project — this is the deliberate Ledger Label Rule (mono/uppercase for prices, tags, category labels), not generic AI-slop caps. Worth a spot-check that none of the 13 are actual prose rather than metadata |
| `nested-cards` | 10 | Likely false positive — deal cards contain bordered chips, not literal nested cards |
| `skipped-heading` | 1 | **Real, and both assessments independently agree**: the customization drawer jumps `<h2>` → `<h4>` ("Choose Size"), skipping `<h3>` — an a11y/screen-reader issue |
| `tiny-text` | 3 | Real — 9-10px text somewhere on the page, below comfortable legibility for a brand built on "trustworthy, no surprises" |
| `cramped-padding` | 1 | Real, and matches Assessment A's own cognitive-load finding: the filter bar packs 7 controls into one row |
| `layout-transition` | 1 | Real, minor — a `width` transition (animates a layout property, causes reflow) |

## Overall Impression

A genuinely committed, on-brand system is visible on this page — the masthead alone earns it. But the two biggest problems both sit at a safety/trust moment the brand explicitly stakes its identity on: allergen codes truncated to 5 characters (`GLUTE`, `CELER`, `MUSTA`), and a failed search that doesn't just dead-end quietly — it leaves the Deals section rendering underneath a "showing results for 'x'" header, making a broken search look like it "returned" unrelated deals. Neither is expensive to fix; both directly undercut "trustworthy, reliable, no surprises at checkout," which is PRODUCT.md's stated #1 personality trait.

## What's Working

1. **The masthead as a printed-menu artifact** — serif wordmark between gradient hairline rules plus a `4px double` border-bottom. The single strongest expression of "The Gilded Furnace" on the page, and the main reason this reads as one shop's own menu rather than a template.
2. **The two-register button system is correctly implemented, not just documented** — oxblood stamped square for the transactional "add," gold glossy gradient reserved for aspirational CTAs. DESIGN.md's Furnace Ratio intent is visibly real here.
3. **Progressive disclosure done right** — the allergen-exclusion panel and full customization drawer are both revealed on demand rather than dumped up front, and the allergen filter itself doubles as a real error-prevention feature on a food-safety surface. Measured label contrast: 7.6–8.9:1, well past AA.

## Priority Issues

**[P1] Allergen tags truncated to 5 characters.** `strtoupper(substr($allergen->name, 0, 5))` at blade line 227 produces `GLUTE`, `CELER`, `MUSTA`, `SESAM`. **Why it matters**: allergens are safety-critical, and the brand's #1 stated personality trait is trustworthy/no-surprises — an ambiguous fragment at exactly this moment is the highest-cost place on the page to be unclear. **Fix**: show the full allergen name, or a curated unambiguous code map (`GLU`, `MLK`, `EGG`) — never a blind `substr`. **Suggested command**: `/impeccable clarify`

**[P1] Failed search dead-ends, and the Deals section renders underneath it.** Verified directly: searching `zzqqxx` shows a "showing results for" header, an empty void, then the full Deals grid with no gate. **Why it matters**: there's no confirmation the search even ran, and the unrelated Deals section reads as if it were the (wrong) result — actively misleading, not just unhelpful. **Fix**: add a real empty state ("No dishes match 'x'…") and hide or clearly relabel Deals while a search is active. **Suggested command**: `/impeccable harden`

**[P2] Cards use Tailwind's `shadow-sm`, violating the project's own Stamped Ledger Rule.** DESIGN.md is explicit: no soft/blurred `box-shadow` anywhere except one named exception (the auth sign-in glow). Menu item cards and deal cards both use a generic soft shadow instead. **Fix**: drop `shadow-sm`; the 1px ledger border already carries the surface, and if hover-lift is wanted, use the documented `2px 2px 0 #1b1c1c` stamp instead. **Suggested command**: `/impeccable polish`

**[P2] Off-palette greens for diet badges/toggles.** `bg-green-700`/`green-800`/`green-100` introduce a third accent color family with no home in DESIGN.md's committed oxblood/gold/parchment system. **Fix**: express veg/vegan within the existing palette (e.g. an oxblood-outline or gold-bordered chip variant), or deliberately add one green token to DESIGN.md if a dedicated diet-signal color is genuinely wanted. **Suggested command**: `/impeccable colorize`

**[P2] No `prefers-reduced-motion` support anywhere, despite motion running on every menu photo.** PRODUCT.md commits to standard WCAG AA, which includes reduced-motion support; the detector's 79-count `image-hover-transform` finding is the runtime footprint of this gap (`group-hover:scale-105`, `duration-500`, applied to every item photo with no fallback). **Fix**: a global `@media (prefers-reduced-motion: reduce)` block disabling transforms/transitions. **Suggested command**: `/impeccable harden`

**[P2] Two genuinely tiny (9-10px) text instances, caught by the deterministic scan.** Below comfortable legibility, on a brand whose whole trust proposition depends on clarity. **Fix**: bring these up to the DESIGN.md label scale (12px/600 minimum) rather than an ad-hoc smaller size. **Suggested command**: `/impeccable typeset`

## Persona Red Flags

**Casey (distracted mobile)**: all 71 items render on one page with no lazy-load/pagination — heavy on a slow connection. The 3 view-toggle icons take a full row on mobile at ~32px (`p-2`), under the 44×44pt touch target minimum. Filtering to one category hides that category's own heading, so a returning user loses their "where am I" anchor. The add-to-cart `+` button is a correctly-sized 48px in the thumb zone.

**Riley (stress tester)**: confirmed the no-match search leaves Deals rendering underneath a "results for" header — reads as broken or misleading, not just empty. The "Vegan Pizza" card shows only a `GLUTE` allergen tag and no Vegan badge — a name-vs-badge data inconsistency (likely a missing `is_vegan` flag on that item) that a careful user will notice and distrust. Combining diet + category + allergen filters can silently empty a category with zero feedback.

**Jordan (first-timer)**: the truncated allergen codes have no legend or tooltip anywhere on the page — a first-time visitor with a real allergy has no way to resolve `MUSTA` without guessing. The 3 view-toggle icons are unlabeled beyond a hover `title`, which a first-timer won't discover. Filtering to a category removes that category's own name from view, losing the "you are here" signal exactly when a newcomer needs it most.

**Marcus — Local Regular** (project-specific persona, from PRODUCT.md's "local regulars, mobile, fast repeat orders" audience): the speed tools that exist (search, diet toggles, category filmstrip) genuinely serve him. But there's no favorites list or "reorder my usual," so even a customer who orders the same thing every week rebuilds the entire order from scratch each visit — directly undercutting PRODUCT.md's stated goal that regulars should default to the site over the phone. On mobile, reaching Drinks/Desserts requires horizontal-scrolling the category filmstrip.

## Minor Observations

- The category grid's own section heading is hidden by `x-show="active === 'all' || search"` whenever a single category is active — worth fixing alongside the persona findings above rather than as a separate task.
- The "AUTHENTIC ITALIAN TAKEAWAY" tracked-caps tagline in the masthead sits close to the banned "tiny uppercase eyebrow" pattern, but reads as sanctioned by the deliberate double-rule masthead motif rather than reflexive AI scaffolding — not flagged as a violation, just worth a second look if the masthead is ever revised.
- **Furnace Ratio tension**: DESIGN.md's own rule says gold is "the glow, not the fuel — one or two per screen," but the Deals grid alone renders 10 gold-gradient buttons at once. Worth resolving alongside the color work above.
- Items without a stored photo fall back to a generic placehold.co placeholder in both dev and production (deterministic-scan true positive) — low severity, but a real gap for any menu item missing an uploaded image.
- A `width` CSS transition animates a layout property (causes reflow) — minor performance nit, easy to swap for a `transform`-based equivalent.

## Questions to Consider

- If allergen clarity is core to the brand's trust promise, why is it the most truncated, lowest-emphasis element on the card — what would a card that treats allergen clarity as first-class actually look like?
- If gold is "the glow, not the fuel," what would the Deals section look like with exactly one hero deal in gold and the rest in oxblood — restoring the Furnace Ratio and creating real hierarchy among deals instead of six equally loud CTAs?
- For the regular who already knows the menu, the fastest path today is still scroll-and-hunt — what if the page opened straight to a one-tap "reorder your usual," collapsing the browse step for exactly the audience PRODUCT.md most wants to convert off the phone?
