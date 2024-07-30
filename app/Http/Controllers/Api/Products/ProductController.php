<?php

namespace App\Http\Controllers\Api\Products;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Api\AppController;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends AppController
{
    public function index()
    {
        $products = $this->getProducts();


        return $this->successResponse(
            'home.home_success',
            [
                'products' => ProductResource::collection($products),
                'pagination' => $this->getPaginationData($products),
            ]
        );
    }

    public function show($id)
    {
        try {
            $product = Product::with('images', 'categories', 'sizes', 'additions')->findOrFail($id);


            return $this->successResponse(
                'home.home_success',
                [
                    'product' => new ProductResource($product),
                    'pagination' => $this->getPaginationData($product),
                ]
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.product_not_found');
        }
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'search' => 'required|string|max:255',
        ]);

        $filters = ['search' => $validated['search']];
        $products = Product::filter($filters)
            ->with(['images', 'sizes', 'additions'])
            ->paginate(6);

        if ($products->isEmpty()) {
            return $this->notFoundResponse('home.product_not_found');
        }

        // Add null check here
        $productResources = $products->isNotEmpty() ? ProductResource::collection($products) : null;

        return $this->successResponse(
            'home.home_success',
            [
                'products' => $productResources,
                'pagination' => $this->getPaginationData($products),
            ]
        );
    }


    public function filter(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
        ]);

        $filters = [
            'search' => $validated['search'] ?? null,
            'price_min' => $validated['price_min'] ?? null,
            'price_max' => $validated['price_max'] ?? null,
        ];


        $products = Product::filter($filters)
            ->with(['images', 'sizes', 'additions'])
            ->paginate(6);

        if ($products->isEmpty()) {
            return $this->notFoundResponse('home.product_not_found');
        }

        // Add null check here
        $productResources = $products->isNotEmpty() ? ProductResource::collection($products) : null;

        return $this->successResponse(
            'home.home_success',
            [
                'products' => $productResources,
                'pagination' => $this->getPaginationData($products),
            ]
        );
    }
    public function addProductToCart(Request $request, $productId)
    {
        try {
            // Validate request data
            $data = $request->validate([
                'size_id' => 'required|exists:sizes,id',
                'additions' => 'required|array|min:1',
                'additions.*.addition_id' => 'required|exists:additions,id',
                'additions.*.quantity' => 'required|integer|min:1',
                'quantity' => 'required|integer|min:1',
            ]);

            // Find the product
            $product = $this->getProductById($productId);

            // Check if the size exists for the product
            $this->productSizeCheck($product, $data);

            // Calculate the total addition price
            $totalAdditionPrice = $this->totalAddition($product, $data);

            $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

            // Create and save CartItem
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'price' => $product->price,
                'size_id' => $data['size_id'],
                'quantity' => $data['quantity'],
                'addition_quantity' => array_sum(array_column($data['additions'], 'quantity')),
                'total' => ($data['quantity'] * ($product->price + $totalAdditionPrice)),
                'total_addition_price' => $totalAdditionPrice
            ]);

            // Attach additions to the product
            $this->attachProductAdditions($productId, $data);

            // Update the cart's total price
            $this->updateTotalCartPrice($cart, $cartItem);

            return $this->successResponse(
                'home.add_product_to_cart_success'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.product_not_found');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse((object)['errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('General error: ', ['error' => $e->getMessage()]);
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }
    public function addMultipleProductsToCart(Request $request)
    {
        try {
            // Log the incoming request data
            Log::info('Request data: ', $request->all());

            // Validate request data
            $data = $request->validate([
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|exists:products,id',
            ]);

            $cart = $this->findOrCreateCart();

            foreach ($data['products'] as $productData) {
                Log::info('Processing product: ', $productData);

                $product = Product::findOrFail($productData['product_id']);

                // Create and save CartItem
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'total' => $product->price,
                ]);

                // Update the cart's total price
                $this->updateTotalCartPrice($cart, $cartItem);
            }

            return $this->successResponse(
                'home.add_product_to_cart_success'
            );
        } catch (ModelNotFoundException $e) {
            Log::error('Product not found: ', ['error' => $e->getMessage()]);
            return $this->notFoundResponse('home.product_not_found');
        } catch (ValidationException $e) {
            Log::error('Validation error: ', ['errors' => $e->errors()]);
            return $this->validationErrorResponse((object)['errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('General error: ', ['error' => $e->getMessage()]);
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }
}
