<?php

namespace App\Http\Controllers\Api\Addition;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Addition;
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
            $sizeExists = $product->sizes()->where('sizes.id', $data['size_id'])->exists();
            if (!$sizeExists) {
                return response()->json([
                    'message' => "Size with ID {$data['size_id']} does not exist for this product.",
                ], 400);
            }

            // Initialize total addition price
            $totalAdditionPrice = 0;

            // Loop through each addition in the request data
            foreach ($data['additions'] as $addition) {
                // Check if the addition exists for the product
                $additionExists = $product->additions()->where('additions.id', $addition['addition_id'])->exists();
                if (!$additionExists) {
                    return response()->json([
                        'message' => "Addition with ID {$addition['addition_id']} does not exist for this product.",
                    ], 400);
                }

                // Calculate the total addition price
                $totalAdditionPrice += $product->additions()
                    ->where('additions.id', $addition['addition_id'])
                    ->first()
                    ->price * $addition['quantity'];
            }

            // Find or create the cart
            $cart = Cart::firstOrCreate(
                ['user_id' => $this->user->id],
                ['total_price' => 0]
            );

            // Create and save CartItem
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'price' => $product->price,
                'size_id' => $data['size_id'],
                'quantity' => array_sum(array_column($data['additions'], 'quantity')),
                'total' => array_sum(array_column($data['additions'], 'quantity')) * $product->price + $totalAdditionPrice,
                'total_addition_price' => $totalAdditionPrice
            ]);

            // Attach additions to the product
            foreach ($data['additions'] as $addition) {
                $this->user->productAdditions()->attach($productId, [
                    'addition_id' => $addition['addition_id'],
                    'quantity' => $addition['quantity'],
                ]);
            }

            // Update the cart's total price
            $cart->total_price += $cartItem->total;
            $cart->save();

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
