<?php

namespace App\Http\Controllers\Api\Category;

use Exception;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Api\AppController;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends AppController
{


    public function index(Request $request)
    {
        try {
            $mainCategories = $this->getMainCategories();
            $subCategories = $this->getSubCategories();
            // dd($subCategories);
            $products = $this->getProducts();

            return $this->successResponse(
                __('home.home_success'),
                [
                    'main_categories' => CategoryResource::collection($mainCategories),
                    'main_pagination' => $this->getPaginationData($mainCategories),
                    'sub_categories' => CategoryResource::collection($subCategories),
                    'sub_pagination' => $this->getPaginationData($subCategories),
                    'products' => ProductResource::collection($products),
                    'products_pagination' => $this->getPaginationData($products),
                ]
            );
        } catch (Exception $e) {
            return $this->genericErrorResponse(__('auth.error_occurred'), ['error' => $e->getMessage()]);
        }
    }

    public function show($id, $subCategoryId = null)
    {
        try {
            $category = $this->getCategoryWithRelations($id);

            if ($subCategoryId) {
                $subCategory = $this->getSubCategoryWithRelations($category, $subCategoryId);

                return $this->successResponse(
                    __('home.home_success'),
                    ['category' => new CategoryResource($subCategory)]
                );
            }

            $products = $this->getProductsForCategory($category);

            return $this->successResponse(
                __('home.home_success'),
                ['category' => new CategoryResource($category->setRelation('products', $products))]
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(__('home.category_not_found'));
        } catch (Exception $e) {
            Log::error('HomeController error: ' . $e->getMessage());
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }

    private function getMainCategories()
    {
        return Category::with(['children'])
            ->whereDoesntHave('parent')
            ->get();
    }

    private function getSubCategories()
    {
        return Category::with(['parent'])
            ->whereHas('parent')
            ->get();
    }

    private function getCategoryWithRelations($id)
    {
        return Category::with(['parent', 'children', 'products'])->findOrFail($id);
    }

    private function getSubCategoryWithRelations($category, $subCategoryId)
    {
        return $category->children()->with(['products.images', 'products.sizes', 'products.additions'])
            ->findOrFail($subCategoryId);
    }

    private function getProductsForCategory($category)
    {
        $childCategoryIds = $category->children->pluck('id');
        $allProductIds = $category->products->pluck('id')
            ->merge(
                Product::whereIn('id', function ($query) use ($childCategoryIds) {
                    $query->select('product_id')
                        ->from('product_category')
                        ->whereIn('category_id', $childCategoryIds);
                })->pluck('id')
            )->unique();

        return Product::whereIn('id', $allProductIds)
            ->with(['images', 'sizes', 'additions'])
            ->paginate(6);
    }
}
