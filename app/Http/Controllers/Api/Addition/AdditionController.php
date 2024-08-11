<?php

namespace App\Http\Controllers\Api\Addition;

use Exception;
use App\Models\Cart;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\AdditionResource;
use App\Http\Controllers\Api\AppController;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdditionController extends AppController
{
    public function index($productId)
    {
        try {
            $product = $this->getProductById($productId);

            $additions = $product->additions()->paginate(5);
            return $this->successResponse(
                'null',
                [
                    'additions' => AdditionResource::collection($additions),
                ]
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.product_not_found');
        } catch (Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
    public function addAdditionToCart(Request $request, $productId)
    {
        try {

            $data = $request->validate([
                'size_id' => 'required|exists:sizes,id',
                'additions' => 'required|array|min:1',
                'additions.*.addition_id' => 'required|exists:additions,id',
                'additions.*.quantity' => 'required|integer|min:1',
            ]);

            // Find the product
            $product = $this->getProductById($productId);

            // Check if the size exists for the product
            $this->productSizeCheck($product, $data);

            //Check if the addition exists for the product and  Calculate the total addition price
            $totalAdditionPrice = $this->totalAddition($product, $data);

            $cart = Cart::firstOrCreate();

            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'price' => $product->price,
                'size_id' => $data['size_id'],
                'addition_quantity' => array_sum(array_column($data['additions'], 'quantity')),
                'total' => $product->price + $totalAdditionPrice,
                'total_addition_price' => $totalAdditionPrice
            ]);

            $this->attachProductAdditions($productId, $data);

            // Update the cart's total price
            $this->updateTotalCartPrice($cart, $cartItem);

            return $this->successResponse(
                'home.add_product_to_cart_success',
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.product_not_found');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse(['errors' => $e->errors()]);
        } catch (Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
}
