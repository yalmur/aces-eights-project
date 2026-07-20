# Kitchen Accept/Reject with Payment Authorization Hold

**Date:** 2026-07-20
**Status:** Approved, pending implementation plan

## Problem

The Kitchen dashboard's "Fire" button moves an order from `accepted` to `cooking` with no way to decline it. Payment is captured immediately at checkout, so there's nothing left to "return" if the kitchen can't fulfil an order (out of stock, closing, etc.) — a decline today would require a separate manual refund with no built-in flow, and no customer notification exists for it.

## Goal

Replace "Fire" with **Accept** / **Reject**:
- **Accept** — capture the customer's payment, move the order into the cooking pipeline (what "Fire" does today, plus the capture step).
- **Reject** — release the customer's payment hold (or refund it, in the rarer already-captured case), cancel the order, and email the customer: "Sorry, we're unable to accept your order — please try again another time."

For this to mean anything, payment must be *authorized* (held) at checkout, not charged immediately. Charging then refunding on reject was considered and rejected — it's the same end effect but the customer sees a real charge-then-refund on their statement, and refunds can take days to clear. Authorization-hold is the standard pattern for exactly this "pending approval" scenario.

## Scope

Applies to every order that reaches the `accepted` status, regardless of origin:
- **Online orders** (delivery/collection via Stripe Checkout) — real payment hold to capture or void.
- **In-store/walk-in orders** (`Admin/OrderController::storeInStore`) — no card on file (staff already took payment in person), so Accept/Reject here is purely a status transition with no Stripe call. Existing in-store buttons already do this (relabeled for consistency, not rebuilt).

Does not touch Party Hall booking or Deals — neither creates a Stripe Checkout Session independently of `CheckoutController`; only the standard delivery/collection/eat-in order flow is affected.

## Non-goals

- No reason-capture UI for Reject (one-click, fixed message — confirmed with user).
- No change to the generic `OrderStatusUpdate` email for later-stage transitions (ready/dispatched/delivered/etc.) — only the specific `accepted → cancelled` transition gets the new rejection email.
- No handling for authorization-hold expiry (card networks typically hold 7+ days; same-day kitchen turnaround makes this a non-issue for this cycle).

## Design

### 1. Payment authorization at checkout

`CheckoutController::store()` — add `payment_intent_data.capture_method = 'manual'` to the Stripe Checkout Session creation params (`mode: 'payment'` already supports this). Nothing else about session creation changes.

### 2. Existing webhook — verify unaffected, no code change

`StripeWebhookController::handle()` listens for `checkout.session.completed`, which fires as soon as checkout succeeds (card authorized), independent of capture mode. It already just records `stripe_payment_intent_id` and flips `pending_payment → accepted`. Confirmed no change needed here.

### 3. Correction to the same-day confirmation-page fallback

`OrderController::reconcileWithStripe()` (added earlier today, before this feature) currently gates on `$session->payment_status === 'paid'`. Under manual capture, Stripe's Checkout Session `payment_status` stays `'unpaid'` until the PaymentIntent is actually captured — it will never read `'paid'` at this stage anymore. Must change the gate to `$session->status === 'complete'` (checkout succeeded / card authorized), matching what the webhook effectively checks via event type.

### 4. New column: `orders.payment_captured_at` (nullable timestamp)

Source of truth for whether *this* order's payment has actually been captured (vs. merely authorized). Used to:
- Prevent double-capture on a double-click / concurrent request (guard: only capture if this is still null and DB status is still `accepted` at the moment of the update).
- Decide void (`cancel()`) vs. refund (`refund()`) when an order is cancelled.

### 5. Centralized capture/void logic in `Admin/OrderController::updateStatus()`

This is the one route every UI already goes through (Kitchen dashboard cards, order detail page, in-store view), so payment side-effects live here, not duplicated per view:

- **`accepted → cooking` ("Accept")**: if `stripe_payment_intent_id` is set and `payment_captured_at` is null, call `$stripe->paymentIntents->capture($id)`.
  - Success: set `payment_captured_at = now()`, proceed with the status update as normal.
  - Failure (e.g. Stripe declines capture — can happen even after successful authorization): **do not** advance the order. Return to the admin with an error; order stays `accepted` so kitchen can retry or reject.
  - No payment intent (in-store order): just proceed with the status transition, no Stripe call.
