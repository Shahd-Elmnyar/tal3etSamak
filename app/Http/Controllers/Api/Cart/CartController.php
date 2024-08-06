<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Api\AppController;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends AppController
{
    public function showCart(Request $request)
    {
        $cart = Cart::where('user_id', $this->user->id)->with('cartItems.product')->first();

        if (!$cart) {
            return response()->json([
                'cart' => [],
                'totalItems' => 0,
                'totalPrice' => 0.00
            ]);
        }

        $totalItems = $cart->cartItems->sum('quantity');
        $totalPrice = $cart->total_price;


        return response()->json([
            'cart' => $cart->cartItems,
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice
        ]);
    }
}
