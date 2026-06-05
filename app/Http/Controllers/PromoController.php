<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'code'     => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $promo = Promotion::where('code', strtoupper(trim($request->code)))->first();

        if (!$promo || !$promo->isValid((float) $request->subtotal)) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired promo code.']);
        }

        $discount = $promo->calculateDiscount((float) $request->subtotal, (float) $request->input('delivery_fee', 0));

        return response()->json([
            'valid'    => true,
            'code'     => $promo->code,
            'label'    => $promo->type_label,
            'discount' => $discount,
            'message'  => "{$promo->type_label} applied — saving £" . number_format($discount, 2),
        ]);
    }
}
