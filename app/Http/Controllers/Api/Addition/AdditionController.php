<?php

namespace App\Http\Controllers\Api\Addition;

use App\Models\Cart;
use App\Models\CartItem;

use Illuminate\Http\Request;
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

            $additions =$product->additions()->paginate(5);
            return $this->successResponse(
                'home.home_success',
                [
                    'additions' => AdditionResource::collection($additions),
                ]
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.product_not_found');
        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }
    public function addAdditionToCart(Request $request, $productId)
    {
        try {
            // Validate request data
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

            // Calculate the total addition price
            $totalAdditionPrice = $this->totalAddition($product, $data);

            $cart = Cart::firstOrCreate();


            // Create and save CartItem
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'price' => $product->price,
                'size_id' => $data['size_id'],
                'addition_quantity' => array_sum(array_column($data['additions'], 'quantity')),
                'total' => $product->price + $totalAdditionPrice,
                'total_addition_price' => $totalAdditionPrice
            ]);

            // Attach additions to the product
            $this->attachProductAdditions($productId, $data);

            // Update the cart's total price
            $this->updateTotalCartPrice($cart ,$cartItem);

            return $this->successResponse(
                'home.add_product_to_cart_success',
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.product_not_found');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse((object)['errors' => $e->errors()]);
        } catch (\Exception $e) {
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }
}