- **`* → cancelled`**: if a payment intent exists and `payment_captured_at` is null → `cancel()` it (void the hold, no money ever moved). If `payment_captured_at` is set (a later-stage cancellation, not the kitchen-reject path) → `refund()` instead.
  - The new "sorry, unable to accept" email (`OrderRejected` mailable) fires **only** for the `accepted → cancelled` transition specifically — that's the true "kitchen reject" case the user asked for. A cancellation from a later stage (rare, generic admin action, not this feature's target) keeps using the existing `OrderStatusUpdate` email — sending "unable to accept" would be factually wrong once the kitchen had already accepted and started cooking.
  - No payment intent (in-store order): just cancel the order, skip all Stripe calls; still send whichever email applies if `customer_email` is present, matching existing behavior.
- Guard every Stripe call in try/catch; log and surface a clear admin-facing error on failure rather than silently swallowing it.

### 6. New Mailable: `App\Mail\OrderRejected`

Small dedicated Mailable + blade view (mirrors `OrderConfirmation`'s structure): "Sorry, we're unable to accept your order today. Please try again another time." plus the order reference and a note that no payment was taken (or that it's been refunded, in the already-captured case).

### 7. UI changes

- **`resources/views/admin/kitchen/_card.blade.php`**: where the "Fire" button appears today (`$column === 'preparing' && $order->status === 'accepted'`), replace with two buttons:
  - **Accept** → `POST admin.orders.status` with `status=cooking`.
  - **Reject** → same route with `status=cancelled`, `onsubmit="return confirm(...)"` (matching the existing confirm pattern already used in the in-store cancel button).
- **`resources/views/admin/orders/detail.blade.php`**: while `$order->status === 'accepted'`, replace the free-form status `<select>` + "Update Status" button with the same Accept/Reject button pair. For any other status, the dropdown remains as-is (no payment implications past this point).
- **`resources/views/admin/orders/in-store.blade.php`**: the existing `accepted → cooking` "→ Cooking" button relabels to "Accept" specifically for that transition (later-stage buttons like "→ Ready"/"→ Collected" keep their current wording). The existing cancel button (already has a `confirm()`) effectively serves as "Reject" at this stage — no new button needed, just consistent framing. No Stripe calls fire for these since `stripe_payment_intent_id` is never set on in-store orders.

### 8. Customer-facing wording

`Order::getStatusLabelAttribute()` — the `accepted` case relabels from "Accepted" to **"Order Received"** for the confirmation/tracking pages, since at this stage the kitchen hasn't actually decided yet — "Accepted" language is reserved for the moment *after* the kitchen clicks Accept (once the order is `cooking`). Implementation should also check `emails/order-confirmation.blade.php`, `orders/confirmation.blade.php`, and `orders/tracking.blade.php` for any hardcoded "Accepted"/"confirmed" copy beyond the accessor.

## Data flow summary

```
Checkout (manual capture hold)
  → pending_payment
  → [webhook or confirmation-page fallback] → accepted (authorized, in kitchen queue)
      → Kitchen: Accept  → capture()  → cooking → ... → delivered/collected
      → Kitchen: Reject  → cancel()/refund() → cancelled → customer emailed "sorry"
```

## Testing

Feature tests to add (Laravel `tests/Feature`):
- Checkout session creation includes `payment_intent_data.capture_method = manual`.
- Webhook still flips `pending_payment → accepted` and records the payment intent under manual capture (unaffected by this change, but assert to lock it in).
- Confirmation-page fallback correctly reconciles based on `session.status === 'complete'` rather than `payment_status`.
- Accept (`accepted → cooking`): captures payment (mock Stripe client), sets `payment_captured_at`, advances status. Capture failure leaves status unchanged and surfaces an error.
- Reject (`accepted → cancelled`): voids the hold (not a refund) when uncaptured, sends `OrderRejected` email, order excluded from revenue totals (already covered by existing `whereNotIn` filters — assert it stays that way).
- Later-stage cancellation (already captured): issues a refund instead of a void, sends the existing generic status email, not `OrderRejected`.
- In-store orders: Accept/Reject perform no Stripe calls and behave exactly as today.
- Double-click / concurrent Accept guard: second attempt is a no-op, doesn't double-capture.
