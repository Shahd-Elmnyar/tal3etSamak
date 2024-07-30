<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Category;
use App\Traits\LocalizesContent;
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

        // Apply middleware to check authorization on every request
        $this->middleware(function ($request, $next) {
            $this->user = $this->checkAuthorization($request);
            // $this->setLocale();  // Set the locale here
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
        // Initialize total addition price
        $totalAdditionPrice = 0;

        // Loop through each addition in the request data
        foreach ($data['additions'] as $addition) {
            // Check if the addition exists for the product
            $additionExists = $product->additions()->where('additions.id', $addition['addition_id'])->exists();
            if (!$additionExists) {
                throw ValidationException::withMessages([
                    'additions' => "Addition with ID {$addition['addition_id']} does not exist for this product."
                ]);
            }

            // Calculate the total addition price
            $totalAdditionPrice += $product->additions()
                ->where('additions.id', $addition['addition_id'])
                ->first()
                ->price * $addition['quantity'];
        }

        return $totalAdditionPrice;
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
}
