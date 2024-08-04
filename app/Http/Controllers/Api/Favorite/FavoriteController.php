<?php

namespace App\Http\Controllers\Api\Favorite;

use App\Http\Controllers\Api\AppController;
use Exception;
use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class FavoriteController extends AppController
{
    public function index(Request $request)
    {
        try {
            $favoriteProducts = $this->getUserFavoriteProducts($this->user->id);
            if ($favoriteProducts->isEmpty()) {
                return $this->notFoundResponse('NO_FAVORITE_PRODUCTS');
            }

            return $this->successResponse(
                'home.home_success',[
                ProductResource::collection($favoriteProducts)
            ]);
        } catch (Exception $e) {
            Log::error('Error during forget password process: ' . $e->getMessage());

            return $this->genericErrorResponse(__('errors.server_error'), ['error' => $e->getMessage()]);

        }
    }

    private function getUserFavoriteProducts($userId)
    {
        return Favorite::with('product')
            ->where('user_id', $userId)
            ->get()
            ->pluck('product');
    }

    public function store(Request $request)
    {
        try {
            $productExists = Product::where('id', $request->product_id)->exists();
            if (!$productExists) {
                return $this->notFoundResponse('home.product_not_found');
            }

            // Check if the product is already in favorites
            $alreadyFavorited = Favorite::where('user_id', $this->user->id)
                ->where('product_id', $request->product_id)
                ->exists();
            if ($alreadyFavorited) {
                return response()->json([
                    'code' => 'ERROR',
                    'data' => __('home.PRODUCT_ALREADY_FAVORITED'),
                ], 422);
            }

            $favorite = Favorite::firstOrCreate([
                'user_id' => $this->user->id,
                'product_id' => $request->product_id,
            ]);

            return $this->successResponse(
                'home.favorite_success',
                [new ProductResource($favorite->product)]);
        } catch (Exception $e) {
            return $this->genericErrorResponse(__('errors.server_error'), ['error' => $e->getMessage()]);

        }
    }

    public function destroy( $id)
    {
        try {

            $favorite = Favorite::where('user_id', $this->user->id)
                ->where('product_id', $id)
                ->first();

            if ($favorite) {
                $favorite->delete();
                return $this->successResponse('home.PRODUCT_REMOVED');
            } else {
                return $this->notFoundResponse('home.PRODUCT_NOT_FAVORITED');
            }
        } catch (Exception $e) {
            return $this->genericErrorResponse(__('errors.server_error'), ['error' => $e->getMessage()]);

        }
    }
    public function destroyAll()
    {
        try {
            $deleted = Favorite::where('user_id', $this->user->id)->delete();

            if ($deleted) {
                return $this->successResponse('home.ALL_FAVORITES_REMOVED');
            } else {
                return $this->notFoundResponse('home.NO_FAVORITES_TO_REMOVE');
            }
        } catch (Exception $e) {
            return $this->genericErrorResponse(__('errors.server_error'), ['error' => $e->getMessage()]);
        }
    }
}
