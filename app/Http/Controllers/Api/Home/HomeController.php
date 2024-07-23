<?php

namespace App\Http\Controllers\Api\Home;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\MainController;
use App\Http\Requests\Home\HomeRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;

class HomeController  extends MainController
{
    public function index(Request $request)
    {
        $user = $this->checkAuthorization($request);

        $trendMeals = Product::getTotalQuantities();
        $topDiscountedProducts = $this->getTopDiscountedProducts();
        $categories = $this->getCategories();

        $this->emptyResponse($trendMeals, __('home.no_trend_meals_found'));
        $this->emptyResponse($topDiscountedProducts, __('home.no_discounted_products_found'));
        $this->emptyResponse($categories, __('home.no_categories_found'));

        return $this->successResponse(
            __('home.home_success'),
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
            ->limit(3)
            ->get();
    }

    private function getCategories()
    {
        return Category::paginate(4);
    }
}
