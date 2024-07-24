<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Api\AppController;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends AppController
{
    public function index(Request $request)
    {

        $products = Product::with('images', 'sizes', 'additions')->paginate(6);

        return $this->successResponse(
            'home.home_success',
            [
                'products' => ProductResource::collection($products),
            ]
        );
    }

    public function show(Request $request, $id)
    {


        try {
            $product = Product::with('images', 'categories', 'sizes', 'additions')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('home.product_not_found');
        }

        return $this->successResponse(
            'home.home_success',
            [
                'product' => new ProductResource($product),
            ]
        );
    }
}
