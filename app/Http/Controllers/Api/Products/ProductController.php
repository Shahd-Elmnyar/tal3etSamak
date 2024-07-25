<?php

namespace App\Http\Controllers\Api\Products;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;

use App\Http\Controllers\Api\AppController;
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
            ]
        );
    }

    public function show($id)
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


    return $this->successResponse(
        'home.home_success',
        [
            'products' => ProductResource::collection($products),
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

    return $this->successResponse(
        'home.home_success',
        [
            'products' => ProductResource::collection($products),
        ]
    );
}
}

