<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PriceDropAlert;
use Illuminate\Http\Request;

class PriceDropAlertController extends Controller
{
    public function toggle(Request $request, Product $product)
    {
        if (!auth('web')->check()) {
            return response()->json(['redirect' => route('login')]);
        }

        $user    = auth('web')->user();
        $existing = PriceDropAlert::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['set' => false, 'message' => 'Alert removed.']);
        }

        PriceDropAlert::create([
            'user_id'      => $user->id,
            'product_id'   => $product->id,
            'target_price' => $request->target_price,
        ]);

        return response()->json(['set' => true, 'message' => 'Price drop alert set! We\'ll notify you when the price drops.']);
    }
}