<?php

namespace App\Http\Controllers\Api\Cart;

use Exception;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\AppController;
use App\Http\Resources\CartItemResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends AppController
{
    public function showCart(): JsonResponse
    {
        try {

            $cart = Cart::where('user_id', $this->user->id)
                ->with('cartItems.product')
                ->firstOrFail();

            $totalItems = $cart->cartItems->sum('quantity');
            $totalPrice = $cart->total_price;

            return $this->cartResponse($cart->cartItems, $totalItems, $totalPrice);
        } catch (ModelNotFoundException $e) {
            return $this->emptyCartResponse();
        } catch (Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    protected function emptyCartResponse(): JsonResponse
    {
        return $this->successResponse(
            null,
            [
                'cart' => [],
                'totalItems' => 0,
                'totalPrice' => number_format(0.00, 2)
            ]
        );
    }

    protected function cartResponse($cartItems, int $totalItems, float $totalPrice): JsonResponse
    {
        return $this->successResponse(
            null,
            [
                'cart' => CartItemResource::collection($cartItems),
                'totalItems' => $totalItems,
                'totalPrice' => number_format($totalPrice, 2)
            ]
        );
    }
}
