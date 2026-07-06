<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\MenuItem;
use App\Models\MenuItemCrust;
use App\Models\MenuItemSize;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promotion;
use App\Models\Setting;
use App\Models\Topping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    private static function sizeExtrasForItem(MenuItem $item): array
    {
        $sizes = $item->sizes->where('is_available', true);
        if ($sizes->isEmpty()) {
            return [
                '12" Standard' => 0.0,
                '15" Large'    => (float) Setting::get('size_large_extra', '4.00'),
            ];
        }
        return $sizes->pluck('price_adjustment', 'name')->map(fn ($v) => (float) $v)->all();
    }

    private static function crustExtrasForItem(MenuItem $item): array
    {
        $crusts = $item->crusts->where('is_available', true);
        if ($crusts->isEmpty()) {
            return [
                '48hr Sourdough' => 0.0,
                'Gluten-Free'    => (float) Setting::get('crust_gluten_free_extra', '2.00'),
                'Cauliflower'    => (float) Setting::get('crust_cauliflower_extra', '2.50'),
            ];
        }
        return $crusts->pluck('price_adjustment', 'name')->map(fn ($v) => (float) $v)->all();
    }

    public function index(): View
    {
        $defaultAddress = Auth::user()->defaultAddress();
        $zones          = DeliveryZone::active()->get(['name', 'fee', 'postcodes']);

        return view('checkout.index', [
            'title'            => 'Checkout',
            'defaultAddress'   => $defaultAddress,
            'zones'            => $zones,
            'allergyEnabled'   => Setting::get('allergy_alerts_enabled', '1') === '1',
            'allergyDisclaimer'=> Setting::get('checkout_disclaimer', 'ACES & EIGHTS PIZZA CO. TAKES FOOD SAFETY SERIOUSLY. Our kitchen handles wheat, dairy, and eggs. Full allergen info available on request.'),
        ]);
    }

    public function deliveryFee(Request $request): JsonResponse
    {
        $postcode = trim($request->query('postcode', ''));
        if (!$postcode) {
            return response()->json(['covered' => false, 'fee' => 0, 'zone' => null, 'message' => 'Enter your postcode']);
        }

        $zone = DeliveryZone::findByPostcode($postcode);
        if (!$zone) {
            return response()->json(['covered' => false, 'fee' => 0, 'zone' => null, 'message' => "Sorry, we don't deliver to {$postcode}. We deliver within 2 miles of NW5 2HP."]);
        }

        return response()->json(['covered' => true, 'fee' => (float) $zone->fee, 'zone' => $zone->name, 'message' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_type'     => 'required|in:delivery,collection,eat_in',
            'cart_items'     => ['required', 'json', function ($attr, $val, $fail) {
                $items = json_decode($val, true);
                if (empty($items)) $fail('Your cart is empty.');
            }],
            'street_address' => 'required_if:order_type,delivery|nullable|string|max:255',
            'city'           => 'required_if:order_type,delivery|nullable|string|max:100',
            'postal_code'    => 'required_if:order_type,delivery|nullable|string|max:20',
            'promo_code'     => 'nullable|string|max:50',
            'notes'          => 'nullable|string|max:500',
        ]);

        $cartItems  = json_decode($data['cart_items'], true);
        $isDelivery = $data['order_type'] === 'delivery';

        if ($isDelivery) {
            $zone = DeliveryZone::findByPostcode($data['postal_code'] ?? '');
            if (!$zone) {
                return back()->withInput()->withErrors([
                    'postal_code' => "Sorry, we don't deliver to that postcode. We cover NW5, N7, N19 and nearby areas within 2 miles of Tufnell Park.",
                ]);
            }
            $deliveryFee = (float) $zone->fee;
        } else {
            $deliveryFee = 0;
        }
        $user        = Auth::user();

        $slugs         = array_column($cartItems, 'id');
        $menuItems     = MenuItem::with(['sizes', 'crusts'])->whereIn('slug', $slugs)->where('is_available', true)->get()->keyBy('slug');
        $toppingNames  = collect($cartItems)->flatMap(fn($ci) => array_column($ci['toppings'] ?? [], 'name'))->unique()->values();
        $toppingPrices = $toppingNames->isNotEmpty()
            ? Topping::whereIn('name', $toppingNames)->where('is_available', true)->get()->pluck('price', 'name')
            : collect();

        $orderItems = [];
        $subtotal   = 0;

        foreach ($cartItems as $ci) {
            $menuItem = $menuItems[$ci['id'] ?? ''] ?? null;
            if (!$menuItem) {
                return back()->withInput()->withErrors(['cart_items' => 'One or more items are unavailable.']);
            }

            $qty        = min(20, max(1, (int) ($ci['qty'] ?? 1)));
            $sizeExtra  = self::sizeExtrasForItem($menuItem)[$ci['size'] ?? '']  ?? 0.0;
            $crustExtra = self::crustExtrasForItem($menuItem)[$ci['crust'] ?? ''] ?? 0.0;

            $toppings      = [];
            $toppingsExtra = 0.0;
            foreach ($ci['toppings'] ?? [] as $t) {
                if (!isset($toppingPrices[$t['name']])) {
                    return back()->withInput()->withErrors(['cart_items' => 'One or more toppings are currently unavailable.']);
                }
                $price = (float) $toppingPrices[$t['name']];
                $toppingsExtra += $price;
                $toppings[] = ['name' => $t['name'], 'price' => $price];
            }

            $lineTotal  = ((float) $menuItem->base_price + $sizeExtra + $crustExtra + $toppingsExtra) * $qty;
            $subtotal  += $lineTotal;

            $orderItems[] = [
                'menu_item_id'        => $menuItem->id,
                'name'                => $menuItem->name,
                'qty'                 => $qty,
                'unit_price'          => (float) $menuItem->base_price,
                'size'                => $ci['size'] ?? null,
                'crust'               => $ci['crust'] ?? null,
                'size_extra'          => $sizeExtra,
                'crust_extra'         => $crustExtra,
                'added_toppings'      => $toppings,
                'removed_ingredients' => $ci['removedIngredients'] ?? [],
                'instructions'        => $ci['instructions'] ?? null,
                'line_total'          => $lineTotal,
            ];
        }

        // Apply promo atomically — lockForUpdate prevents concurrent overuse of max_uses
        $discountAmount = 0;
        $promoCode      = null;
        $promoModel     = null;

        if (!empty($data['promo_code'])) {
            DB::transaction(function () use (&$discountAmount, &$promoCode, &$promoModel, $data, $subtotal, $deliveryFee) {
                $promoModel = Promotion::where('code', strtoupper(trim($data['promo_code'])))
                    ->lockForUpdate()->first();
                if ($promoModel && $promoModel->isValid($subtotal)) {
                    $discountAmount = $promoModel->calculateDiscount($subtotal, $deliveryFee);
                    $promoCode      = $promoModel->code;
                    $promoModel->incrementUses();
                } else {
                    $promoModel = null;
                }
            });
        }

        $total = max(0, $subtotal + $deliveryFee - $discountAmount);

        $order = null;
        try {
            $order = Order::create([
                'user_id'           => $user->id,
                'type'              => $data['order_type'],
                'status'            => 'pending_payment',
                'subtotal'          => $subtotal,
                'delivery_fee'      => $deliveryFee,
                'total'             => $total,
                'promo_code'        => $promoCode,
                'discount_amount'   => $discountAmount,
                'customer_name'     => $user->name,
                'customer_email'    => $user->email,
                'delivery_address'  => $data['street_address'] ?? null,
                'delivery_city'     => $data['city'] ?? null,
                'delivery_postcode' => $data['postal_code'] ?? null,
                'notes'             => $data['notes'] ?? null,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            $stripe    = new StripeClient(config('services.stripe.secret'));
            $lineItems = [];

            foreach ($order->items as $item) {
                $summary = $item->customisation_summary;
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'gbp',
                        'unit_amount'  => (int) round($item->line_total / $item->qty * 100),
                        'product_data' => ['name' => $item->name . ($summary !== 'No extras' ? ' (' . $summary . ')' : '')],
                    ],
                    'quantity' => $item->qty,
                ];
            }

            if ($deliveryFee > 0) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'gbp',
                        'unit_amount'  => (int) ($deliveryFee * 100),
                        'product_data' => ['name' => 'Delivery Fee'],
                    ],
                    'quantity' => 1,
                ];
            }

            $sessionParams = [
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => route('orders.confirmation', $order->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('checkout'),
                'metadata'             => ['order_id' => $order->id],
            ];

            if ($discountAmount > 0) {
                $couponId = 'promo-' . strtolower($promoCode) . '-' . (int) round($discountAmount * 100);
                try {
                    $coupon = $stripe->coupons->retrieve($couponId);
                } catch (\Stripe\Exception\InvalidRequestException) {
                    $coupon = $stripe->coupons->create([
                        'id'         => $couponId,
                        'amount_off' => (int) round($discountAmount * 100),
                        'currency'   => 'gbp',
                        'duration'   => 'once',
                        'name'       => 'Promo: ' . $promoCode,
                    ]);
                }
                $sessionParams['discounts'] = [['coupon' => $coupon->id]];
            }

            $session = $stripe->checkout->sessions->create($sessionParams);

            $order->update(['stripe_session_id' => $session->id]);

            return redirect($session->url, 303);

        } catch (\Exception $e) {
            if ($order) $order->delete();
            if ($promoModel) $promoModel->decrement('current_uses');
            return back()->withInput()->withErrors(['cart_items' => 'Payment could not be initialised. Please try again.']);
        }
    }
}
