<?php

namespace App\Http\Controllers\Api\Home;

use Exception;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Api\AppController;

class HomeController extends AppController
{
    public function index()
    {
        try {
            $trendMeals = Product::getTotalQuantities();
            $topDiscountedProducts = $this->getTopDiscountedProducts();
            $categories = $this->getCategories();

            // Handle empty responses
            $this->emptyResponse($trendMeals, 'home.no_trend_meals_found');
            $this->emptyResponse($topDiscountedProducts, 'home.no_discounted_products_found');
            $this->emptyResponse($categories, 'home.no_categories_found');

            return $this->successResponse(
                __('home.home_success'),
                [
                    'trend_meals' => ProductResource::collection($trendMeals),
                    'top_discounted_products' => ProductResource::collection($topDiscountedProducts),
                    'categories' => CategoryResource::collection($categories),
                ]
            );
        } catch (Exception $e) {
            Log::error('HomeController error: ' . $e->getMessage());
            return $this->genericErrorResponse(__('errors.server_error'), ['error' => $e->getMessage()]);
        }
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
        return Category::with(['parent', 'children', 'products'])->get();
    }
}
