<?php

namespace App\Http\Controllers\Api\Favorite;

use App\Http\Controllers\Api\AppController;
use Exception;
use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;

class FavoriteController extends AppController
{
    public function index()
    {
        try {
            $favoriteProducts = $this->getUserFavoriteProducts($this->user->id);
            if ($favoriteProducts->isEmpty()) {
                return $this->notFoundResponse('NO_FAVORITE_PRODUCTS');
            }

            return $this->successResponse(
                null,
                ProductResource::collection($favoriteProducts)
            );
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    private function getUserFavoriteProducts($userId)
    {
        return Product::whereHas('favorites', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    }

    public function store(Request $request)
    {
        try {
            $productExists = Product::where('id', $request->product_id)->exists();
            if (!$productExists) {
                return $this->notFoundResponse('home.product_not_found');
            }

            // Check if the product is already in favorites
            $alreadyFavorite = $this->getProductFavorite($this->user->id, $request->product_id);
            if ($alreadyFavorite) {
                return $this->validationErrorResponse('home.PRODUCT_ALREADY_FAVORITE');
            }

            $favorite = Favorite::firstOrCreate([
                'user_id' => $this->user->id,
                'product_id' => $request->product_id,
            ]);

            return $this->successResponse(
                'home.favorite_success',
                [new ProductResource($favorite->product)]
            );
        } catch (Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }

    private function getProductFavorite($userId, $productId)
    {
        return Favorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function destroy($id)
    {
        try {

            $favorite = $this->getProductFavorite($this->user->id, $id);

            if ($favorite) {
                $favorite->delete();
                return $this->successResponse('home.PRODUCT_REMOVED');
            } else {
                return $this->notFoundResponse('home.PRODUCT_NOT_FAVORITE');
            }
        } catch (Exception $e) {
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
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
            Log::error('General error : ' . $e->getMessage());
            return $this->genericErrorResponse();
        }
    }
}
