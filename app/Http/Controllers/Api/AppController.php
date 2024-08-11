<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\MainController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;

class AppController extends MainController
{
    use AuthorizesRequests, ValidatesRequests;
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = $this->checkAuthorization($request);
            return $next($request);
        });
    }
    public function getProducts()
    {
        return Product::with(['images', 'sizes', 'additions'])->paginate(6);
    }
    public function productSizeCheck($product,$data){
        $sizeExists = $product->sizes()->where('sizes.id', $data['size_id'])->exists();
        if (!$sizeExists) {
            return response()->json([
                'message' => "Size with ID {$data['size_id']} does not exist for this product.",
            ], 400);
        }
    }


    public function totalAddition($product, $data)
    {
        $totalAdditionPrice = 0.0;
        foreach ($data['additions'] as $addition) {
            $additionExists = $product->additions()->where('additions.id', $addition['addition_id'])->exists();
            if (!$additionExists) {
                throw ValidationException::withMessages([
                    'additions' => __('home.not_addition_for_this_product'),
                ]);
            }
            // Calculate the total addition price
            $totalAdditionPrice += $product->additions()
                ->where('additions.id', $addition['addition_id'])
                ->first()
                ->price * $addition['quantity'];
        }

        return $totalAdditionPrice; // Should be a float
    }


    public function findOrCreateCart(){
        // Find or create the cart
        $cart = Cart::firstOrCreate(
            ['user_id' => $this->user->id],
            ['total_price' => 0]
        );
        return $cart;
    }
    public function attachProductAdditions($productId, $data){
        foreach ($data['additions'] as $addition) {
            $this->user->productAdditions()->attach($productId, [
                'addition_id' => $addition['addition_id'],
                'quantity' => $addition['quantity'],
            ]);
        }
    }
    public function updateTotalCartPrice($cart, $cartItem)
    {
        // Ensure that $cart->total_price and $cartItem->total are numeric
        if (!is_numeric($cart->total_price) || !is_numeric($cartItem->total)) {
            Log::error('Non-numeric value encountered in updateTotalCartPrice', [
                'cart_total_price' => $cart->total_price,
                'cart_item_total' => $cartItem->total
            ]);
            throw new \Exception('Non-numeric value encountered in updateTotalCartPrice');
        }

        $cart->total_price += $cartItem->total;
        $cart->save();
    }
    public function productHasAddition($product, $additionId)
    {
        return $product->additions()->where('additions.id', $additionId)->exists();
    }
    public function getProductById($productId)
    {
        return Product::findOrFail($productId);
    }
}
