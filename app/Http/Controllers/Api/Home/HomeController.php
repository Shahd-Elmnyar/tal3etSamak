<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Api\AppController;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;

class HomeController  extends AppController
{
    public function index()
    {
        $trendMeals = Product::getTotalQuantities();
        $topDiscountedProducts = $this->getTopDiscountedProducts();
        $categories = $this->getCategories();

        $this->emptyResponse($trendMeals,  'home.no_trend_meals_found');
        $this->emptyResponse($topDiscountedProducts,  'home.no_discounted_products_found');
        $this->emptyResponse($categories,  'home.no_categories_found');
        // $a = CategoryResource::collection($categories);
        // dd($a);

        return $this->successResponse(
            'home.home_success',
            [
                'trend_meals' => ProductResource::collection($trendMeals),
                'top_discounted_products' => ProductResource::collection($topDiscountedProducts),
                'categories' => CategoryResource::collection($categories),
            ]
        );
    }


    private function getTopDiscountedProducts()
    {
        return Product::orderByDesc('discount')
            ->with('images', 'categories', 'favorites', 'sizes', 'additions')
            ->limit(3)
            ->get();
    }

    private function getCategories()
    {
        return Category::with(['parent', 'children', 'products'])->paginate(4);
    }
}
