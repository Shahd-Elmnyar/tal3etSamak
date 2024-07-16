<?php

namespace App\Http\Controllers\Api\Home;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Home\HomeRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'code' => 'ERROR',
                'message' =>__('auth.user_not_auth') ,
            ], 401);
        }

        $trendMeals = $this->getTrendMeals($user->id);
        $topDiscountedProducts = $this->getTopDiscountedProducts();
        $categories = $this->getCategories();

        if ($categories->isEmpty()) {
            return response()->json([
                'code' => 'ERROR',
                'message' => __('home.no_categories_found'),
            ], 404);
        }

        // Check if trend meals is empty
        if ($trendMeals->isEmpty()) {
            return response()->json([
                'code' => 'ERROR',
                'message' => __('home.no_trend_meals_found'),
            ], 404);
        }

        // Check if top discounted products is empty
        if ($topDiscountedProducts->isEmpty()) {
            return response()->json([
                'code' => 'ERROR',
                'message' => __('home.no_discounted_products_found'),
            ], 404);
        }

        return response()->json([
            'code' => 'SUCCESS',
            'data' => [
                'trendMeals' => ProductResource::collection($trendMeals),
                'categories' => CategoryResource::collection($categories),
                'topDiscounted' => ProductResource::collection($topDiscountedProducts),
            ]
        ]);
    }

    private function getTrendMeals($userId)
    {
        $trendMeals = Product::with(['favorites' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
            ->select('products.*', DB::raw('COUNT(order_items.id) as order_count'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy(
                'products.id',
                'products.name',
                'products.description',
                'products.img',
                'products.price',
                'products.offer_price',
                'products.discount_type',
                'products.discount',
                'products.offer',
                'products.sale',
                'products.active',
                'products.category_id',
                'products.size_id',
                'products.created_at',
                'products.updated_at'
            )
            ->orderByDesc('order_count')
            ->limit(2)
            ->get();

        if ($trendMeals->isEmpty()) {
            $trendMeals = Product::with(['favorites' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
                ->inRandomOrder()
                ->limit(3)
                ->get();
        }

        return $trendMeals;
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
