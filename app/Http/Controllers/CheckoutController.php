<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function index(): View
    {
        return view('checkout.index', ['title' => 'Checkout']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_type'     => 'required|in:delivery,collection',
            'cart_items'     => ['required', 'json', function ($attr, $val, $fail) {
                $items = json_decode($val, true);
                if (empty($items)) $fail('Your cart is empty.');
            }],
            'street_address' => 'required_if:order_type,delivery|nullable|string|max:255',
            'city'           => 'required_if:order_type,delivery|nullable|string|max:100',
            'postal_code'    => 'required_if:order_type,delivery|nullable|string|max:20',
        ]);

        $cartItems   = json_decode($data['cart_items'], true);
        $isDelivery  = $data['order_type'] === 'delivery';
        $deliveryFee = $isDelivery ? 3.50 : 0;
        $user        = Auth::user();

        $orderItems = [];
        $subtotal   = 0;

        foreach ($cartItems as $ci) {
            $lineTotal   = (float) ($ci['lineTotal'] ?? 0);
            $subtotal   += $lineTotal;
            $orderItems[] = [
                'name'                => $ci['name'],
                'qty'                 => max(1, (int) ($ci['qty'] ?? 1)),
                'unit_price'          => (float) ($ci['basePrice'] ?? 0),
                'size'                => $ci['size'] ?? null,
                'crust'               => $ci['crust'] ?? null,
                'size_extra'          => (float) ($ci['sizeExtra'] ?? 0),
                'crust_extra'         => (float) ($ci['crustExtra'] ?? 0),
                'added_toppings'      => $ci['toppings'] ?? [],
                'removed_ingredients' => $ci['removedIngredients'] ?? [],
                'instructions'        => $ci['instructions'] ?? null,
                'line_total'          => $lineTotal,
            ];
        }

        $total = $subtotal + $deliveryFee;

        $order = Order::create([
            'user_id'           => $user->id,
            'type'              => $data['order_type'],
            'status'            => 'pending_payment',
            'subtotal'          => $subtotal,
            'delivery_fee'      => $deliveryFee,
            'total'             => $total,
            'customer_name'     => $user->name,
            'customer_email'    => $user->email,
            'delivery_address'  => $data['street_address'] ?? null,
            'delivery_city'     => $data['city'] ?? null,
            'delivery_postcode' => $data['postal_code'] ?? null,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        try {
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

            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => route('orders.confirmation', $order->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('checkout'),
                'metadata'             => ['order_id' => $order->id],
            ]);

            $order->update(['stripe_session_id' => $session->id]);

            return redirect($session->url, 303);

        } catch (\Exception $e) {
            return redirect()->route('orders.confirmation', $order->id);
        }
    }
}
