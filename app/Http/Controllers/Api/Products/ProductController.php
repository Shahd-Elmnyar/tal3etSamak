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
            null,
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
                null,
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

        $productResources = $products->isNotEmpty() ? ProductResource::collection($products) : null;

        return $this->successResponse(
            null,
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

        $productResources = $products->isNotEmpty() ? ProductResource::collection($products) : null;

        return $this->successResponse(
            null,
            [
                'products' => $productResources,
                'pagination' => $this->getPaginationData($products),
            ]
        );
    }






    public function addProductToCart(Request $request, $productId)
    {
        try {
            $data = $request->validate([
                'size_id' => 'required|exists:sizes,id',
                'additions' => 'required|array|min:1',
                'additions.*.addition_id' => 'required|exists:additions,id',
                'additions.*.quantity' => 'required|integer|min:1',
                'quantity' => 'required|integer|min:1',
            ]);

            $product = $this->getProductById($productId);

            $this->productSizeCheck($product, $data);

            $this->validateProductAdditions($product, $data);

            $totalAdditionPrice = $this->totalAddition($product, $data);

            $cart = $this->createCart($request);

            $cartItem = $this->createCartItem($product, $data, $cart, $totalAdditionPrice, $productId);

            $this->attachProductAdditions($productId, $data);

            $this->updateTotalCartPrice($cart, $cartItem);

            return $this->successResponse(
                'home.add_product_to_cart_success'
            );

        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.product_not_found');

        } catch (ValidationException $e) {

            Log::error('Validation errors: ', ['errors' => $e->errors()]);
            return $this->validationErrorResponse(['errors' => $e->errors()]);

        } catch (\Exception $e) {

            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }



    protected function validateProductAdditions($product, $data)
    {
        foreach ($data['additions'] as $addition) {
            if (!$this->productHasAddition($product, $addition['addition_id'])) {
                return $this->validationErrorResponse('home.not_addition_for_this_product');
            }
        }
    }



    protected function createCart($request){
        return Cart::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['total_price' => 0]
        );
    }


    protected function createCartItem($product, $data, $cart, $totalAdditionPrice,$productId){
        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $productId,
            'price' => $product->price,
            'size_id' => $data['size_id'],
            'quantity' => $data['quantity'],
            'addition_quantity' => array_sum(array_column($data['additions'], 'quantity')),
            'total' => ($data['quantity'] * ($product->price + $totalAdditionPrice)),
            'total_addition_price' => $totalAdditionPrice
        ]);
    }






    public function addMultipleProductsToCart(Request $request)
    {
        try {

            Log::info('Request data: ', $request->all());


            $data = $request->validate([
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|exists:products,id',
            ]);

            $cart = $this->findOrCreateCart();

            $this->addMultipleToCartItem($data, $cart);

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

            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();

        }
    }


    protected function addMultipleToCartItem($data, $cart){
        foreach ($data['products'] as $productData) {
            Log::info('Processing product: ', $productData);

            $product = Product::findOrFail($productData['product_id']);

            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'total' => $product->price,
            ]);

            $this->updateTotalCartPrice($cart, $cartItem);
        }
    }
}
